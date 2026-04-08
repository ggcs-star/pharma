<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\SupplierStock;
use App\Models\SupplierItemCatalog;
use Illuminate\Http\Request;
use App\Models\Purchase\PurchaseOrder;

class SupplierOrderController extends Controller
{

    public function index()
    {
        $orders = PurchaseOrder::with(['retailer', 'items.item', 'items.catalog'])
            ->where('supplier_id', auth('supplier')->id())
            ->latest()
            ->get();

        return view('supplier.orders.index', compact('orders'));
    }
   public function show($id)
{
    $order = PurchaseOrder::with([
        'retailer',
        'items.item',
        'items.catalog.supplier',   // 🔥 add this
        'items.catalog.stocks'      // 🔥 optional (stock history)
    ])
    ->where('supplier_id', auth('supplier')->id())
    ->findOrFail($id);

    return view('supplier.orders.show', compact('order'));
}

public function updateStatus(Request $request, $id)
{
    $order = PurchaseOrder::where('supplier_id', auth('supplier')->id())
        ->findOrFail($id);

    $newStatus = $request->status;

    // 🔥 Allowed transitions
    $allowed = [
        'pending' => ['confirmed', 'rejected'],
        'confirmed' => ['processing'],
        'processing' => ['dispatched'],
        'dispatched' => ['delivered'],
    ];

    if (!isset($allowed[$order->status]) || !in_array($newStatus, $allowed[$order->status])) {
        return back()->with('error', 'Invalid status change');
    }

    $order->update(['status' => $newStatus]);

    return back()->with('success', 'Status updated');
}
}