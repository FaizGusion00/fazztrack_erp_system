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
        $order = Order::with(['client', 'jobs'])
            ->where('order_id', $orderId)
            ->firstOrFail();

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

        $order = Order::with(['client', 'jobs'])
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