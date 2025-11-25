<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Order;
use App\Models\Product;
use App\Models\Job;
use App\Models\OrderStatusLog;
use App\Services\StorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    private const HOLD_ELIGIBLE_STATUSES = [
        'Order Approved',
        'Design Review',
        'Design Approved',
        'Job Created',
        'Job Start',
        'Job Complete',
        'Order Packaging',
    ];

    private const COMPLETED_STATUSES = [
        'Order Finished',
        'Completed',
    ];

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
        
        // Handle tab filtering
        $activeTab = $request->get('tab', 'active');
        
        if ($activeTab === 'completed') {
            $query->whereIn('status', self::COMPLETED_STATUSES);
        } else {
            $query->whereNotIn('status', self::COMPLETED_STATUSES);
        }
        
        // Sorting functionality
        $sort = $request->get('sort', 'latest_added');
        switch ($sort) {
            case 'latest_added':
                $query->orderBy('created_at', 'desc');
                break;
            case 'latest_updated':
                $query->orderBy('updated_at', 'desc');
                break;
            case 'alphabetical':
                $query->orderBy('job_name', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }
        
        $orders = $query->paginate(15)->withQueryString();
        
        // Get counts for tabs
        $activeCount = Order::whereNotIn('status', self::COMPLETED_STATUSES)->count();
        $completedCount = Order::whereIn('status', self::COMPLETED_STATUSES)->count();
        
        return view('orders.index', [
            'orders' => $orders,
            'activeTab' => $activeTab,
            'activeCount' => $activeCount,
            'completedCount' => $completedCount,
            'holdEligibleStatuses' => self::HOLD_ELIGIBLE_STATUSES,
        ]);
    }

    /**
     * Show the form for creating a new order
     */
    public function create()
    {
        // Optimize: Select only needed columns instead of all()
        $clients = Client::select('client_id', 'name')->orderBy('name')->get();
        $products = Product::active()->inStock()->select('product_id', 'name', 'stock')->orderBy('name')->get();
        return view('orders.create', compact('clients', 'products'));
    }

    /**
     * Store a newly created order
     */
    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,client_id',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,product_id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.comments' => 'nullable|string|max:500',
            'job_name' => 'required|string|max:255',
            'delivery_method' => 'required|in:Self Collect,Shipping,Grab,Lalamove,Bus',
            'design_deposit' => 'required|numeric|min:0',
            'production_deposit' => 'required|numeric|min:0',
            'balance_payment' => 'required|numeric|min:0',
            'due_date_design' => 'required|date',
            'due_date_production' => 'required|date|after:due_date_design',
            'remarks' => 'nullable|string',
            'receipts.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'job_sheets' => 'nullable|array',
            'job_sheets.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'design_images' => 'nullable|array',
            'design_images.*' => 'nullable|file|mimes:jpg,jpeg,png|max:20480',
            'download_link' => 'nullable|url',
        ]);

        // For backward compatibility, use the first product as the main product_id
        $firstProduct = $request->products[0];
        
        $orderData = $request->only([
            'client_id', 'job_name', 'delivery_method', 'design_deposit',
            'production_deposit', 'balance_payment', 'due_date_design',
            'due_date_production', 'remarks', 'download_link'
        ]);
        
        // Set the first product as the main product_id for backward compatibility
        $orderData['product_id'] = $firstProduct['product_id'];
        $orderData['status'] = 'Order Created';

        // Handle multiple receipts upload
        if ($request->hasFile('receipts')) {
            $receiptPaths = StorageService::storeMultiple($request->file('receipts'), 'receipts');
            $orderData['receipts'] = json_encode($receiptPaths);
        }

        // Handle multiple job sheets upload
        if ($request->hasFile('job_sheets')) {
            $jobSheetPaths = StorageService::storeMultiple($request->file('job_sheets'), 'job_sheets');
            $orderData['job_sheet'] = json_encode($jobSheetPaths);
        }

        // Handle design images upload (multiple images)
        $designImages = [];
        if ($request->hasFile('design_images')) {
            foreach ($request->file('design_images') as $imageFile) {
                if ($imageFile && $imageFile->isValid()) {
                    $designImages[] = StorageService::store($imageFile, 'designs/final');
                }
            }
        }
        
        // Store as array - Laravel will automatically JSON encode due to 'array' cast in model
        if (!empty($designImages)) {
            $orderData['design_files'] = $designImages;
        }

        $order = Order::create($orderData);
        
        // Clear dashboard cache when new order is created
        Cache::forget('dashboard_stats_superadmin');
        Cache::forget('dashboard_stats_admin');
        Cache::forget('dashboard_stats_sales');
        Cache::forget('dashboard_recent_orders');
        Cache::forget('dashboard_recent_orders_sales');

        // Handle multiple receipts upload - store each in separate table with dates
        if ($request->hasFile('receipts')) {
            foreach ($request->file('receipts') as $receiptFile) {
                $filePath = StorageService::store($receiptFile, 'receipts');
                \App\Models\OrderReceipt::create([
                    'order_id' => $order->order_id,
                    'file_path' => $filePath,
                    'file_name' => $receiptFile->getClientOriginalName(),
                    'file_type' => $receiptFile->getClientMimeType(),
                    'file_size' => $receiptFile->getSize(),
                    'uploaded_at' => now(),
                ]);
            }
        }

        // Attach all products with their quantities and comments
        // Handle duplicate products by combining them
        $productGroups = [];
        foreach ($request->products as $productData) {
            $productId = $productData['product_id'];
            if (isset($productGroups[$productId])) {
                // Combine quantities and merge comments
                $productGroups[$productId]['quantity'] += $productData['quantity'];
                if ($productData['comments']) {
                    $existingComments = $productGroups[$productId]['comments'];
                    $productGroups[$productId]['comments'] = $existingComments ? 
                        $existingComments . ' | ' . $productData['comments'] : 
                        $productData['comments'];
                }
            } else {
                $productGroups[$productId] = $productData;
            }
        }
        
        // Attach the combined products
        foreach ($productGroups as $productData) {
            $order->products()->attach($productData['product_id'], [
                'quantity' => $productData['quantity'],
                'comments' => $productData['comments'] ?? null,
            ]);
        }

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
        $order->load(['client.contacts', 'jobs.assignedUser', 'orderProducts.product', 'statusLogs.user']);

        return view('orders.show', [
            'order' => $order,
            'holdEligibleStatuses' => self::HOLD_ELIGIBLE_STATUSES,
        ]);
    }

    /**
     * Show the form for editing the specified order
     */
    public function edit(Order $order)
    {
        // Optimize: Select only needed columns instead of all()
        $clients = Client::select('client_id', 'name')->orderBy('name')->get();
        $products = Product::active()->select('product_id', 'name', 'stock')->orderBy('name')->get();
        $order->load('orderProducts.product'); // Load existing order products
        return view('orders.edit', compact('order', 'clients', 'products'));
    }

    /**
     * Update the specified order
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,client_id',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,product_id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.comments' => 'nullable|string|max:500',
            'job_name' => 'required|string|max:255',
            'delivery_method' => 'required|in:Self Collect,Shipping,Grab,Lalamove,Bus',
            'design_deposit' => 'required|numeric|min:0',
            'production_deposit' => 'required|numeric|min:0',
            'balance_payment' => 'required|numeric|min:0',
            'due_date_design' => 'required|date',
            'due_date_production' => 'required|date|after:due_date_design',
            'remarks' => 'nullable|string',
            'receipts.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'job_sheets' => 'nullable|array',
            'job_sheets.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'design_images' => 'nullable|array',
            'design_images.*' => 'nullable|file|mimes:jpg,jpeg,png|max:20480',
            'download_link' => 'nullable|url',
            'delete_receipts' => 'nullable|array',
            'delete_receipts.*' => 'nullable|integer|exists:order_receipts,id',
            'delete_job_sheets' => 'nullable|array',
            'delete_job_sheets.*' => 'nullable|string',
            'delete_design_images' => 'nullable|array',
            'delete_design_images.*' => 'nullable|string',
        ]);

        // For backward compatibility, use the first product as the main product_id
        $firstProduct = $request->products[0];
        
        $orderData = $request->only([
            'client_id', 'job_name', 'delivery_method', 'design_deposit',
            'production_deposit', 'balance_payment', 'due_date_design',
            'due_date_production', 'remarks', 'download_link'
        ]);
        
        // Set the first product as the main product_id for backward compatibility
        $orderData['product_id'] = $firstProduct['product_id'];

        // Handle job sheets - delete and add
        $existingJobSheets = [];
        if ($order->job_sheet) {
            // Check if it's JSON (new format) or string (old format)
            $decoded = json_decode($order->job_sheet, true);
            if (is_array($decoded)) {
                // New format: array of paths
                $existingJobSheets = $decoded;
            } else {
                // Old format: single path string
                $existingJobSheets = [$order->job_sheet];
            }
        }
        
        // Remove deleted job sheets
        if ($request->has('delete_job_sheets') && is_array($request->delete_job_sheets)) {
            foreach ($request->delete_job_sheets as $deletedPath) {
                if (!empty($deletedPath)) {
                    // Remove from array
                    $existingJobSheets = array_values(array_filter($existingJobSheets, function($path) use ($deletedPath) {
                        return $path !== $deletedPath;
                    }));
                    // Delete file from storage
                    StorageService::delete($deletedPath);
                }
            }
        }
        
        // Add new job sheets to existing ones
        if ($request->hasFile('job_sheets')) {
            foreach ($request->file('job_sheets') as $jobSheetFile) {
                if ($jobSheetFile && $jobSheetFile->isValid()) {
                    $existingJobSheets[] = StorageService::store($jobSheetFile, 'job_sheets');
                }
            }
        }
        
        // Update job_sheet (can be empty array if all deleted)
        $orderData['job_sheet'] = !empty($existingJobSheets) ? json_encode($existingJobSheets) : null;

        // Handle design images - delete and add
        $existingDesignFiles = $order->getDesignFilesArray();
        $designImages = [];
        
        // Convert old format (keyed array) to new format (numeric array) if needed
        // Preserve existing images
        if (is_array($existingDesignFiles) && !empty($existingDesignFiles)) {
            foreach ($existingDesignFiles as $key => $value) {
                if (is_numeric($key)) {
                    // Already in new format
                    $designImages[] = $value;
                } else {
                    // Old format: convert to new format
                    if (!empty($value)) {
                        $designImages[] = $value;
                    }
                }
            }
        }
        
        // Remove deleted design images
        if ($request->has('delete_design_images') && is_array($request->delete_design_images)) {
            foreach ($request->delete_design_images as $deletedPath) {
                if (!empty($deletedPath)) {
                    // Remove from array
                    $designImages = array_values(array_filter($designImages, function($path) use ($deletedPath) {
                        return $path !== $deletedPath;
                    }));
                    // Delete file from storage
                    StorageService::delete($deletedPath);
                }
            }
        }
        
        // Add new images to existing ones
        if ($request->hasFile('design_images')) {
            foreach ($request->file('design_images') as $imageFile) {
                if ($imageFile && $imageFile->isValid()) {
                    $designImages[] = StorageService::store($imageFile, 'designs/final');
                }
            }
        }
        
        // Update design_files (can be empty array if all deleted)
        // Store as array - Laravel will automatically JSON encode due to 'array' cast in model
        $orderData['design_files'] = !empty($designImages) ? $designImages : [];

        $order->update($orderData);
        
        // Clear dashboard cache when order is updated
        Cache::forget('dashboard_stats_superadmin');
        Cache::forget('dashboard_stats_admin');
        Cache::forget('dashboard_stats_sales');
        Cache::forget('dashboard_recent_orders');
        Cache::forget('dashboard_recent_orders_sales');
        Cache::forget('dashboard_revenue_data');
        Cache::forget('dashboard_revenue_data_sales');

        // Update products - first detach all existing, then attach new ones
        $order->products()->detach();
        
        // Handle duplicate products by combining them
        $productGroups = [];
        foreach ($request->products as $productData) {
            $productId = $productData['product_id'];
            if (isset($productGroups[$productId])) {
                // Combine quantities and merge comments
                $productGroups[$productId]['quantity'] += $productData['quantity'];
                if ($productData['comments']) {
                    $existingComments = $productGroups[$productId]['comments'];
                    $productGroups[$productId]['comments'] = $existingComments ? 
                        $existingComments . ' | ' . $productData['comments'] : 
                        $productData['comments'];
                }
            } else {
                $productGroups[$productId] = $productData;
            }
        }
        
        // Attach the combined products
        foreach ($productGroups as $productData) {
            $order->products()->attach($productData['product_id'], [
                'quantity' => $productData['quantity'],
                'comments' => $productData['comments'] ?? null,
            ]);
        }

        // Handle receipts - delete and add
        if ($request->has('delete_receipts') && is_array($request->delete_receipts)) {
            foreach ($request->delete_receipts as $receiptId) {
                if (!empty($receiptId)) {
                    $receipt = \App\Models\OrderReceipt::find($receiptId);
                    if ($receipt && $receipt->order_id === $order->order_id) {
                        // Delete file from storage
                        StorageService::delete($receipt->file_path);
                        // Delete record from database
                        $receipt->delete();
                    }
                }
            }
        }
        
        // Add new receipts
        if ($request->hasFile('receipts')) {
            foreach ($request->file('receipts') as $receiptFile) {
                $filePath = StorageService::store($receiptFile, 'receipts');
                \App\Models\OrderReceipt::create([
                    'order_id' => $order->order_id,
                    'file_path' => $filePath,
                    'file_name' => $receiptFile->getClientOriginalName(),
                    'file_type' => $receiptFile->getClientMimeType(),
                    'file_size' => $receiptFile->getSize(),
                    'uploaded_at' => now(),
                ]);
            }
        }

        return redirect()->route('orders.show', $order)
            ->with('success', 'Order updated successfully.');
    }

    /**
     * Approve order payment (Admin/SuperAdmin only)
     */
    public function approve(Order $order)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
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
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->isSuperAdmin() && !$user->isSalesManager()) {
            abort(403, 'Access denied. Only sales managers or super admins can hold orders.');
        }

        if (in_array($order->status, self::COMPLETED_STATUSES, true)) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Completed or delivered orders cannot be put on hold.');
        }

        if ($order->status === 'On Hold') {
            return redirect()->route('orders.show', $order)
                ->with('info', 'Order is already on hold.');
        }

        if (!in_array($order->status, self::HOLD_ELIGIBLE_STATUSES, true)) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Order cannot be put on hold at this stage.');
        }

        $request->validate([
            'status_comment' => 'nullable|string|max:500',
        ]);

        $previousStatus = $order->status;
        $comment = $request->status_comment ?? 'Order put on hold';

        $order->update([
            'status' => 'On Hold',
            'status_comment' => $comment,
            'status_before_hold' => $previousStatus,
        ]);

        $this->logStatusChange(
            $order,
            $previousStatus,
            'On Hold',
            $comment,
            $user->id
        );
        return redirect()->route('orders.show', $order)
            ->with('success', 'Order put on hold.');
    }

    /**
     * Resume order from hold
     */
    public function resume(Order $order)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->isSuperAdmin() && !$user->isSalesManager()) {
            abort(403, 'Access denied. Only sales managers or super admins can resume orders.');
        }

        if ($order->status !== 'On Hold') {
            return redirect()->route('orders.show', $order)
                ->with('info', 'Order is not currently on hold.');
        }

        $previousStatus = $order->status_before_hold ?? 'Order Approved';

        $order->update([
            'status' => $previousStatus,
            'status_comment' => null,
            'status_before_hold' => null,
        ]);

        $this->logStatusChange(
            $order,
            'On Hold',
            $previousStatus,
            'Order resumed from hold',
            $user->id
        );
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

    private function logStatusChange(Order $order, ?string $previousStatus, string $newStatus, ?string $comment, int $userId): void
    {
        OrderStatusLog::create([
            'order_id' => $order->order_id,
            'user_id' => $userId,
            'previous_status' => $previousStatus,
            'new_status' => $newStatus,
            'comment' => $comment,
        ]);
    }

    /**
     * Create jobs for order (Designer/Sales Manager)
     */
    public function createJobs(Request $request, Order $order)
    {
        if ($order->status === 'On Hold') {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Jobs cannot be created while the order is on hold. Please resume the order first.');
        }
        $request->validate([
            'phase' => 'required|in:PRINT,PRESS,CUT,SEW,QC',
        ]);

        $phase = $request->phase;
        
        // Check if this phase already exists
        $existingJob = $order->jobs()->where('phase', $phase)->first();
        if ($existingJob) {
            return redirect()->route('orders.show', $order)
                ->with('error', "A job for {$phase} phase already exists.");
        }
        
        // Check if previous phases are completed (workflow validation)
        $phases = ['PRINT', 'PRESS', 'CUT', 'SEW', 'QC'];
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
            $receipts = json_decode($order->receipts, true) ?: [];
            StorageService::deleteMultiple($receipts);
        }
        if ($order->job_sheet) {
            StorageService::delete($order->job_sheet);
        }

        $order->delete();
        return redirect()->route('orders.index')
            ->with('success', 'Order deleted successfully.');
    }

    /**
     * Update delivery status for an order
     */
    public function updateDelivery(Request $request, Order $order)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Check role-based access
        if (!$user->isSuperAdmin() && !$user->isAdmin() && !$user->isSalesManager()) {
            abort(403, 'Access denied. You do not have permission to update delivery status.');
        }
        
        $request->validate([
            'delivery_status' => 'required|in:Pending,In Transit,Delivered,Failed',
            'tracking_number' => 'nullable|string|max:255',
            'delivery_company' => 'nullable|string|max:255',
            'delivery_notes' => 'nullable|string',
        ]);

        $updateData = [
            'delivery_status' => $request->delivery_status,
            'tracking_number' => $request->tracking_number,
            'delivery_company' => $request->delivery_company,
            'delivery_notes' => $request->delivery_notes,
        ];

        // Set delivery date when delivered
        if ($request->delivery_status === 'Delivered') {
            $updateData['delivery_date'] = now();
        }

        $order->update($updateData);

        return response()->json([
            'message' => 'Delivery status updated successfully',
            'order' => $order->fresh(),
        ]);
    }

    /**
     * Update payment status for an order
     */
    public function updatePayment(Request $request, Order $order)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Check role-based access
        if (!$user->isSuperAdmin() && !$user->isAdmin() && !$user->isSalesManager()) {
            abort(403, 'Access denied. You do not have permission to update payment status.');
        }
        
        $request->validate([
            'payment_status' => 'required|in:Pending,Partial,Completed,Overdue',
            'paid_amount' => 'required|numeric|min:0|max:' . $order->total_amount,
            'payment_notes' => 'nullable|string',
            'payment_due_date' => 'nullable|date',
        ]);

        $updateData = [
            'payment_status' => $request->payment_status,
            'paid_amount' => $request->paid_amount,
            'payment_notes' => $request->payment_notes,
            'payment_due_date' => $request->payment_due_date,
        ];

        // Set last payment date when payment is made
        if ($request->paid_amount > $order->paid_amount) {
            $updateData['last_payment_date'] = now();
        }

        $order->update($updateData);

        return response()->json([
            'message' => 'Payment status updated successfully',
            'order' => $order->fresh(),
        ]);
    }
} 