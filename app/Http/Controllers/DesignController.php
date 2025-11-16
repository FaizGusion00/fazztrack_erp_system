<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Design;
use App\Services\StorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class DesignController extends Controller
{
    /**
     * Display a listing of designs
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Only Designer, Admin, Sales Manager, SuperAdmin can access
        if (!$user->isDesigner() && !$user->isAdmin() && !$user->isSuperAdmin() && !$user->isSalesManager()) {
            abort(403, 'Access denied. Only designers, admins, and sales managers can access this page.');
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
        
        return view('designs.index', compact('designs', 'pendingCount', 'approvedCount', 'rejectedCount', 'view'));
    }

    /**
     * Show the form for uploading design for an order
     */
    public function create(Request $request, Order $order = null)
    {
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
    public function store(Request $request, Order $order = null)
    {
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
            'design_front' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'design_back' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'design_left' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'design_right' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'design_notes' => 'nullable|string|max:1000',
        ]);

        $designFiles = [];
        
        // Handle design file uploads
        $designFields = ['design_front', 'design_back', 'design_left', 'design_right'];
        foreach ($designFields as $field) {
            if ($request->hasFile($field)) {
                $designFiles[$field] = StorageService::store($request->file($field), 'designs/draft');
            }
        }

        // Calculate next version number
        $maxVersion = $order->designs()->max('version') ?? 0;
        $nextVersion = $maxVersion + 1;
        
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
        $order->update(['status' => 'Design Review']);

        return redirect()->route('designs.show', $design)
            ->with('success', 'Design uploaded successfully. Waiting for review.');
    }

    /**
     * Display the specified design
     */
    public function show(Design $design)
    {
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
        $user = Auth::user();
        
        // Only Designer can update designs
        if (!$user->isDesigner()) {
            abort(403, 'Access denied. Only designers can update designs.');
        }
        
        if ($design->designer_id !== $user->id) {
            abort(403, 'You can only update your own designs.');
        }
        
        $request->validate([
            'design_front' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'design_back' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'design_left' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'design_right' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'design_notes' => 'nullable|string|max:1000',
        ]);

        // If design is rejected, create a new version instead of updating
        if ($design->status === 'Rejected') {
            // Create new design with incremented version
            $maxVersion = $design->order->designs()->max('version') ?? 0;
            $nextVersion = $maxVersion + 1;
            
            $designFiles = [];
            
            // Handle design file uploads
            $designFields = ['design_front', 'design_back', 'design_left', 'design_right'];
            foreach ($designFields as $field) {
                if ($request->hasFile($field)) {
                    $designFiles[$field] = StorageService::store($request->file($field), 'designs/draft');
                } else {
                    // Copy from previous version if not uploaded
                    $oldFiles = json_decode($design->design_files, true) ?: [];
                    if (isset($oldFiles[$field])) {
                        $designFiles[$field] = $oldFiles[$field];
                    }
                }
            }
            
            // Create new design record
            // Note: design_files is cast to 'array' in model, so Laravel will auto-encode
            $newDesign = $design->order->designs()->create([
                'designer_id' => $user->id,
                'design_files' => $designFiles, // Array - Laravel will auto JSON encode
                'design_notes' => $request->design_notes,
                'status' => 'Pending Review',
                'version' => $nextVersion,
            ]);
            
            // Update order status to Design Review
            $design->order->update(['status' => 'Design Review']);
            
            return redirect()->route('designs.show', $newDesign)
                ->with('success', 'New design version created successfully. Waiting for review.');
        }
        
        // For non-rejected designs, update existing design
        $designFiles = json_decode($design->design_files, true) ?: [];
        
        // Handle design file uploads
        $designFields = ['design_front', 'design_back', 'design_left', 'design_right'];
        foreach ($designFields as $field) {
            if ($request->hasFile($field)) {
                // Delete old file if exists
                if (isset($designFiles[$field])) {
                    StorageService::delete($designFiles[$field]);
                }
                $designFiles[$field] = StorageService::store($request->file($field), 'designs/draft');
            }
        }

        // Note: design_files is cast to 'array' in model, so Laravel will auto-encode
        $design->update([
            'design_files' => $designFiles, // Array - Laravel will auto JSON encode
            'design_notes' => $request->design_notes,
            'status' => 'Pending Review',
        ]);

        return redirect()->route('designs.show', $design)
            ->with('success', 'Design updated successfully. Waiting for review.');
    }

    /**
     * Approve design (Sales Manager/SuperAdmin only)
     */
    public function approve(Design $design)
    {
        $user = Auth::user();
        
        // Only Sales Manager and SuperAdmin can approve designs
        if (!$user->isSuperAdmin() && !$user->isSalesManager()) {
            abort(403, 'Access denied. Only sales managers and super admins can approve designs.');
        }
        
        $design->update([
            'status' => 'Approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        // Update order status to Design Approved for finalization by Sales Manager
        $design->order->update(['status' => 'Design Approved']);

        return redirect()->route('designs.show', $design)
            ->with('success', 'Design approved successfully.');
    }

    /**
     * Reject design with feedback (Sales Manager/SuperAdmin only)
     */
    public function reject(Request $request, Design $design)
    {
        $user = Auth::user();
        
        // Only Sales Manager and SuperAdmin can reject designs
        if (!$user->isSuperAdmin() && !$user->isSalesManager()) {
            abort(403, 'Access denied. Only sales managers and super admins can reject designs.');
        }
        
        $request->validate([
            'feedback' => 'required|string|max:1000',
        ]);

        $design->update([
            'status' => 'Rejected',
            'feedback' => $request->feedback,
            'rejected_by' => $user->id,
            'rejected_at' => now(),
        ]);

        return redirect()->route('designs.show', $design)
            ->with('success', 'Design rejected with feedback.');
    }
} 