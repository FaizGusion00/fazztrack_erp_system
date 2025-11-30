<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Design;
use App\Models\OrderStatusLog;
use App\Services\StorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DesignController extends Controller
{
    /**
     * Display a listing of designs
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Only Designer, Admin, Sales Manager, SuperAdmin can access
        if (!$user->isDesigner() && !$user->isAdmin() && !$user->isSuperAdmin() && !$user->isSalesManager()) {
            abort(403, 'Access denied. Only designers, admins, and sales managers can access this page.');
        }
        
        // For designers, also get orders that need design work
        $ordersNeedingDesign = collect();
        $ordersNeedingPola = collect();
        if ($user->isDesigner()) {
            // Get orders with status "Order Approved" that don't have any designs yet
            $ordersNeedingDesign = Order::where('status', 'Order Approved')
                ->whereDoesntHave('designs')
                ->with('client')
                ->orderBy('due_date_design', 'asc')
                ->get();
            
            // Get all orders with approved designs (can add/edit pola link until production jobs end)
            // Allow editing pola link for orders with approved designs, regardless of order status or existing pola_link
            $ordersNeedingPola = Order::whereHas('designs', function($q) {
                    $q->where('status', 'Approved');
                })
                ->whereIn('status', [
                    'Design Approved', 
                    'Job Created', 
                    'Job Start', 
                    'Job Complete', 
                    'Order Packaging', 
                    'Order Finished'
                ])
                ->with(['client', 'designs' => function($q) {
                    $q->where('status', 'Approved');
                }])
                ->orderBy('due_date_design', 'asc')
                ->get();
        }
        
        $query = Design::with(['order.client', 'designer', 'approvedBy', 'rejectedBy']);
        
        // Filter based on user role
        if ($user->isDesigner()) {
            // Designer sees only their own designs
            $query->where('designer_id', $user->id);
        }
        // Sales Manager and SuperAdmin can see all designs
        
        // Filter by tab
        $tab = $request->get('tab', 'all');
        if ($tab === 'pending') {
            $query->where('status', 'Pending Review');
        } elseif ($tab === 'approved') {
            $query->where('status', 'Approved');
        } elseif ($tab === 'rejected') {
            $query->where('status', 'Rejected');
        } elseif ($tab === 'pola') {
            // For pola tab, we need orders with approved designs that need pola links
            // This will be handled separately in the view
        }
        
        // Filter by status (additional filter)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by order
        if ($request->filled('order_id')) {
            $query->where('order_id', $request->order_id);
        }
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('design_id', 'like', "%{$search}%")
                  ->orWhere('version', 'like', "%{$search}%")
                  ->orWhereHas('order', function($orderQuery) use ($search) {
                      $orderQuery->where('job_name', 'like', "%{$search}%")
                                ->orWhere('order_id', 'like', "%{$search}%")
                                ->orWhereHas('client', function($clientQuery) use ($search) {
                                    $clientQuery->where('name', 'like', "%{$search}%");
                                });
                  })
                  ->orWhereHas('designer', function($designerQuery) use ($search) {
                      $designerQuery->where('name', 'like', "%{$search}%");
                  });
            });
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
            case 'version':
                $query->orderBy('version', 'desc');
                break;
            case 'status':
                $query->orderBy('status', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }
        
        $designs = $query->paginate(15)->withQueryString();
        
        // Get counts for tabs
        $pendingCount = Design::when($user->isDesigner(), function($q) use ($user) {
            $q->where('designer_id', $user->id);
        })->where('status', 'Pending Review')->count();
        
        $approvedCount = Design::when($user->isDesigner(), function($q) use ($user) {
            $q->where('designer_id', $user->id);
        })->where('status', 'Approved')->count();
        
        $rejectedCount = Design::when($user->isDesigner(), function($q) use ($user) {
            $q->where('designer_id', $user->id);
        })->where('status', 'Rejected')->count();
        
        $view = $request->get('view', 'table');
        
        // Get count of orders with approved designs that can have pola links (for designer role only)
        $polaCount = 0;
        if ($user->isDesigner()) {
            $polaCount = $ordersNeedingPola->count();
        }
        
        return view('designs.index', compact('designs', 'pendingCount', 'approvedCount', 'rejectedCount', 'polaCount', 'view', 'ordersNeedingDesign', 'ordersNeedingPola', 'user'));
    }

    /**
     * Show the form for uploading design for an order
     */
    public function create(Request $request, ?Order $order = null)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Only Designer can upload designs
        if (!$user->isDesigner()) {
            abort(403, 'Access denied. Only designers can upload designs.');
        }
        
        // Handle query parameter if order is not provided via route model binding
        if (!$order) {
            // Try order_id query parameter first
            if ($request->has('order_id')) {
                $orderId = $request->get('order_id');
                $order = Order::find($orderId);
            } else {
                // Try to find numeric value in query parameters (e.g., ?2 means order_id=2)
                $queryParams = $request->query();
                foreach ($queryParams as $key => $value) {
                    // Check if key is numeric (e.g., ?2) or value is numeric (e.g., ?order=2)
                    $orderId = null;
                    if (is_numeric($key)) {
                        $orderId = $key;
                    } elseif (is_numeric($value)) {
                        $orderId = $value;
                    }
                    
                    if ($orderId) {
                        $order = Order::find($orderId);
                        if ($order) {
                            break;
                        }
                    }
                }
            }
            
            // If still no order, try to get from route parameter name
            if (!$order && $request->route() && $request->route()->hasParameter('order')) {
                $order = $request->route('order');
            }
        }
        
        if (!$order) {
            abort(404, 'Order is required. Please specify an order ID.');
        }
        
        // Load the client relationship and ensure it exists
        $order->load('client');
        
        if (!$order->client) {
            abort(404, 'Client information not found for this order.');
        }
        
        return view('designs.create', compact('order'));
    }

    /**
     * Store a newly uploaded design
     */
    public function store(Request $request, ?Order $order = null)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Only Designer can upload designs
        if (!$user->isDesigner()) {
            abort(403, 'Access denied. Only designers can upload designs.');
        }
        
        // Handle order parameter if not provided via route model binding
        if (!$order) {
            // Try order_id from request input
            if ($request->has('order_id')) {
                $orderId = $request->get('order_id');
                $order = Order::find($orderId);
            } else {
                // Try to find numeric value in query parameters or route parameters
                $queryParams = $request->query();
                foreach ($queryParams as $key => $value) {
                    $orderId = null;
                    if (is_numeric($key)) {
                        $orderId = $key;
                    } elseif (is_numeric($value)) {
                        $orderId = $value;
                    }
                    
                    if ($orderId) {
                        $order = Order::find($orderId);
                        if ($order) {
                            break;
                        }
                    }
                }
            }
            
            // If still no order, try to get from route parameter name
            if (!$order && $request->route() && $request->route()->hasParameter('order')) {
                $order = $request->route('order');
            }
        }
        
        if (!$order) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['order_id' => 'Order is required. Please specify an order ID.']);
        }
        
        $request->validate([
            'design_files' => 'required|array|min:1',
            'design_files.*' => 'file|mimes:png,jpg,jpeg,gif,ai,eps,pdf,psd,rar,zip,7z|max:51200', // 50MB max per file
            'design_notes' => 'nullable|string|max:1000',
        ]);

        $designFiles = [];
        
        // Handle multiple design file uploads
        if ($request->hasFile('design_files')) {
            foreach ($request->file('design_files') as $file) {
                $storedPath = StorageService::store($file, 'designs/draft');
                // Store with original filename as key for easy reference
                $designFiles[] = [
                    'path' => $storedPath,
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ];
            }
        }

        // Calculate next version number
        $maxVersion = $order->designs()->max('version') ?? 0;
        $nextVersion = $maxVersion + 1;
        
        // Validate workflow: Can only upload design if order is in "Order Approved" status
        if ($order->status !== 'Order Approved') {
            return redirect()->back()
                ->withInput()
                ->withErrors(['order_id' => "Cannot upload design. Order must be in 'Order Approved' status first. Current status: {$order->status}"]);
        }
        
        // Create design record
        // Note: design_files is cast to 'array' in model, so Laravel will auto-encode
        $design = $order->designs()->create([
            'designer_id' => $user->id,
            'design_files' => $designFiles, // Array - Laravel will auto JSON encode
            'design_notes' => $request->design_notes,
            'status' => 'Pending Review',
            'version' => $nextVersion,
        ]);

        // Update order status to Design Review
        $previousStatus = $order->status;
        $order->update(['status' => 'Design Review']);
        
        // Log status change
        OrderStatusLog::create([
            'order_id' => $order->order_id,
            'user_id' => $user->id,
            'previous_status' => $previousStatus,
            'new_status' => 'Design Review',
            'comment' => 'Design uploaded',
        ]);
        
        // Clear dashboard cache when new design is created
        Cache::forget('dashboard_stats_admin');
        Cache::forget('dashboard_pending_designs');

        return redirect()->route('designs.show', $design)
            ->with('success', 'Design uploaded successfully. Waiting for review.');
    }

    /**
     * Display the specified design
     */
    public function show(Design $design)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Only Designer, Admin, Sales Manager, SuperAdmin can view designs
        if (!$user->isDesigner() && !$user->isAdmin() && !$user->isSuperAdmin() && !$user->isSalesManager()) {
            abort(403, 'Access denied. Only designers, admins, and sales managers can view designs.');
        }
        
        // Load relationships
        $design->load(['order.client', 'order.products', 'designer', 'approvedBy', 'rejectedBy']);
        
        // Get all versions of designs for this order
        $versionHistory = Design::where('order_id', $design->order_id)
            ->orderBy('version', 'asc')
            ->orderBy('created_at', 'asc')
            ->with(['designer', 'approvedBy', 'rejectedBy'])
            ->get();
        
        return view('designs.show', compact('design', 'versionHistory'));
    }

    /**
     * Show the form for editing design
     */
    public function edit(Design $design)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Only Designer can edit designs
        if (!$user->isDesigner()) {
            abort(403, 'Access denied. Only designers can edit designs.');
        }
        
        if ($design->designer_id !== $user->id) {
            abort(403, 'You can only edit your own designs.');
        }
        
        return view('designs.edit', compact('design'));
    }

    /**
     * Update the specified design
     */
    public function update(Request $request, Design $design)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Only Designer can update designs
        if (!$user->isDesigner()) {
            abort(403, 'Access denied. Only designers can update designs.');
        }
        
        if ($design->designer_id !== $user->id) {
            abort(403, 'You can only update your own designs.');
        }
        
        $request->validate([
            'design_files' => 'nullable|array',
            'design_files.*' => 'file|mimes:png,jpg,jpeg,gif,ai,eps,pdf,psd,rar,zip,7z|max:51200', // 50MB max per file
            'design_notes' => 'nullable|string|max:1000',
        ]);

        // If design is rejected, create a new version instead of updating
        if ($design->status === 'Rejected') {
            // Create new design with incremented version
            $maxVersion = $design->order->designs()->max('version') ?? 0;
            $nextVersion = $maxVersion + 1;
            
            $designFiles = [];
            
            // Handle new file uploads
            if ($request->hasFile('design_files')) {
                foreach ($request->file('design_files') as $file) {
                    $storedPath = StorageService::store($file, 'designs/draft');
                    $designFiles[] = [
                        'path' => $storedPath,
                        'original_name' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                    ];
                }
            }
            
            // If no new files uploaded, copy from previous version
            if (empty($designFiles)) {
                $oldFiles = is_array($design->design_files) ? $design->design_files : [];
                // Handle both old format (associative array) and new format (indexed array)
                if (!empty($oldFiles)) {
                    // Check if old format (associative with keys like 'design_front')
                    if (isset($oldFiles['design_front']) || isset($oldFiles[0])) {
                        // New format (indexed array)
                        if (isset($oldFiles[0]) && is_array($oldFiles[0])) {
                            $designFiles = $oldFiles;
                        } else {
                            // Old format - convert to new format
                            foreach ($oldFiles as $key => $path) {
                                if (is_string($path)) {
                                    $designFiles[] = [
                                        'path' => $path,
                                        'original_name' => ucfirst(str_replace('_', ' ', $key)) . '.' . pathinfo($path, PATHINFO_EXTENSION),
                                        'size' => 0,
                                        'mime_type' => 'image/jpeg',
                                    ];
                                }
                            }
                        }
                    }
                }
            }
            
            // Create new design record
            $newDesign = $design->order->designs()->create([
                'designer_id' => $user->id,
                'design_files' => $designFiles,
                'design_notes' => $request->design_notes,
                'status' => 'Pending Review',
                'version' => $nextVersion,
            ]);
            
            // Update order status to Design Review (when new version uploaded after rejection)
            $previousStatus = $design->order->status;
            $design->order->update(['status' => 'Design Review']);
            
            // Log status change
            OrderStatusLog::create([
                'order_id' => $design->order->order_id,
                'user_id' => $user->id,
                'previous_status' => $previousStatus,
                'new_status' => 'Design Review',
                'comment' => 'New design version uploaded after rejection',
            ]);
            
            // Clear dashboard cache when new design version is created
            Cache::forget('dashboard_stats_admin');
            Cache::forget('dashboard_pending_designs');
            
            return redirect()->route('designs.show', $newDesign)
                ->with('success', 'New design version created successfully. Waiting for review.');
        }
        
        // For non-rejected designs, update existing design
        $designFiles = is_array($design->design_files) ? $design->design_files : [];
        
        // Handle new file uploads - add to existing files
        if ($request->hasFile('design_files')) {
            foreach ($request->file('design_files') as $file) {
                $storedPath = StorageService::store($file, 'designs/draft');
                $designFiles[] = [
                    'path' => $storedPath,
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ];
            }
        }

        // Note: design_files is cast to 'array' in model, so Laravel will auto-encode
        $design->update([
            'design_files' => $designFiles, // Array - Laravel will auto JSON encode
            'design_notes' => $request->design_notes,
            'status' => 'Pending Review',
        ]);
        
        // Clear dashboard cache when design is updated
        Cache::forget('dashboard_stats_admin');
        Cache::forget('dashboard_pending_designs');

        return redirect()->route('designs.show', $design)
            ->with('success', 'Design updated successfully. Waiting for review.');
    }

    /**
     * Approve design (Sales Manager/SuperAdmin only)
     */
    public function approve(Design $design)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Only Sales Manager and SuperAdmin can approve designs
        if (!$user->isSuperAdmin() && !$user->isSalesManager()) {
            abort(403, 'Access denied. Only sales managers and super admins can approve designs.');
        }
        
        // Validate workflow: Can only approve design if order is in "Design Review" status
        if ($design->order->status !== 'Design Review') {
            return redirect()->route('designs.show', $design)
                ->with('error', "Cannot approve design. Order must be in 'Design Review' status first. Current status: {$design->order->status}");
        }
        
        $design->update([
            'status' => 'Approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        // Update order status to Design Approved for finalization by Sales Manager
        $previousStatus = $design->order->status;
        $design->order->update(['status' => 'Design Approved']);
        
        // Log status change
        OrderStatusLog::create([
            'order_id' => $design->order->order_id,
            'user_id' => $user->id,
            'previous_status' => $previousStatus,
            'new_status' => 'Design Approved',
            'comment' => 'Design approved',
        ]);
        
        // Clear dashboard cache when design is approved
        Cache::forget('dashboard_stats_superadmin');
        Cache::forget('dashboard_stats_admin');
        Cache::forget('dashboard_stats_sales');
        Cache::forget('dashboard_pending_designs');

        return redirect()->route('designs.show', $design)
            ->with('success', 'Design approved successfully.');
    }

    /**
     * Reject design with feedback (Sales Manager/SuperAdmin only)
     */
    public function reject(Request $request, Design $design)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Only Sales Manager and SuperAdmin can reject designs
        if (!$user->isSuperAdmin() && !$user->isSalesManager()) {
            abort(403, 'Access denied. Only sales managers and super admins can reject designs.');
        }
        
        $request->validate([
            'feedback' => 'required|string|max:1000',
        ]);
        
        // Validate workflow: Can only reject design if order is in "Design Review" status
        if ($design->order->status !== 'Design Review') {
            return redirect()->route('designs.show', $design)
                ->with('error', "Cannot reject design. Order must be in 'Design Review' status first. Current status: {$design->order->status}");
        }

        $design->update([
            'status' => 'Rejected',
            'feedback' => $request->feedback,
            'rejected_by' => $user->id,
            'rejected_at' => now(),
        ]);
        
        // Note: Order status stays as "Design Review" when design is rejected
        // This allows designer to upload a new version
        
        // Clear dashboard cache when design is rejected
        Cache::forget('dashboard_stats_admin');
        Cache::forget('dashboard_pending_designs');

        return redirect()->route('designs.show', $design)
            ->with('success', 'Design rejected with feedback.');
    }

    /**
     * Update pola link for an order (Designer only)
     */
    public function updatePola(Request $request, Order $order)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Only Designer can update pola link
        if (!$user->isDesigner()) {
            abort(403, 'Access denied. Only designers can update pola links.');
        }
        
        // Validate: Order must have approved design
        $order->load('designs');
        $approvedDesigns = $order->designs->filter(function($design) {
            return strtolower($design->status) === 'approved';
        });
        
        if ($approvedDesigns->isEmpty()) {
            return redirect()->back()
                ->with('error', 'Cannot add pola link. Order must have at least one approved design first.');
        }
        
        // Validate: Order status should allow pola link editing (until production jobs end)
        $allowedStatuses = [
            'Design Approved', 
            'Job Created', 
            'Job Start', 
            'Job Complete', 
            'Order Packaging', 
            'Order Finished'
        ];
        
        if (!in_array($order->status, $allowedStatuses, true)) {
            return redirect()->back()
                ->with('error', "Cannot update pola link. Order status must allow pola editing. Current status: {$order->status}");
        }
        
        $request->validate([
            'pola_link' => 'required|url|max:500',
        ]);
        
        $order->update([
            'pola_link' => $request->pola_link,
        ]);
        
        // Clear dashboard cache
        Cache::forget('dashboard_stats_superadmin');
        Cache::forget('dashboard_stats_admin');
        Cache::forget('dashboard_stats_sales');
        
        return redirect()->back()
            ->with('success', 'Pola link updated successfully.');
    }
} 