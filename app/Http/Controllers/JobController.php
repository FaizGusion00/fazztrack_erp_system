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
        $query = Job::with(['order.client', 'assignedUser']);
        
        // Role-based filtering
        if ($user->isProductionStaff()) {
            $query->where('assigned_user_id', $user->id);
        }
        
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
            $qrData = json_decode($request->qr_data, true);
            
            if (!$qrData || !isset($qrData['job_id'])) {
                return response()->json(['error' => 'Invalid QR code'], 400);
            }

            $job = Job::with(['order.client', 'assignedUser'])
                ->findOrFail($qrData['job_id']);

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
        
        // Check permissions
        if ($user->isProductionStaff() && $job->assigned_user_id !== $user->id) {
            return response()->json(['error' => 'Access denied'], 403);
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
        if ($order->status === 'Job Created') {
            $order->update(['status' => 'Job Start']);
        }

        return response()->json([
            'message' => 'Job started successfully',
            'job' => $job->fresh(),
            'start_time' => $job->start_time->toISOString(),
            'next_job' => $this->getNextJob($job)
        ]);
    }

    /**
     * End a job (capture end time and calculate duration)
     */
    public function endJob(Request $request, Job $job)
    {
        $user = Auth::user();
        
        // Check permissions
        if ($user->isProductionStaff() && $job->assigned_user_id !== $user->id) {
            return response()->json(['error' => 'Access denied'], 403);
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

        // Calculate time taken for this phase
        $duration = null;
        if ($job->start_time && $job->end_time) {
            $duration = $job->end_time->diffInMinutes($job->start_time);
        }

        // Update order status based on job completion
        $order = $job->order;
        $allJobs = $order->jobs;
        
        // Check if all jobs are completed
        if ($allJobs->where('status', 'Completed')->count() === $allJobs->count()) {
            $order->update(['status' => 'Order Finished']);
        }
        // Check if QC phase is completed
        elseif ($job->phase === 'QC' && $job->status === 'Completed') {
            $order->update(['status' => 'Job Complete']);
        }
        // Check if IRON/PACKING phase is starting
        elseif ($job->phase === 'IRON/PACKING' && $job->status === 'In Progress') {
            $order->update(['status' => 'Order Packaging']);
        }

        return response()->json([
            'message' => 'Job completed successfully',
            'job' => $job->fresh(),
            'end_time' => $job->end_time->toISOString(),
            'duration_minutes' => $duration,
            'duration_formatted' => $this->formatDuration($duration),
            'next_job' => $this->getNextJob($job)
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
        $orders = \App\Models\Order::where('status', 'Approved')->get();
        return view('jobs.edit', compact('job', 'orders'));
    }

    /**
     * Update the specified job
     */
    public function update(Request $request, Job $job)
    {
        $request->validate([
            'phase' => 'required|in:PRINT,PRESS,CUT,SEW,QC,IRON/PACKING',
            'start_quantity' => 'required|integer|min:1',
            'remarks' => 'nullable|string',
        ]);

        $job->update([
            'phase' => $request->phase,
            'start_quantity' => $request->start_quantity,
            'remarks' => $request->remarks,
        ]);

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
            'assigned_user_id' => 'required|exists:users,id',
        ]);

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
        
        // Check if user is assigned to this job or is admin/superadmin
        if ($job->assigned_user_id !== $user->id && !$user->isAdmin() && !$user->isSuperAdmin()) {
            return response()->json(['success' => false, 'message' => 'Access denied']);
        }
        
        $job->load(['order.client']);
        
        return response()->json([
            'success' => true,
            'job' => $job
        ]);
    }

    /**
     * Get workflow information for a job
     */
    public function getWorkflowInfo(Job $job)
    {
        $user = Auth::user();
        
        // Check if user is assigned to this job or is admin/superadmin
        if ($job->assigned_user_id !== $user->id && !$user->isAdmin() && !$user->isSuperAdmin()) {
            return response()->json(['success' => false, 'message' => 'Access denied']);
        }
        
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
} 