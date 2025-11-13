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
        
        $query = Order::with(['client', 'designs.designer']);
        
        // Filter based on user role
        if ($user->isDesigner()) {
            // Designer sees orders with approved payment or orders in design review
            $query->where(function($q) {
                $q->where('status', 'Order Approved')
                  ->orWhere('status', 'Design Review');
            });
        } else {
            // Admin/Sales Manager/SuperAdmin can see all orders that need design work
            $query->where(function($q) {
                $q->where('status', 'Order Approved')
                  ->orWhere('status', 'Design Review')
                  ->orWhere('status', 'Design Approved');
            });
        }
        
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
        return view('designs.index', compact('orders'));
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

        // Create design record
        $design = $order->designs()->create([
            'designer_id' => $user->id,
            'design_files' => json_encode($designFiles),
            'design_notes' => $request->design_notes,
            'status' => 'Pending Review',
            'version' => $order->designs()->count() + 1,
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
        $design->load(['order.client', 'designer', 'approvedBy', 'rejectedBy']);
        
        return view('designs.show', compact('design'));
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

        $design->update([
            'design_files' => json_encode($designFiles),
            'design_notes' => $request->design_notes,
            'status' => 'Pending Review',
        ]);

        return redirect()->route('designs.show', $design)
            ->with('success', 'Design updated successfully. Waiting for review.');
    }

    /**
     * Approve design (Admin/Sales Manager/SuperAdmin)
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
     * Reject design with feedback (Admin/Sales Manager/SuperAdmin)
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