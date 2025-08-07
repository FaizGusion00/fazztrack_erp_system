<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobController extends Controller
{
    /**
     * Display a listing of jobs
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Only SuperAdmin and Sales Manager can view jobs
        if (!$user->isSuperAdmin() && !$user->isSalesManager()) {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have permission to view jobs.');
        }
        
        $query = Job::with(['order.client', 'assignedUser']);
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('job_id', 'like', "%{$search}%")
                  ->orWhere('phase', 'like', "%{$search}%")
                  ->orWhereHas('order', function($orderQuery) use ($search) {
                      $orderQuery->where('job_name', 'like', "%{$search}%")
                                ->orWhere('order_id', 'like', "%{$search}%");
                  })
                  ->orWhereHas('order.client', function($clientQuery) use ($search) {
                      $clientQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by phase
        if ($request->filled('phase')) {
            $query->where('phase', $request->phase);
        }
        
        $jobs = $query->paginate(15)->withQueryString();
        return view('jobs.index', compact('jobs'));
    }

    /**
     * Show QR scanner for production staff
     */
    public function scanner()
    {
        return view('jobs.scanner');
    }

    /**
     * Scan QR code and show job details
     */
    public function scanQr(Request $request)
    {
        $request->validate([
            'qr_data' => 'required|string',
        ]);

        try {
            $qrData = $request->qr_data;
            $jobId = null;

            // Try to parse as JSON first (for complex QR codes)
            try {
                $qrDataObj = json_decode($qrData, true);
                if ($qrDataObj && isset($qrDataObj['job_id'])) {
                    $jobId = $qrDataObj['job_id'];
                }
            } catch (\Exception $e) {
                // Not JSON, try other formats
            }

            // If not JSON, try different QR code formats
            if (!$jobId) {
                if (preg_match('/QR_([A-Za-z0-9]+)_([A-Z]+)/', $qrData, $matches)) {
                    // Format: QR_EVLrykvkjc_PRINT (QR_code_phase)
                    $jobId = $matches[1];
                } elseif (preg_match('/QR_(\d+)/', $qrData, $matches)) {
                    // Format: QR_123
                    $jobId = $matches[1];
                } elseif (preg_match('/JOB_(\d+)/', $qrData, $matches)) {
                    // Format: JOB_123
                    $jobId = $matches[1];
                } elseif (preg_match('/^(\d+)$/', $qrData, $matches)) {
                    // Direct job ID number
                    $jobId = $matches[1];
                } else {
                    // Try to extract any number as job ID
                    if (preg_match('/(\d+)/', $qrData, $matches)) {
                        $jobId = $matches[1];
                    }
                }
            }

            if (!$jobId) {
                return response()->json(['error' => 'Invalid QR code format'], 400);
            }

            // For direct job IDs, allow numeric values
            if (!preg_match('/^[A-Za-z0-9]+$/', $jobId)) {
                return response()->json(['error' => 'Invalid job ID format'], 400);
            }

            $job = Job::with(['order.client', 'assignedUser'])
                ->findOrFail($jobId);

            // Check if user can access this job
            $user = Auth::user();
            if ($user->isProductionStaff() && $job->assigned_user_id !== $user->id) {
                return response()->json(['error' => 'Access denied'], 403);
            }

            return response()->json([
                'job' => $job,
                'can_start' => $job->status === 'Pending',
                'can_end' => $job->status === 'In Progress',
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Job not found'], 404);
        }
    }

    /**
     * Start a job (capture start time)
     */
    public function startJob(Request $request, Job $job)
    {
        $user = Auth::user();
        
        // Check permissions - Production staff can access jobs matching their phase or unassigned jobs
        if ($user->isProductionStaff()) {
            // Check if job phase matches user's phase
            if ($job->phase !== $user->phase) {
                return response()->json([
                    'error' => 'Access denied. This job phase does not match your assigned phase.',
                    'job_phase' => $job->phase,
                    'user_phase' => $user->phase
                ], 403);
            }
            
            // Check if job is assigned to someone else
            if ($job->assigned_user_id && $job->assigned_user_id !== $user->id) {
                return response()->json([
                    'error' => 'Access denied. This job is assigned to another user.',
                    'assigned_user_id' => $job->assigned_user_id,
                    'current_user_id' => $user->id
                ], 403);
            }
        }

        if ($job->status !== 'Pending') {
            return response()->json(['error' => 'Job cannot be started'], 400);
        }

        // STRICT WORKFLOW: Check if previous job is completed
        $previousJob = $this->getPreviousJob($job);
        if ($previousJob && $previousJob->status !== 'Completed') {
            return response()->json([
                'error' => 'Previous job must be completed first.',
                'previous_job' => $previousJob->phase,
                'previous_status' => $previousJob->status,
                'message' => "Please complete {$previousJob->phase} phase first."
            ], 400);
        }

        $request->validate([
            'start_quantity' => 'nullable|integer|min:1',
        ]);

        // Start the job with timestamp
        $job->startJob($request->start_quantity);

        // Assign user if not already assigned
        if (!$job->assigned_user_id) {
            $job->update(['assigned_user_id' => $user->id]);
        }

        // Update order status to Job Start if this is the first job started
        $order = $job->order;
        $allJobs = $order->jobs;
        $inProgressJobs = $allJobs->where('status', 'In Progress')->count();
        $completedJobs = $allJobs->where('status', 'Completed')->count();
        $totalJobs = $allJobs->count();
        
        // Enhanced order status update for job start
        if ($order->status === 'Job Created' || $order->status === 'Design Approved') {
            $order->update(['status' => 'Job Start']);
        } elseif ($inProgressJobs > 0 && $completedJobs === 0) {
            // First job started - update to Job Start
            $order->update(['status' => 'Job Start']);
        }

        return response()->json([
            'message' => 'Job started successfully',
            'job' => $job->fresh(),
            'start_time' => $job->start_time->toISOString(),
            'next_job' => $this->getNextJob($job),
            'order_status' => $order->fresh()->status,
            'in_progress_jobs' => $inProgressJobs,
            'completed_jobs' => $completedJobs,
            'total_jobs' => $totalJobs
        ]);
    }

    /**
     * End a job (capture end time and calculate duration)
     */
    public function endJob(Request $request, Job $job)
    {
        $user = Auth::user();
        
        // Check permissions - Production staff can access jobs matching their phase or unassigned jobs
        if ($user->isProductionStaff()) {
            // Check if job phase matches user's phase
            if ($job->phase !== $user->phase) {
                return response()->json([
                    'error' => 'Access denied. This job phase does not match your assigned phase.',
                    'job_phase' => $job->phase,
                    'user_phase' => $user->phase
                ], 403);
            }
            
            // Check if job is assigned to someone else
            if ($job->assigned_user_id && $job->assigned_user_id !== $user->id) {
                return response()->json([
                    'error' => 'Access denied. This job is assigned to another user.',
                    'assigned_user_id' => $job->assigned_user_id,
                    'current_user_id' => $user->id
                ], 403);
            }
        }

        if ($job->status !== 'In Progress') {
            return response()->json(['error' => 'Job cannot be ended'], 400);
        }

        $request->validate([
            'end_quantity' => 'nullable|integer|min:0',
            'reject_quantity' => 'nullable|integer|min:0',
            'reject_status' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        // End the job with timestamp and calculate duration
        $job->endJob(
            $request->end_quantity,
            $request->reject_quantity,
            $request->reject_status,
            $request->remarks
        );

        // Get the duration from the model (already calculated)
        $duration = $job->fresh()->duration;

        // Update order status based on job completion
        $order = $job->order;
        $allJobs = $order->jobs;
        $completedJobs = $allJobs->where('status', 'Completed')->count();
        $totalJobs = $allJobs->count();
        
        // Enhanced order status update logic
        if ($completedJobs === $totalJobs && $totalJobs > 0) {
            // All jobs completed - Order Finished
            $order->update(['status' => 'Order Finished']);
            
            // Automatically set delivery status based on delivery method
            if ($order->delivery_method === 'Self Collect') {
                $order->update(['delivery_status' => 'Pending']);
            } else {
                $order->update(['delivery_status' => 'Pending']);
            }
        } elseif ($job->phase === 'QC' && $job->status === 'Completed') {
            // QC phase completed - Job Complete
            $order->update(['status' => 'Job Complete']);
        } elseif ($job->phase === 'IRON/PACKING' && $job->status === 'In Progress') {
            // IRON/PACKING started - Order Packaging
            $order->update(['status' => 'Order Packaging']);
        } elseif ($job->phase === 'IRON/PACKING' && $job->status === 'Completed') {
            // IRON/PACKING completed - Order Finished
            $order->update(['status' => 'Order Finished']);
            
            // Automatically set delivery status based on delivery method
            if ($order->delivery_method === 'Self Collect') {
                $order->update(['delivery_status' => 'Pending']);
            } else {
                $order->update(['delivery_status' => 'Pending']);
            }
        } elseif ($completedJobs > 0 && $completedJobs < $totalJobs) {
            // Some jobs completed but not all - keep current status or update to Job Start
            if ($order->status === 'Job Created' || $order->status === 'Design Approved') {
                $order->update(['status' => 'Job Start']);
            }
        }

        return response()->json([
            'message' => 'Job completed successfully',
            'job' => $job->fresh(),
            'end_time' => $job->end_time->toISOString(),
            'duration' => $duration,
            'duration_formatted' => $this->formatDuration($duration),
            'next_job' => $this->getNextJob($job),
            'order_status' => $order->fresh()->status,
            'completed_jobs' => $completedJobs,
            'total_jobs' => $totalJobs
        ]);
    }

    /**
     * Show job details
     */
    public function show(Job $job)
    {
        $job->load(['order.client', 'assignedUser']);
        return view('jobs.show', compact('job'));
    }

    /**
     * Show the form for creating a new job
     */
    public function create()
    {
        $orders = \App\Models\Order::where('status', 'Approved')->get();
        return view('jobs.create', compact('orders'));
    }

    /**
     * Store a newly created job
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,order_id',
            'phase' => 'required|in:PRINT,PRESS,CUT,SEW,QC,IRON/PACKING',
            'start_quantity' => 'required|integer|min:1',
            'remarks' => 'nullable|string',
        ]);

        $job = Job::create([
            'order_id' => $request->order_id,
            'phase' => $request->phase,
            'status' => 'Pending',
            'start_quantity' => $request->start_quantity,
            'remarks' => $request->remarks,
            'qr_code' => 'QR_' . Str::random(10) . '_' . $request->phase,
        ]);

        return redirect()->route('jobs.show', $job)
            ->with('success', 'Job created successfully.');
    }

    /**
     * Show the form for editing the specified job
     */
    public function edit(Job $job)
    {
        // Check if user has permission to edit this job
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isSuperAdmin()) {
            return redirect()->route('jobs.index')
                ->with('error', 'You do not have permission to edit jobs.');
        }

        $orders = \App\Models\Order::where('status', '!=', 'Order Finished')->get();
        $users = User::where('role', 'Production Staff')->get();
        
        return view('jobs.edit', compact('job', 'orders', 'users'));
    }

    /**
     * Update the specified job
     */
    public function update(Request $request, Job $job)
    {
        // Check if user has permission to edit this job
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isSuperAdmin()) {
            return redirect()->route('jobs.index')
                ->with('error', 'You do not have permission to edit jobs.');
        }

        $request->validate([
            'order_id' => 'required|exists:orders,order_id',
            'phase' => 'required|in:PRINT,PRESS,CUT,SEW,QC,IRON/PACKING',
            'status' => 'required|in:Pending,In Progress,Completed,On Hold',
            'start_quantity' => 'nullable|integer|min:1',
            'end_quantity' => 'nullable|integer|min:0',
            'reject_quantity' => 'nullable|integer|min:0',
            'reject_status' => 'nullable|string|max:255',
            'assigned_user_id' => 'nullable|exists:users,id',
            'remarks' => 'nullable|string|max:1000',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
        ]);

        // Additional validation for quantities
        if ($request->filled('start_quantity') && $request->filled('end_quantity')) {
            if ($request->end_quantity > $request->start_quantity) {
                return back()->withErrors(['end_quantity' => 'End quantity cannot be greater than start quantity.'])->withInput();
            }
        }

        // Check if assigned user can handle this phase
        if ($request->filled('assigned_user_id')) {
            $assignedUser = User::find($request->assigned_user_id);
            if ($assignedUser && $assignedUser->role === 'Production Staff' && $assignedUser->phase !== $request->phase) {
                return back()->withErrors(['assigned_user_id' => 'Selected user cannot handle this phase.'])->withInput();
            }
        }

        // Prepare update data
        $updateData = [
            'order_id' => $request->order_id,
            'phase' => $request->phase,
            'status' => $request->status,
            'start_quantity' => $request->start_quantity,
            'end_quantity' => $request->end_quantity,
            'reject_quantity' => $request->reject_quantity,
            'reject_status' => $request->reject_status,
            'assigned_user_id' => $request->assigned_user_id ?: null,
            'remarks' => $request->remarks,
        ];

        // Handle time fields
        if ($request->filled('start_time')) {
            $updateData['start_time'] = $request->start_time;
        }
        if ($request->filled('end_time')) {
            $updateData['end_time'] = $request->end_time;
            
            // Recalculate duration if both times are provided
            if ($request->filled('start_time')) {
                $startTime = \Carbon\Carbon::parse($request->start_time);
                $endTime = \Carbon\Carbon::parse($request->end_time);
                $updateData['duration'] = $startTime->diffInMinutes($endTime, false);
            }
        }

        // Update the job
        $job->update($updateData);

        return redirect()->route('jobs.show', $job)
            ->with('success', 'Job updated successfully.');
    }

    /**
     * Remove the specified job
     */
    public function destroy(Job $job)
    {
        $job->delete();
        return redirect()->route('jobs.index')
            ->with('success', 'Job deleted successfully.');
    }

    /**
     * Assign job to user (Admin/SuperAdmin only)
     */
    public function assign(Request $request, Job $job)
    {
        $request->validate([
            'assigned_user_id' => 'nullable|exists:users,id',
        ]);

        // If no user is assigned, just unassign the job
        if (!$request->filled('assigned_user_id')) {
            $job->update(['assigned_user_id' => null]);
            return response()->json([
                'message' => 'Job unassigned successfully',
                'job' => $job->fresh(),
            ]);
        }

        $user = User::find($request->assigned_user_id);
        
        // Check if user can handle this phase
        if ($user->isProductionStaff() && $user->phase !== $job->phase) {
            return response()->json(['error' => 'User cannot handle this phase'], 400);
        }

        $job->update(['assigned_user_id' => $request->assigned_user_id]);

        return response()->json([
            'message' => 'Job assigned successfully',
            'job' => $job->fresh(),
        ]);
    }

    /**
     * Get available users for job assignment
     */
    public function getAvailableUsers(Job $job)
    {
        $users = User::where('role', 'Production Staff')
            ->where('phase', $job->phase)
            ->get();

        return response()->json(['users' => $users]);
    }

    /**
     * Get job details for QR scanner
     */
    public function getJobDetails(Job $job)
    {
        $user = Auth::user();
        
        // SuperAdmin and Sales Manager can access all jobs
        if ($user->isSuperAdmin() || $user->isSalesManager()) {
            $job->load(['order.client']);
            return response()->json([
                'success' => true,
                'job' => $job
            ]);
        }
        
        // Production staff can access:
        // 1. Jobs assigned to them
        // 2. Unassigned jobs that match their phase
        if ($user->isProductionStaff()) {
            $canAccess = false;
            
            // Check if job is assigned to this user
            if ($job->assigned_user_id === $user->id) {
                $canAccess = true;
            }
            // Check if job is unassigned and matches user's phase
            elseif (!$job->assigned_user_id && $job->phase === $user->phase) {
                $canAccess = true;
            }
            
            if ($canAccess) {
                $job->load(['order.client']);
                return response()->json([
                    'success' => true,
                    'job' => $job
                ]);
            } else {
                return response()->json([
                    'success' => false, 
                    'message' => 'Access denied. This job is not assigned to you or does not match your phase.'
                ]);
            }
        }
        
        // Default: deny access
        return response()->json([
            'success' => false, 
            'message' => 'Access denied'
        ]);
    }

    /**
     * Get workflow information for a job
     */
    public function getWorkflowInfo(Job $job)
    {
        $user = Auth::user();
        
        // SuperAdmin and Sales Manager can access all jobs
        if ($user->isSuperAdmin() || $user->isSalesManager()) {
            $previousJob = $this->getPreviousJob($job);
            $nextJob = $this->getNextJob($job);
            
            return response()->json([
                'success' => true,
                'previous_job' => $previousJob,
                'next_job' => $nextJob,
                'can_start' => !$previousJob || $previousJob->status === 'Completed',
                'workflow_message' => $previousJob && $previousJob->status !== 'Completed' 
                    ? "Please complete {$previousJob->phase} phase first." 
                    : "Ready to start {$job->phase} phase."
            ]);
        }
        
        // Production staff can access:
        // 1. Jobs assigned to them
        // 2. Unassigned jobs that match their phase
        if ($user->isProductionStaff()) {
            $canAccess = false;
            
            // Check if job is assigned to this user
            if ($job->assigned_user_id === $user->id) {
                $canAccess = true;
            }
            // Check if job is unassigned and matches user's phase
            elseif (!$job->assigned_user_id && $job->phase === $user->phase) {
                $canAccess = true;
            }
            
            if ($canAccess) {
                $previousJob = $this->getPreviousJob($job);
                $nextJob = $this->getNextJob($job);
                
                return response()->json([
                    'success' => true,
                    'previous_job' => $previousJob,
                    'next_job' => $nextJob,
                    'can_start' => !$previousJob || $previousJob->status === 'Completed',
                    'workflow_message' => $previousJob && $previousJob->status !== 'Completed' 
                        ? "Please complete {$previousJob->phase} phase first." 
                        : "Ready to start {$job->phase} phase."
                ]);
            } else {
                return response()->json([
                    'success' => false, 
                    'message' => 'Access denied. This job is not assigned to you or does not match your phase.'
                ]);
            }
        }
        
        // Default: deny access
        return response()->json([
            'success' => false, 
            'message' => 'Access denied'
        ]);
    }

    /**
     * Generate QR code for job
     */
    public function generateQr(Job $job)
    {
        $qrCode = $job->generateQrCode();
        return response($qrCode)->header('Content-Type', 'image/svg+xml');
    }

    /**
     * Print job order with QR code
     */
    public function printJob(Job $job)
    {
        $job->load(['order.client']);
        return view('jobs.print', compact('job'));
    }

    /**
     * Debug method to list all jobs (for testing QR scanner)
     */
    public function debugJobs()
    {
        $jobs = Job::select('job_id', 'phase', 'status', 'order_id')
            ->with(['order:order_id,job_name'])
            ->get();
        
        return response()->json([
            'jobs' => $jobs->map(function($job) {
                return [
                    'job_id' => $job->job_id,
                    'phase' => $job->phase,
                    'status' => $job->status,
                    'order_id' => $job->order_id,
                    'order_name' => $job->order->job_name ?? 'N/A'
                ];
            })
        ]);
    }

    /**
     * Get the previous job in sequence
     */
    private function getPreviousJob(Job $job)
    {
        $phases = ['PRINT', 'PRESS', 'CUT', 'SEW', 'QC', 'IRON/PACKING'];
        $currentIndex = array_search($job->phase, $phases);
        
        if ($currentIndex !== false && $currentIndex > 0) {
            return $job->order->jobs()->where('phase', $phases[$currentIndex - 1])->first();
        }
        
        return null;
    }

    /**
     * Get the next job in sequence
     */
    private function getNextJob(Job $job)
    {
        $phases = ['PRINT', 'PRESS', 'CUT', 'SEW', 'QC', 'IRON/PACKING'];
        $currentIndex = array_search($job->phase, $phases);
        
        if ($currentIndex !== false && $currentIndex < count($phases) - 1) {
            return $job->order->jobs()->where('phase', $phases[$currentIndex + 1])->first();
        }
        
        return null;
    }

    /**
     * Format duration in human readable format
     */
    private function formatDuration($minutes)
    {
        if (!$minutes) return 'N/A';
        
        if ($minutes < 60) {
            return "{$minutes} minutes";
        }
        
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;
        
        if ($remainingMinutes === 0) {
            return "{$hours} hour" . ($hours > 1 ? 's' : '');
        }
        
        return "{$hours} hour" . ($hours > 1 ? 's' : '') . " {$remainingMinutes} minute" . ($remainingMinutes > 1 ? 's' : '');
    }

    /**
     * Get job details by QR code
     */
    public function getJobDetailsByQr($qrCode)
    {
        $user = auth()->user();
        
        // Find job by QR code
        $job = Job::where('qr_code', $qrCode)->with(['order.client', 'assignedUser'])->first();
        
        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => 'Job not found with this QR code.'
            ], 404);
        }
        
        // Check access permissions
        if ($user->isProductionStaff()) {
            // Production staff can only access jobs assigned to them or unassigned jobs matching their phase
            if ($job->assigned_user_id && $job->assigned_user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'This job is assigned to another user.'
                ], 403);
            }
            
            if ($job->phase !== $user->phase) {
                return response()->json([
                    'success' => false,
                    'message' => 'This job does not match your assigned phase.'
                ], 403);
            }
        } elseif (!$user->isSuperAdmin() && !$user->isSalesManager()) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Only SuperAdmin and Sales Manager can view job details.'
            ], 403);
        }
        
        return response()->json([
            'success' => true,
            'job' => $job
        ]);
    }
} 