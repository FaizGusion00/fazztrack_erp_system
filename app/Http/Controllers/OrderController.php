<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Order;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Display a listing of orders
     */
    public function index(Request $request)
    {
        $query = Order::with(['client', 'jobs']);
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('job_name', 'like', "%{$search}%")
                  ->orWhere('order_id', 'like', "%{$search}%")
                  ->orWhereHas('client', function($clientQuery) use ($search) {
                      $clientQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by delivery method
        if ($request->filled('delivery_method')) {
            $query->where('delivery_method', $request->delivery_method);
        }
        
        $orders = $query->paginate(15)->withQueryString();
        return view('orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new order
     */
    public function create()
    {
        $clients = Client::all();
        return view('orders.create', compact('clients'));
    }

    /**
     * Store a newly created order
     */
    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,client_id',
            'job_name' => 'required|string|max:255',
            'delivery_method' => 'required|in:Self Collect,Shipping',
            'design_deposit' => 'required|numeric|min:0',
            'production_deposit' => 'required|numeric|min:0',
            'balance_payment' => 'required|numeric|min:0',
            'due_date_design' => 'required|date',
            'due_date_production' => 'required|date|after:due_date_design',
            'remarks' => 'nullable|string',
            'receipts.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'job_sheet' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'design_front' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            'design_back' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            'design_left' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            'design_right' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            'download_link' => 'nullable|url',
        ]);

        $orderData = $request->only([
            'client_id', 'job_name', 'delivery_method', 'design_deposit',
            'production_deposit', 'balance_payment', 'due_date_design',
            'due_date_production', 'remarks', 'download_link'
        ]);

        $orderData['status'] = 'Order Created';

        // Handle multiple receipts upload
        if ($request->hasFile('receipts')) {
            $receiptPaths = [];
            foreach ($request->file('receipts') as $receipt) {
                $receiptPaths[] = $receipt->store('receipts', 'public');
            }
            $orderData['receipts'] = json_encode($receiptPaths);
        }

        // Handle job sheet upload
        if ($request->hasFile('job_sheet')) {
            $orderData['job_sheet'] = $request->file('job_sheet')->store('job_sheets', 'public');
        }

        // Handle design files upload
        $designFiles = [];
        $designFields = ['design_front', 'design_back', 'design_left', 'design_right'];
        
        foreach ($designFields as $field) {
            if ($request->hasFile($field)) {
                $designFiles[$field] = $request->file($field)->store('designs/final', 'public');
            }
        }
        
        if (!empty($designFiles)) {
            $orderData['design_files'] = json_encode($designFiles);
        }

        $order = Order::create($orderData);

        return redirect()->route('orders.show', $order)
            ->with('success', 'Order created successfully with all uploaded files.');
    }

    /**
     * Get order status for real-time updates
     */
    public function getStatus(Order $order)
    {
        $order->load(['jobs']);
        $completedJobs = $order->jobs->where('status', 'Completed')->count();
        $totalJobs = $order->jobs->count();
        
        return response()->json([
            'success' => true,
            'order' => $order,
            'completed_jobs' => $completedJobs,
            'total_jobs' => $totalJobs,
            'progress_percentage' => $totalJobs > 0 ? ($completedJobs / $totalJobs) * 100 : 0
        ]);
    }

    /**
     * Display the specified order
     */
    public function show(Order $order)
    {
        $order->load(['client.contacts', 'jobs.assignedUser']);
        return view('orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified order
     */
    public function edit(Order $order)
    {
        $clients = Client::all();
        return view('orders.edit', compact('order', 'clients'));
    }

    /**
     * Update the specified order
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,client_id',
            'job_name' => 'required|string|max:255',
            'delivery_method' => 'required|in:Self Collect,Shipping',
            'design_deposit' => 'required|numeric|min:0',
            'production_deposit' => 'required|numeric|min:0',
            'balance_payment' => 'required|numeric|min:0',
            'due_date_design' => 'required|date',
            'due_date_production' => 'required|date|after:due_date_design',
            'remarks' => 'nullable|string',
            'receipts.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'job_sheet' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'design_front' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            'design_back' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            'design_left' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            'design_right' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            'download_link' => 'nullable|url',
        ]);

        $orderData = $request->only([
            'client_id', 'job_name', 'delivery_method', 'design_deposit',
            'production_deposit', 'balance_payment', 'due_date_design',
            'due_date_production', 'remarks', 'download_link'
        ]);

        // Handle multiple receipts upload
        if ($request->hasFile('receipts')) {
            $receiptPaths = [];
            foreach ($request->file('receipts') as $receipt) {
                $receiptPaths[] = $receipt->store('receipts', 'public');
            }
            $orderData['receipts'] = json_encode($receiptPaths);
        }

        // Handle job sheet upload
        if ($request->hasFile('job_sheet')) {
            // Delete old file if exists
            if ($order->job_sheet) {
                Storage::disk('public')->delete($order->job_sheet);
            }
            $orderData['job_sheet'] = $request->file('job_sheet')->store('job_sheets', 'public');
        }

        // Handle design files upload (finalized design by Sales Manager)
        $designFiles = $order->getDesignFilesArray();
        
        if ($request->hasFile('design_front')) {
            // Delete old file if exists
            if (isset($designFiles['design_front'])) {
                Storage::disk('public')->delete($designFiles['design_front']);
            }
            $designFiles['design_front'] = $request->file('design_front')->store('designs/final', 'public');
        }
        if ($request->hasFile('design_back')) {
            // Delete old file if exists
            if (isset($designFiles['design_back'])) {
                Storage::disk('public')->delete($designFiles['design_back']);
            }
            $designFiles['design_back'] = $request->file('design_back')->store('designs/final', 'public');
        }
        if ($request->hasFile('design_left')) {
            // Delete old file if exists
            if (isset($designFiles['design_left'])) {
                Storage::disk('public')->delete($designFiles['design_left']);
            }
            $designFiles['design_left'] = $request->file('design_left')->store('designs/final', 'public');
        }
        if ($request->hasFile('design_right')) {
            // Delete old file if exists
            if (isset($designFiles['design_right'])) {
                Storage::disk('public')->delete($designFiles['design_right']);
            }
            $designFiles['design_right'] = $request->file('design_right')->store('designs/final', 'public');
        }
        
        if (!empty($designFiles)) {
            $orderData['design_files'] = json_encode($designFiles);
        }

        $order->update($orderData);

        return redirect()->route('orders.show', $order)
            ->with('success', 'Order updated successfully.');
    }

    /**
     * Approve order payment (Admin/SuperAdmin only)
     */
    public function approve(Order $order)
    {
        $user = auth()->user();
        
        // Only Admin and SuperAdmin can approve payments
        if (!$user->isAdmin() && !$user->isSuperAdmin()) {
            abort(403, 'Access denied. Only admins can approve payments.');
        }
        
        $order->update(['status' => 'Order Approved']);
        return redirect()->route('orders.show', $order)
            ->with('success', 'Payment approved successfully. Order is now ready for design.');
    }

    /**
     * Put order on hold
     */
    public function putOnHold(Request $request, Order $order)
    {
        $request->validate([
            'status_comment' => 'nullable|string',
        ]);

        $order->update([
            'status' => 'On Hold',
            'status_comment' => $request->status_comment ?? 'Order put on hold',
        ]);

        return redirect()->route('orders.show', $order)
            ->with('success', 'Order put on hold.');
    }

    /**
     * Resume order from hold
     */
    public function resume(Order $order)
    {
        $order->update([
            'status' => 'In Progress',
            'status_comment' => null,
        ]);

        return redirect()->route('orders.show', $order)
            ->with('success', 'Order resumed successfully.');
    }

    /**
     * Complete order
     */
    public function complete(Order $order)
    {
        $order->update(['status' => 'Completed']);
        return redirect()->route('orders.show', $order)
            ->with('success', 'Order completed successfully.');
    }

    /**
     * Create jobs for order (Designer/Sales Manager)
     */
    public function createJobs(Request $request, Order $order)
    {
        $request->validate([
            'phase' => 'required|in:PRINT,PRESS,CUT,SEW,QC,IRON/PACKING',
        ]);

        $phase = $request->phase;
        
        // Check if this phase already exists
        $existingJob = $order->jobs()->where('phase', $phase)->first();
        if ($existingJob) {
            return redirect()->route('orders.show', $order)
                ->with('error', "A job for {$phase} phase already exists.");
        }
        
        // Check if previous phases are completed (workflow validation)
        $phases = ['PRINT', 'PRESS', 'CUT', 'SEW', 'QC', 'IRON/PACKING'];
        $phaseIndex = array_search($phase, $phases);
        
        if ($phaseIndex > 0) {
            $previousPhase = $phases[$phaseIndex - 1];
            $previousJob = $order->jobs()->where('phase', $previousPhase)->first();
            
            if (!$previousJob || $previousJob->status !== 'Completed') {
                return redirect()->route('orders.show', $order)
                    ->with('error', "Cannot create {$phase} job. Previous phase ({$previousPhase}) must be completed first.");
            }
        }

        // Create the job
        Job::create([
            'order_id' => $order->order_id,
            'phase' => $phase,
            'status' => 'Pending',
            'qr_code' => 'QR_' . Str::random(10) . '_' . $phase,
        ]);

        return redirect()->route('orders.show', $order)
            ->with('success', "{$phase} job created successfully.");
    }

    /**
     * Remove the specified order
     */
    public function destroy(Order $order)
    {
        // Delete associated files
        if ($order->receipts) {
            Storage::disk('public')->delete($order->receipts);
        }
        if ($order->job_sheet) {
            Storage::disk('public')->delete($order->job_sheet);
        }

        $order->delete();
        return redirect()->route('orders.index')
            ->with('success', 'Order deleted successfully.');
    }
} 