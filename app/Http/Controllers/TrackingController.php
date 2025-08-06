<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    /**
     * Show public tracking page
     */
    public function show($orderId)
    {
        $order = Order::with(['client', 'jobs.assignedUser', 'product'])
            ->where('order_id', $orderId)
            ->firstOrFail();

        // If it's an AJAX request, return JSON for live updates
        if (request()->ajax()) {
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
                'delivery' => [
                    'status' => $order->delivery_status,
                    'tracking_number' => $order->tracking_number,
                    'delivery_company' => $order->delivery_company,
                    'delivery_date' => $order->delivery_date ? $order->delivery_date->format('M d, Y H:i') : null,
                    'delivery_notes' => $order->delivery_notes,
                    'proof_of_delivery_path' => $order->proof_of_delivery_path
                ]
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

        $order = Order::with(['client', 'jobs.assignedUser'])
            ->where('order_id', $request->order_id)
            ->first();

        if (!$order) {
            return back()->withErrors(['order_id' => 'Order not found']);
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
} 