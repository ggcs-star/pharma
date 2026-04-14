<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request; // ✅ FIX

class OrderController extends Controller
{
    // All Orders List
public function index(Request $request)
{
    $query = Order::with('user');

    // 📅 Date Filter
    if ($request->from_date) {
        $query->whereDate('created_at', '>=', $request->from_date);
    }

    if ($request->to_date) {
        $query->whereDate('created_at', '<=', $request->to_date);
    }

    // 📦 Status Filter
    if ($request->status) {
        $query->where('status', $request->status);
    }

    // 💊 Prescription Filter
    if ($request->rx == 'yes') {
        $query->whereNotNull('prescription_id');
    }

    if ($request->rx == 'no') {
        $query->whereNull('prescription_id');
    }

    $orders = $query->latest()->paginate(10);

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