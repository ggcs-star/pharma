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
                'status' => 'draft',
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
    public function convert($id)
    {
        return redirect()->route('purchase.create', [
            'po_id' => $id
        ]);
    }
}