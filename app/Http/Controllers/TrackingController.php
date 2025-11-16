<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TrackingController extends Controller
{
    /**
     * Show public tracking page
     */
    public function show($orderId)
    {
        // Trim whitespace to handle potential input issues
        $orderId = trim($orderId);
        
        $order = Order::with(['client', 'jobs.assignedUser', 'product'])
            ->where('order_id', $orderId)
            ->first();
        
        // If order not found, show a custom 404 page with helpful information
        if (!$order) {
            abort(404, "Order #{$orderId} not found. Please check your order ID and try again.");
        }

        // If it's an AJAX request, return JSON for live updates
        if (request()->ajax()) {
            // Get cached tracking data for faster response
            $cachedTracking = Cache::get("tracking_order_{$order->order_id}");
            
            return response()->json([
                'order' => $order,
                'jobs' => $order->jobs,
                'progress' => [
                    'total_phases' => 6,
                    'completed' => $order->jobs->where('status', 'Completed')->count(),
                    'in_progress' => $order->jobs->where('status', 'In Progress')->count(),
                    'pending' => $order->jobs->where('status', 'Pending')->count(),
                    'percentage' => $order->jobs->where('status', 'Completed')->count() / 6 * 100
                ],
                'payment' => [
                    'design_deposit' => $order->design_deposit,
                    'production_deposit' => $order->production_deposit,
                    'balance_payment' => $order->balance_payment,
                    'total_amount' => $order->total_amount,
                    'payment_status' => $order->payment_status,
                    'paid_amount' => $order->paid_amount,
                    'remaining_balance' => $order->total_amount - $order->paid_amount,
                    'last_payment_date' => $order->last_payment_date ? $order->last_payment_date->format('M d, Y H:i') : null,
                    'payment_due_date' => $order->payment_due_date ? $order->payment_due_date->format('M d, Y') : null,
                ],
                'delivery' => [
                    'status' => $order->delivery_status,
                    'tracking_number' => $order->tracking_number,
                    'delivery_company' => $order->delivery_company,
                    'delivery_date' => $order->delivery_date ? $order->delivery_date->format('M d, Y H:i') : null,
                    'delivery_notes' => $order->delivery_notes,
                    'proof_of_delivery_path' => $order->proof_of_delivery_path,
                    'last_updated' => $cachedTracking['last_updated'] ?? now()->toISOString(),
                    'real_time_sync' => true
                ],
                'tracking_synced' => !empty($cachedTracking),
                'client_tracking' => $this->getClientTrackingInfo($order)
            ]);
        }

        return view('tracking.show', compact('order'));
    }

    /**
     * Search order by order ID
     */
    public function search(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string',
        ]);

        // Trim whitespace and convert to integer to handle string inputs
        $orderId = trim($request->order_id);

        $order = Order::with(['client', 'jobs.assignedUser'])
            ->where('order_id', $orderId)
            ->first();

        if (!$order) {
            // Provide helpful error message with suggestions
            $nearbyOrders = Order::whereBetween('order_id', [max(1, $orderId - 5), $orderId + 5])
                ->pluck('order_id')
                ->take(5)
                ->implode(', ');
            
            $errorMessage = "Order #{$orderId} not found.";
            if ($nearbyOrders) {
                $errorMessage .= " Did you mean one of these: {$nearbyOrders}?";
            }
            
            return back()
                ->withInput()
                ->withErrors(['order_id' => $errorMessage]);
        }

        return redirect()->route('tracking.show', $order->order_id);
    }

    /**
     * Show tracking search page
     */
    public function searchForm()
    {
        return view('tracking.search');
    }

    /**
     * Get client tracking information
     */
    private function getClientTrackingInfo($order)
    {
        if (!$order->tracking_number || !$order->delivery_company) {
            return null;
        }

        return [
            'tracking_number' => $order->tracking_number,
            'delivery_company' => $order->delivery_company,
            'tracking_url' => $this->generateTrackingUrl($order),
            'last_updated' => now()->toISOString()
        ];
    }

    /**
     * Generate tracking URL for client
     */
    private function generateTrackingUrl($order)
    {
        $trackingUrls = [
            'PosLaju' => "https://www.poslaju.com.my/track-trace-v2/?tracking_number={$order->tracking_number}",
            'DHL' => "https://www.dhl.com/track?tracking-id={$order->tracking_number}",
            'FedEx' => "https://www.fedex.com/tracking?tracknumbers={$order->tracking_number}",
            'J&T' => "https://www.jtexpress.com.my/tracking?tracking_number={$order->tracking_number}"
        ];

        return $trackingUrls[$order->delivery_company] ?? null;
    }

    /**
     * Get real-time tracking updates
     */
    public function getTrackingUpdates($orderId)
    {
        $order = Order::findOrFail($orderId);
        
        // Get cached tracking data
        $cachedTracking = \Cache::get("tracking_order_{$order->order_id}");
        
        return response()->json([
            'order_id' => $order->order_id,
            'delivery_status' => $order->delivery_status,
            'tracking_number' => $order->tracking_number,
            'delivery_company' => $order->delivery_company,
            'last_updated' => $cachedTracking['last_updated'] ?? now()->toISOString(),
            'sync_status' => 'active',
            'client_tracking' => $this->getClientTrackingInfo($order)
        ]);
    }
} 