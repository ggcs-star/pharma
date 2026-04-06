<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request; // ✅ FIX

class OrderController extends Controller
{
    // All Orders List
    public function index()
    {
        $orders = Order::with('user')
            ->latest()
            ->get();

        return view('admin.orders.index', compact('orders'));
    }

    // Order Details
    public function show($id)
    {
        $order = Order::with([
            'user',
            'items.item',
            'address'
        ])->findOrFail($id);

        return view('admin.orders.show', compact('order'));
    }

    // Update Status
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required'
        ]);

        $order = Order::findOrFail($id);

        $order->update([
            'status' => $request->status
        ]);

        return back()->with('success', 'Order status updated');
    }
}