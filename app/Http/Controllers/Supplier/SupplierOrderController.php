<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\SupplierStock;
use App\Models\SupplierItemCatalog;
use Illuminate\Http\Request;
use App\Models\Purchase\PurchaseOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
            'items.catalog.supplier',
            'items.catalog.stocks'
        ])
            ->where('supplier_id', auth('supplier')->id())
            ->findOrFail($id);

        return view('supplier.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, $id)
    {
        $order = PurchaseOrder::where('supplier_id', auth('supplier')->id())
            ->with('items')
            ->findOrFail($id);

        $newStatus = $request->status;

        $allowed = [
            'pending' => ['confirmed', 'rejected'],
            'confirmed' => ['processing'],
            'processing' => ['dispatched'],
            'dispatched' => ['delivered'],
        ];

        if (!isset($allowed[$order->status]) || !in_array($newStatus, $allowed[$order->status])) {
            return back()->with('error', 'Invalid status change');
        }

        DB::beginTransaction();

        try {

            if ($order->status == 'pending' && $newStatus == 'confirmed') {

                foreach ($order->items as $item) {

                    $qty = (int) ($item->qty ?? $item->quantity ?? 0);

                    if ($qty <= 0)
                        continue;

                    $catalog = SupplierItemCatalog::where('supplier_id', auth('supplier')->id())
                        ->where('id', $item->supplier_item_catalog_id)
                        ->first();

                    if (!$catalog)
                        continue;

                    if ($catalog->current_stock < $qty)
                        continue;

                    $catalog->decrement('current_stock', $qty);

                    SupplierStock::create([
                        'supplier_item_catalog_id' => $catalog->id,
                        'qty' => $qty,
                        'type' => 'sale',
                        'reference_id' => $order->id,
                        'note' => 'Stock sold via order #' . $order->id
                    ]);
                }
            }

            $order->update(['status' => $newStatus]);

            DB::commit();

            Log::info('Order updated', [
                'order_id' => $order->id,
                'status' => $newStatus
            ]);

            return back()->with('success', 'Status updated + Stock managed');

        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('Order update failed', [
                'order_id' => $id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', $e->getMessage());
        }
    }
}