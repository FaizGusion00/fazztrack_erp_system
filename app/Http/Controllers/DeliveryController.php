<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeliveryController extends Controller
{
    /**
     * Display a listing of deliveries
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Check role-based access
        if (!$user->isSuperAdmin() && !$user->isAdmin() && !$user->isSalesManager()) {
            abort(403, 'Access denied. You do not have permission to view deliveries.');
        }
        
        $query = Order::with(['client', 'product'])
            ->where('status', 'Order Finished')
            ->whereNotNull('delivery_status');
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
                  ->orWhere('tracking_number', 'like', "%{$search}%")
                  ->orWhere('delivery_company', 'like', "%{$search}%")
                  ->orWhereHas('client', function($clientQuery) use ($search) {
                      $clientQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Filter by delivery status
        if ($request->filled('delivery_status')) {
            $query->where('delivery_status', $request->delivery_status);
        }
        
        // Filter by delivery method
        if ($request->filled('delivery_method')) {
            $query->where('delivery_method', $request->delivery_method);
        }
        
        $deliveries = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        
        return view('deliveries.index', compact('deliveries'));
    }

    /**
     * Show delivery details
     */
    public function show(Order $order)
    {
        $user = Auth::user();
        
        // Check role-based access
        if (!$user->isSuperAdmin() && !$user->isAdmin() && !$user->isSalesManager()) {
            abort(403, 'Access denied. You do not have permission to view delivery details.');
        }
        
        $order->load(['client', 'product', 'jobs']);
        return view('deliveries.show', compact('order'));
    }

    /**
     * Update delivery status
     */
    public function updateDeliveryStatus(Request $request, Order $order)
    {
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

        // Update external tracking if shipping
        if ($order->delivery_method === 'Shipping' && $request->delivery_status === 'In Transit') {
            // This will update the client-facing tracking page
            $this->updateExternalTracking($order);
        }

        return response()->json([
            'message' => 'Delivery status updated successfully',
            'order' => $order->fresh(),
        ]);
    }

    /**
     * Upload proof of delivery
     */
    public function uploadProof(Request $request, Order $order)
    {
        $user = Auth::user();
        
        // Check role-based access
        if (!$user->isSuperAdmin() && !$user->isAdmin() && !$user->isSalesManager()) {
            abort(403, 'Access denied. You do not have permission to upload proof of delivery.');
        }
        
        $request->validate([
            'proof_of_delivery' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        if ($request->hasFile('proof_of_delivery')) {
            $image = $request->file('proof_of_delivery');
            $imageName = 'proof_' . time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('delivery_proofs', $imageName, 'public');
            
            $order->update(['proof_of_delivery_path' => $imagePath]);
        }

        return response()->json([
            'message' => 'Proof of delivery uploaded successfully',
            'order' => $order->fresh(),
        ]);
    }

    /**
     * Update external tracking for client
     */
    private function updateExternalTracking($order)
    {
        // This method will update the client-facing tracking page
        // The tracking page will show delivery updates for shipping orders
        // Implementation depends on how you want to handle external tracking
    }

    /**
     * Get delivery statistics
     */
    public function getStats()
    {
        $user = Auth::user();
        
        // Check role-based access
        if (!$user->isSuperAdmin() && !$user->isAdmin() && !$user->isSalesManager()) {
            abort(403, 'Access denied. You do not have permission to view delivery statistics.');
        }
        
        $stats = [
            'total_deliveries' => Order::where('status', 'Order Finished')->count(),
            'pending_deliveries' => Order::where('delivery_status', 'Pending')->where('status', 'Order Finished')->count(),
            'in_transit' => Order::where('delivery_status', 'In Transit')->where('status', 'Order Finished')->count(),
            'delivered' => Order::where('delivery_status', 'Delivered')->where('status', 'Order Finished')->count(),
            'failed_deliveries' => Order::where('delivery_status', 'Failed')->where('status', 'Order Finished')->count(),
            'shipping_orders' => Order::where('delivery_method', 'Shipping')->where('status', 'Order Finished')->count(),
            'self_collect' => Order::where('delivery_method', 'Self Collect')->where('status', 'Order Finished')->count(),
        ];
        
        return response()->json($stats);
    }

    /**
     * Export deliveries data
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        
        // Check role-based access
        if (!$user->isSuperAdmin() && !$user->isAdmin() && !$user->isSalesManager()) {
            abort(403, 'Access denied. You do not have permission to export delivery data.');
        }
        
        $query = Order::with(['client', 'product'])
            ->where('status', 'Order Finished')
            ->whereNotNull('delivery_status');
        
        // Apply filters
        if ($request->filled('delivery_status')) {
            $query->where('delivery_status', $request->delivery_status);
        }
        
        if ($request->filled('delivery_method')) {
            $query->where('delivery_method', $request->delivery_method);
        }
        
        $deliveries = $query->get();
        
        $filename = 'deliveries_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($deliveries) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Order ID', 'Client', 'Product', 'Delivery Method', 'Delivery Status', 
                'Tracking Number', 'Delivery Company', 'Delivery Date', 'Delivery Notes', 'Created Date'
            ]);
            
            foreach ($deliveries as $delivery) {
                fputcsv($file, [
                    $delivery->order_id,
                    $delivery->client->name ?? 'N/A',
                    $delivery->product->name ?? 'N/A',
                    $delivery->delivery_method,
                    $delivery->delivery_status,
                    $delivery->tracking_number ?? 'N/A',
                    $delivery->delivery_company ?? 'N/A',
                    $delivery->delivery_date ? $delivery->delivery_date->format('Y-m-d H:i:s') : 'N/A',
                    $delivery->delivery_notes ?? 'N/A',
                    $delivery->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
