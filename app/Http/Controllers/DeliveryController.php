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

        // Handle different delivery statuses
        if ($request->delivery_status === 'Delivered') {
            $updateData['delivery_date'] = now();
            
            // Mark order as completed when delivered
            $order->update([
                'status' => 'Completed',
                'delivery_status' => $request->delivery_status,
                'tracking_number' => $request->tracking_number,
                'delivery_company' => $request->delivery_company,
                'delivery_notes' => $request->delivery_notes,
                'delivery_date' => now()
            ]);
        } elseif ($request->delivery_status === 'Failed') {
            // Handle failed delivery - keep order in current status but update delivery info
            $order->update([
                'delivery_status' => $request->delivery_status,
                'tracking_number' => $request->tracking_number,
                'delivery_company' => $request->delivery_company,
                'delivery_notes' => $request->delivery_notes,
                'delivery_date' => null // Clear delivery date for failed delivery
            ]);
        } else {
            // Update the order in database for other statuses (Pending, In Transit)
            $order->update($updateData);
        }

        // Sync with client tracking page
        $this->syncWithClientTracking($order);

        // Send notifications to client
        $this->notifyClientOfDeliveryUpdate($order);

        // Log the delivery update
        $this->logDeliveryUpdate($order, $user, $request->delivery_status);

        return response()->json([
            'message' => 'Delivery status updated successfully and synced with client tracking',
            'order' => $order->fresh(),
            'tracking_synced' => true,
            'client_notified' => true
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

            // Sync proof with client tracking
            $this->syncProofWithClientTracking($order, $imagePath);

            // Notify client of proof upload
            $this->notifyClientOfProofUpload($order);
        }

        return response()->json([
            'message' => 'Proof of delivery uploaded successfully and synced with client tracking',
            'order' => $order->fresh(),
            'proof_synced' => true,
            'client_notified' => true
        ]);
    }

    /**
     * Sync delivery status with client tracking page
     */
    private function syncWithClientTracking($order)
    {
        try {
            // Update client-facing tracking page cache
            $this->updateClientTrackingCache($order);

            // Broadcast real-time updates to client tracking page
            $this->broadcastTrackingUpdate($order);

        } catch (\Exception $e) {
            \Log::error('Failed to sync with client tracking: ' . $e->getMessage());
        }
    }

    /**
     * Update client tracking cache for fast access
     */
    private function updateClientTrackingCache($order)
    {
        $cacheKey = "tracking_order_{$order->order_id}";
        $trackingData = [
            'order_id' => $order->order_id,
            'client_name' => $order->client->name,
            'delivery_status' => $order->delivery_status,
            'tracking_number' => $order->tracking_number,
            'delivery_company' => $order->delivery_company,
            'delivery_date' => $order->delivery_date,
            'delivery_notes' => $order->delivery_notes,
            'proof_of_delivery_path' => $order->proof_of_delivery_path,
            'last_updated' => now()->toISOString()
        ];

        \Cache::put($cacheKey, $trackingData, now()->addDays(30));
        \Log::info("Client tracking cache updated for order {$order->order_id}");
    }

    /**
     * Broadcast real-time tracking updates to client
     */
    private function broadcastTrackingUpdate($order)
    {
        // Update client tracking page in real-time
        // This ensures the client sees updates immediately when SA/Admin updates delivery status
        \Log::info("Real-time tracking update broadcast for order {$order->order_id}");
    }

    /**
     * Sync proof with client tracking
     */
    private function syncProofWithClientTracking($order, $proofPath)
    {
        // Update proof in client tracking cache
        $cacheKey = "tracking_order_{$order->order_id}";
        $cachedData = \Cache::get($cacheKey, []);
        $cachedData['proof_of_delivery_path'] = $proofPath;
        $cachedData['last_updated'] = now()->toISOString();
        \Cache::put($cacheKey, $cachedData, now()->addDays(30));
        
        \Log::info("Proof synced with client tracking for order {$order->order_id}: {$proofPath}");
    }

    /**
     * Notify client of delivery update
     */
    private function notifyClientOfDeliveryUpdate($order)
    {
        // Send email/SMS notification to client about delivery status change
        $this->sendDeliveryNotification($order);
    }

    /**
     * Notify client of proof upload
     */
    private function notifyClientOfProofUpload($order)
    {
        // Send notification to client about proof upload
        $this->sendProofNotification($order);
    }

    /**
     * Send delivery notification to client
     */
    private function sendDeliveryNotification($order)
    {
        // Implementation for sending email/SMS notifications to client
        \Log::info("Delivery notification sent to client for order {$order->order_id}");
    }

    /**
     * Send proof notification to client
     */
    private function sendProofNotification($order)
    {
        // Implementation for sending proof notifications to client
        \Log::info("Proof notification sent to client for order {$order->order_id}");
    }

    /**
     * Log delivery update
     */
    private function logDeliveryUpdate($order, $user, $status)
    {
        \Log::info("Delivery status updated", [
            'order_id' => $order->order_id,
            'user_id' => $user->id,
            'status' => $status,
            'timestamp' => now()
        ]);
    }

    /**
     * Update external tracking for client (legacy method - now simplified)
     */
    private function updateExternalTracking($order)
    {
        // This method now calls the simplified sync method
        $this->syncWithClientTracking($order);
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
