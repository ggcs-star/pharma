<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\Purchase\PurchaseOrder;
use App\Models\Purchase\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\Item;
use App\Models\SupplierItemCatalog;

class PurchaseOrderController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | List
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $query = PurchaseOrder::with(['supplier','retailer']);

        if ($request->from_date && $request->to_date) {
            $query->whereBetween('order_date', [
                $request->from_date,
                $request->to_date
            ]);
        }

        if ($request->search) {
            $query->where('order_number', 'like', '%' . $request->search . '%');
        }

        // 🔥 OPTIONAL: retailer wise filter
        $query->where('retailer_id', Auth::id());

        $orders = $query->latest()->paginate(10);

        return view('purchase_orders.index', compact('orders'));
    }

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        $items = Item::all();

        return view('purchase_orders.create', compact('items'));
    }

    /*
    |--------------------------------------------------------------------------
    | Get Suppliers by Item (AJAX)
    |--------------------------------------------------------------------------
    */
    public function getItemSuppliers(Request $request)
    {
        return SupplierItemCatalog::with('supplier')
            ->where('item_id', $request->item_id)
            ->where('is_active', 1)
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'order_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.supplier_item_catalog_id' => 'required|exists:supplier_item_catalogs,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.rate' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {

            $orderNumber = 'PO-' . date('Ymd') . '-' . rand(1000,9999);

            // 🔥 FIRST ITEM CATALOG
            $firstItem = collect($request->items)
                ->firstWhere('supplier_item_catalog_id', '!=', null);

            $catalog = $firstItem
                ? SupplierItemCatalog::find($firstItem['supplier_item_catalog_id'])
                : null;

            // 🔥 CREATE ORDER
            $order = PurchaseOrder::create([
                'supplier_id' => $catalog ? $catalog->supplier_id : null,
                'retailer_id' => Auth::id(), // 🔥 LOGIN USER
                'order_number' => $orderNumber,
                'order_date' => $request->order_date,
                'status' => 'pending',
                'total_amount' => 0,
                'total_gst' => 0,
                'total_discount' => 0,
                'net_amount' => 0
            ]);

            $totalAmount = 0;
            $totalGST = 0;
            $totalDiscount = 0;

            foreach ($request->items as $item) {

                if (!$item['item_id'] || !$item['quantity']) continue;

                $catalog = SupplierItemCatalog::findOrFail($item['supplier_item_catalog_id']);

                $qty = $item['quantity'];
                $rate = $item['rate'];

                $basic = $qty * $rate;

                $discountPercent = $item['discount_percent'] ?? 0;
                $discount = ($basic * $discountPercent) / 100;

                $taxable = $basic - $discount;

                $gstPercent = $item['gst_percent'] ?? $catalog->gst_percent;
                $gst = ($taxable * $gstPercent) / 100;

                $total = $taxable + $gst;

                PurchaseOrderItem::create([
                    'purchase_order_id' => $order->id,
                    'item_id' => $item['item_id'],
                    'supplier_item_catalog_id' => $catalog->id,
                    'quantity' => $qty,
                    'rate' => $rate,
                    'gst_percent' => $gstPercent,
                    'gst_amount' => $gst,
                    'discount_percent' => $discountPercent,
                    'discount_amount' => $discount,
                    'taxable_amount' => $taxable,
                    'total_amount' => $total
                ]);

                $totalAmount += $basic;
                $totalGST += $gst;
                $totalDiscount += $discount;
            }

            $order->update([
                'total_amount' => $totalAmount,
                'total_gst' => $totalGST,
                'total_discount' => $totalDiscount,
                'net_amount' => ($totalAmount - $totalDiscount + $totalGST)
            ]);

            DB::commit();

            return redirect()->route('purchase-orders.index')
                ->with('success', 'Purchase Order created successfully');

        } catch (\Exception $e) {

            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Convert PO → Purchase
    |--------------------------------------------------------------------------
    */
    public function updateStatus(Request $request, $id)
{
    DB::beginTransaction();

    try {
        $po = PurchaseOrder::with('items')->findOrFail($id);

        $oldStatus = $po->status;
        $newStatus = $request->status;

        // 🔥 UPDATE STATUS
        $po->update([
            'status' => $newStatus
        ]);

        /*
        |--------------------------------------------------------------------------
        | 🔥 AUTO PURCHASE WHEN DELIVERED
        |--------------------------------------------------------------------------
        */
        if ($oldStatus == 'dispatched' && $newStatus == 'delivered') {

            // 🚨 DUPLICATE CHECK
            if (\App\Models\Purchase::where('purchase_order_id', $po->id)->exists()) {
                throw new \Exception("Purchase already created for this PO");
            }

            $purchase = \App\Models\Purchase::create([
                'supplier_id' => $po->supplier_id,
                'invoice_number' => 'PO-' . $po->id . '-' . time(),
                'entry_source' => 'po',
                'purchase_order_id' => $po->id,
                'purchase_date' => now(),
                'payment_type' => 'Pending',
                'entry_by' => auth()->id(),
            ]);

            $totalAmount = 0;
            $totalGST = 0;

            foreach ($po->items as $poItem) {

                $catalog = \App\Models\SupplierItemCatalog::find($poItem->supplier_item_catalog_id);

                if (!$catalog) continue;

                $qty = $poItem->quantity;
                $free = $catalog->free_qty ?? 0;
                $totalQty = $qty + $free;

                $basic = $qty * $catalog->purchase_price;
                $gstAmount = ($basic * $catalog->gst_percent) / 100;
                $total = $basic + $gstAmount;

                $totalAmount += $total;
                $totalGST += $gstAmount;

                // 📦 BATCH
                $batch = \App\Models\Batch::create([
                    'item_id' => $poItem->item_id,
                    'batch_code' => $catalog->batch_no,
                    'expiry_date' => $catalog->expiry_date,
                    'stock' => $totalQty,
                    'mrp' => $catalog->retailer_mrp,
                    'ptr' => $catalog->purchase_price,
                    'selling_price' => $catalog->retailer_price,
                    'created_by' => auth()->id(),
                ]);

                // 🧾 PURCHASE ITEM
                \App\Models\PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'item_id' => $poItem->item_id,
                    'batch_id' => $batch->id,
                    'quantity' => $qty,
                    'free_quantity' => $free,
                    'mrp' => $catalog->retailer_mrp,
                    'ptr' => $catalog->purchase_price,
                    'gst_percent' => $catalog->gst_percent,
                    'gst_amount' => $gstAmount,
                    'taxable_amount' => $basic,
                    'total_amount' => $total,
                ]);

                // 📊 STOCK
                \App\Models\StockMovement::create([
                    'item_id' => $poItem->item_id,
                    'batch_id' => $batch->id,
                    'type' => 'purchase',
                    'quantity' => $totalQty,
                    'running_stock' => $batch->stock,
                    'reference_id' => $purchase->id,
                    'reference_type' => 'Purchase',
                    'user_id' => auth()->id(),
                    'transaction_date' => now(),
                ]);
            }

            // 💰 TOTAL UPDATE
            $purchase->update([
                'total_amount' => $totalAmount,
                'total_gst' => $totalGST,
                'net_amount' => $totalAmount,
            ]);
        }

        DB::commit();

        return back()->with('success', 'Status updated successfully');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', $e->getMessage());
    }
}
    public function convert($id)
    {
        return redirect()->route('purchase.create', [
            'po_id' => $id
        ]);
    }
}