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
 use Carbon\Carbon;

use App\Models\SupplierItemCatalog;
use Illuminate\Support\Str;

class PurchaseOrderController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | List
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {$query = PurchaseOrder::with([
    'supplier',
    'retailer',
    'items',
    'items.item',
    'items.supplierItemCatalog'
])->withCount('items');// 🔥 correct chaining
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
public function create(Request $request)
{
    /*
    |--------------------------------------------------------------------------
    | Manual + Marketplace दोनों support
    |--------------------------------------------------------------------------
    */

    $items = Item::all();
    $suppliers = Supplier::all();

    /*
    |--------------------------------------------------------------------------
    | Marketplace se prefill values
    |--------------------------------------------------------------------------
    */

    $selectedSupplier = $request->supplier_id;
    $selectedItem = $request->item_id;
    $selectedCatalog = $request->catalog_id;

    $prefilledCatalog = null;

    if ($selectedCatalog) {
        $prefilledCatalog = SupplierItemCatalog::with([
            'item',
            'supplier'
        ])->find($selectedCatalog);
    }

    /*
    |--------------------------------------------------------------------------
    | Return View
    |--------------------------------------------------------------------------
    */

    return view(
        'purchase_orders.create',
        compact(
            'items',
            'suppliers',
            'selectedSupplier',
            'selectedItem',
            'selectedCatalog',
            'prefilledCatalog'
        )
    );
}

    /*
    |--------------------------------------------------------------------------
    | Get Suppliers by Item (AJAX)
    |--------------------------------------------------------------------------
    */
 public function getItemSuppliers(Request $request)
{

$data = SupplierItemCatalog::with(['supplier','item'])
    ->where('item_id', $request->item_id)
    ->where('is_active', 1)
    ->where('current_stock', '>', 0)
->where(function ($q) {
    $q->whereNull('expiry_date')
      ->orWhereDate('expiry_date', '>=', now()->startOfDay());
})    ->get();

    return response()->json(
        $data->map(function ($catalog) {

            $item = $catalog->item;

            $imageUrl = null;

            if ($item && $item->main_image) {
                if (str_starts_with($item->main_image, 'http')) {
                    $imageUrl = $item->main_image;
                } else {
                    $imageUrl = \Storage::disk('s3')->url($item->main_image);
                }
            }

            $imageUrl = $imageUrl ?? asset('images/no-image.png');

            return [
                'id' => $catalog->id,
                'supplier_id' => $catalog->supplier_id,
                'item_id' => $catalog->item_id,

                // supplier internal price
                'purchase_price' => $catalog->purchase_price,

                // 🔥 retailer ko dikhne wala actual PTR
                'retailer_price' => $catalog->retailer_price,

                // MRP
                'base_price' => $catalog->base_price,

                'expiry_date' => $catalog->expiry_date,
                'stock_qty' => (int) $catalog->getRawOriginal('current_stock'),
                'image_url' => $imageUrl,

                'supplier' => [
                    'name' => $catalog->supplier?->name,
                ],

                'item' => [
                    'name' => $item?->name,
                    'image_url' => $imageUrl,
                ],
            ];
        })
    );
}
public function getSupplierItems($supplierId)
{
   $data = SupplierItemCatalog::with(['item'])
    ->where('supplier_id', $supplierId)
    ->where('is_active', 1)
    ->where('current_stock', '>', 0)
    ->where(function ($q) {
        $q->whereNull('expiry_date')
          ->orWhereDate('expiry_date', '>=', now()->startOfDay());
    })
    ->whereNotNull('item_id')
    ->get();
    return response()->json(
        $data->map(function ($catalog) {

            $item = $catalog->item;

            $imageUrl = null;

            if ($item && $item->main_image) {
                if (str_starts_with($item->main_image, 'http')) {
                    $imageUrl = $item->main_image;
                } else {
                    $imageUrl = \Storage::disk('s3')->url($item->main_image);
                }
            }

            $imageUrl = $imageUrl ?? asset('images/no-image.png');

            return [
                'id' => $catalog->id,
                'item_id' => $catalog->item_id,

                // supplier internal price
                'purchase_price' => $catalog->purchase_price,

                // 🔥 retailer ko dikhne wala actual PTR
                'retailer_price' => $catalog->retailer_price,

                // MRP
                'base_price' => $catalog->base_price,

                // 🔥 REAL DATA FROM BATCH
                'expiry_date' => $catalog->expiry_date,
                'stock_qty' => (int) $catalog->getRawOriginal('current_stock'),
                'image_url' => $imageUrl,

                'item' => [
                    'name' => $item?->name,
                    'image_url' => $imageUrl,
                ],
            ];
        })
    );
}    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'order_date' => 'required|date',
    //         'items' => 'required|array|min:1',
    //         'items.*.item_id' => 'required|exists:items,id',
    //         'items.*.supplier_item_catalog_id' => 'required|exists:supplier_item_catalogs,id',
    //         'items.*.quantity' => 'required|numeric|min:0.01',
    //         'items.*.rate' => 'required|numeric|min:0',
    //     ]);

    //     DB::beginTransaction();

    //     try {

    //         $orderNumber = 'PO-' . date('Ymd') . '-' . rand(1000,9999);

    //         // 🔥 FIRST ITEM CATALOG
    //         $firstItem = collect($request->items)
    //             ->firstWhere('supplier_item_catalog_id', '!=', null);

    //         $catalog = $firstItem
    //             ? SupplierItemCatalog::find($firstItem['supplier_item_catalog_id'])
    //             : null;

    //         // 🔥 CREATE ORDER
    //         $order = PurchaseOrder::create([
    //             'supplier_id' => $catalog ? $catalog->supplier_id : null,
    //             'retailer_id' => Auth::id(), // 🔥 LOGIN USER
    //             'order_number' => $orderNumber,
    //             'order_date' => $request->order_date,
    //             'status' => 'pending',
    //             'total_amount' => 0,
    //             'total_gst' => 0,
    //             'total_discount' => 0,
    //             'net_amount' => 0
    //         ]);

    //         $totalAmount = 0;
    //         $totalGST = 0;
    //         $totalDiscount = 0;

    //         foreach ($request->items as $item) {

    //             if (!$item['item_id'] || !$item['quantity']) continue;

    //             $catalog = SupplierItemCatalog::findOrFail($item['supplier_item_catalog_id']);

    //             $qty = $item['quantity'];
    //             $rate = $item['rate'];

    //             $basic = $qty * $rate;

    //             $discountPercent = $item['discount_percent'] ?? 0;
    //             $discount = ($basic * $discountPercent) / 100;

    //             $taxable = $basic - $discount;

    //             $gstPercent = $item['gst_percent'] ?? $catalog->gst_percent;
    //             $gst = ($taxable * $gstPercent) / 100;

    //             $total = $taxable + $gst;

    //             PurchaseOrderItem::create([
    //                 'purchase_order_id' => $order->id,
    //                 'item_id' => $item['item_id'],
    //                 'supplier_item_catalog_id' => $catalog->id,
    //                 'quantity' => $qty,
    //                 'rate' => $rate,
    //                 'gst_percent' => $gstPercent,
    //                 'gst_amount' => $gst,
    //                 'discount_percent' => $discountPercent,
    //                 'discount_amount' => $discount,
    //                 'taxable_amount' => $taxable,
    //                 'total_amount' => $total
    //             ]);

    //             $totalAmount += $basic;
    //             $totalGST += $gst;
    //             $totalDiscount += $discount;
    //         }

    //         $order->update([
    //             'total_amount' => $totalAmount,
    //             'total_gst' => $totalGST,
    //             'total_discount' => $totalDiscount,
    //             'net_amount' => ($totalAmount - $totalDiscount + $totalGST)
    //         ]);

    //         DB::commit();

    //         return redirect()->route('purchase-orders.index')
    //             ->with('success', 'Purchase Order created successfully');

    //     } catch (\Exception $e) {

    //         DB::rollBack();
    //         return back()->with('error', $e->getMessage());
    //     }
    // }
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

        // 🔥 GROUP BY SUPPLIER
        $grouped = collect($request->items)->groupBy(function ($item) {
            $catalog = SupplierItemCatalog::find($item['supplier_item_catalog_id']);
            return $catalog->supplier_id;
        });

        foreach ($grouped as $supplier_id => $items) {

            // 🔥 CREATE PO PER SUPPLIER
            $order = PurchaseOrder::create([
                'supplier_id' => $supplier_id,
                'retailer_id' => Auth::id(),
                'order_number' => 'PO-' . date('Ymd') . '-' . rand(1000,9999),
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

            foreach ($items as $item) {

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

            // 🔥 UPDATE TOTAL
            $order->update([
                'total_amount' => $totalAmount,
                'total_gst' => $totalGST,
                'total_discount' => $totalDiscount,
                'net_amount' => ($totalAmount - $totalDiscount + $totalGST)
            ]);
        }

        DB::commit();

        return redirect()->route('purchase-orders.index')
            ->with('success', 'Purchase Orders created successfully');

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

        $user = auth()->user();

        // supplier flow
        if ($user->role == 'supplier') {

            if ($po->supplier_id != $user->id) {
                abort(403, 'Unauthorized');
            }

            $allowedTransitions = [
                'pending' => 'confirmed',
                'confirmed' => 'processing',
                'processing' => 'dispatched',
            ];

            if (
                !isset($allowedTransitions[$oldStatus]) ||
                $allowedTransitions[$oldStatus] != $newStatus
            ) {
                throw new \Exception("Invalid status flow (Supplier)");
            }
        }

      

        $po->update([
            'status' => $newStatus
        ]);

        /*
        OLD AUTO PURCHASE FLOW DISABLED

        dispatched → delivered → auto purchase + auto batch

        New Flow:
        Receive Stock → Update MRP → Publish For Sale
        */

        DB::commit();

        return back()->with('success', 'Status updated successfully');

    } catch (\Exception $e) {
        DB::rollBack();

dd($e->getMessage(), $e->getLine(), $e->getFile());    }
}

public function receiveStock($id)
{
    $order = PurchaseOrder::findOrFail($id);

    $order->stock_received = true;
    $order->save();

    return back()->with('success', 'Stock Received Successfully');
}


public function updateMrp(Request $request, $id)
{
    $request->validate([
        'items' => 'required|array|min:1',

        /*
        |--------------------------------------------------------------------------
        | Every selected item must have:
        | item_id
        | final_mrp
        | conversion_factor
        |--------------------------------------------------------------------------
        */

        'items.*.item_id' => 'required|exists:items,id',
'items.*.offline_price' => 'required|numeric|min:0',
'items.*.online_price' => 'nullable|numeric|min:0',        'items.*.conversion_factor' => 'required|numeric|min:1',
    ]);

    DB::beginTransaction();

    try {

        /*
        |--------------------------------------------------------------------------
        | STEP 1 → FETCH ORDER
        |--------------------------------------------------------------------------
        */

        $order = PurchaseOrder::with('items')->findOrFail($id);

        if ($order->items->isEmpty()) {
            return back()->with(
                'error',
                'No PO items found'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | STEP 2 → ITEM-WISE MRP UPDATE
        |--------------------------------------------------------------------------
        | OLD WRONG:
        | $order->final_mrp = single value
        |
        | NEW CORRECT:
        | Each item gets separate final_mrp
        |--------------------------------------------------------------------------
        */

        foreach ($request->items as $row) {

            /*
            |--------------------------------------------------------------------------
            | Safety check
            |--------------------------------------------------------------------------
            */

            if (empty($row['item_id'])) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | STEP 2A → Update PO Item final_mrp
            |--------------------------------------------------------------------------
            */
$offline = $row['offline_price'];
$online  = $row['online_price'] ?? $offline;

PurchaseOrderItem::where([
    'purchase_order_id' => $order->id,
    'item_id' => $row['item_id'],
])->update([
    'final_mrp' => $offline, // existing logic
    'offline_price' => $offline, // NEW
    'online_price' => $online,   // NEW
]);

            /*
            |--------------------------------------------------------------------------
            | STEP 2B → Update Item conversion_factor
            |--------------------------------------------------------------------------
            | Example:
            | Crocin → 1 strip = 10 tablets
            | Dolo   → 1 strip = 15 tablets
            |--------------------------------------------------------------------------
            */

            \App\Models\Item::where(
                'id',
                $row['item_id']
            )->update([
                'conversion_factor' => $row['conversion_factor']
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | STEP 3 → MARK PO AS PRICE UPDATED
        |--------------------------------------------------------------------------
        */

        $order->update([
            'price_updated' => true
        ]);

        DB::commit();

        return back()->with(
            'success',
            'Item-wise MRP + Conversion Factor Updated Successfully'
        );

    } catch (\Exception $e) {

        DB::rollBack();

        dd(
            'ERROR => ' . $e->getMessage(),
            'LINE => ' . $e->getLine(),
            'FILE => ' . $e->getFile()
        );
    }
}
public function publishSale($id)
{
    DB::beginTransaction();

    try {

        /*
        |--------------------------------------------------------------------------
        | STEP 1 → FETCH ORDER
        |--------------------------------------------------------------------------
        */

        $order = PurchaseOrder::with(['items', 'supplier'])
            ->findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | STEP 2 → VALIDATION CHECKS
        |--------------------------------------------------------------------------
        */

        if (!$order->stock_received || !$order->price_updated) {
            return back()->with(
                'error',
                'Complete Receive Stock + Update MRP first'
            );
        }

        if ((int) $order->published_for_sale === 1) {
            return back()->with(
                'error',
                'This Purchase Order is already published. Please create new PO for new stock.'
            );
        }

        if ($order->items->isEmpty()) {
            return back()->with(
                'error',
                'No PO items found'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | STEP 3 → CREATE PURCHASE ENTRY
        |--------------------------------------------------------------------------
        */

        $purchase = \App\Models\Purchase::create([
            'supplier_id'    => $order->supplier_id,
            'invoice_number' => 'AUTO-PO-' . $order->id . '-' . time(),
            'purchase_date'  => now(),
            'payment_type'   => 'Pending',
            'entry_by'       => auth()->id(),

            'total_amount'   => 0,
            'total_gst'      => 0,
            'total_discount' => 0,
            'net_amount'     => 0,
        ]);

        if (!$purchase) {
            throw new \Exception('Purchase creation failed');
        }

        $totalAmount   = 0;
        $totalGST      = 0;
        $totalDiscount = 0;

        /*
        |--------------------------------------------------------------------------
        | STEP 4 → LOOP ALL PO ITEMS
        |--------------------------------------------------------------------------
        */

     foreach ($order->items as $poItem) {

    if (!$poItem->item_id) {
        throw new \Exception(
            'Item ID missing in PO Item ID: ' . $poItem->id
        );
    }

    $catalog = \App\Models\SupplierItemCatalog::with('item')
        ->find($poItem->supplier_item_catalog_id);

    if (!$catalog) {
        throw new \Exception(
            'Supplier catalog not found for Item ID: ' . $poItem->item_id
        );
    }

    /*
    |------------------------------------------------------------------
    | EXPIRED + OUT OF STOCK VALIDATION
    |------------------------------------------------------------------
    */

  /*
|--------------------------------------------------------------------------
| FINAL SAFE VALIDATION
|--------------------------------------------------------------------------
| PO create hone ke baad stock re-check nahi karna
| Sirf true expiry validate karni hai
|--------------------------------------------------------------------------
*/

if (
    $catalog->expiry_date &&
    \Carbon\Carbon::parse($catalog->expiry_date)
        ->lt(now()->startOfDay())
) {
    throw new \Exception(
        'Expired item cannot be published for Item ID: '
        . $poItem->item_id
    );
}

$qty = (float) ($poItem->quantity ?? 0);

if ($qty <= 0) {
    throw new \Exception(
        'Invalid quantity for Item ID: '
        . $poItem->item_id
    );
}

/*
|--------------------------------------------------------------------------
| PTR Logic
|--------------------------------------------------------------------------
| Use retailer price (actual retailer buying price)
| NOT supplier internal purchase price
|--------------------------------------------------------------------------
*/

$rate = (float) (
    $catalog->retailer_price
    ?? $poItem->rate
    ?? 0
);

if ($rate <= 0) {
    $rate = 0;
}

/*
|--------------------------------------------------------------------------
| MRP Logic
|--------------------------------------------------------------------------
| Use retailer entered final MRP
| fallback → supplier MRP only if final_mrp missing
|--------------------------------------------------------------------------
*/

$mrp = (float) (
    $catalog->base_price
    ?? $poItem->final_mrp
    ?? 0
);

if ($mrp <= 0) {
    $mrp = $rate;
}

            $gstPercent = (float) ($poItem->gst_percent ?? 0);
            $discount   = (float) ($poItem->discount_amount ?? 0);

            $basic     = $qty * $rate;
            $taxable   = $basic - $discount;
            $gstAmount = ($taxable * $gstPercent) / 100;
            $total     = $taxable + $gstAmount;

            $totalAmount   += $basic;
            $totalGST      += $gstAmount;
            $totalDiscount += $discount;

            /*
            |--------------------------------------------------------------------------
            | STEP 6 → STRIP → TABLET CONVERSION LOGIC
            |--------------------------------------------------------------------------
            | Example:
            | 1 strip = 10 tablets
            | 50 strip = 500 loose stock
            |--------------------------------------------------------------------------
            */

         

            /*
            |--------------------------------------------------------------------------
            | STEP 7 → BATCH CREATE
            |--------------------------------------------------------------------------
            */

$batch = \App\Models\Batch::create([
    'item_id' => $poItem->item_id,

    'batch_code' => 'PO-BATCH-' .
        $poItem->item_id . '-' .
        time() . rand(100, 999),

    'expiry_date' => $catalog->expiry_date
        ?? now()->addYear(),

    'stock' => $qty,
    'loose_stock' => 0,

    'mrp' => $mrp,
    'ptr' => $rate,

    // 🔥 NEW SYSTEM
   'offline_price' => $poItem->offline_price ?? $mrp,
'online_price'  => $poItem->online_price ?? $mrp,

    'created_by' => auth()->id(),
    'updated_by' => auth()->id(),
]);

            if (!$batch) {
                throw new \Exception(
                    'Batch creation failed for Item ID: ' . $poItem->item_id
                );
            }

            /*
            |--------------------------------------------------------------------------
            | STEP 8 → PURCHASE ITEM CREATE
            |--------------------------------------------------------------------------
            */

            $purchaseItem = \App\Models\PurchaseItem::create([
                'purchase_id' => $purchase->id,
                'item_id'     => $poItem->item_id,
                'batch_id'    => $batch->id,

                'quantity'      => $qty,
                'free_quantity' => 0,

                'mrp' => $mrp,
                'ptr' => $rate,

                'gst_percent' => $gstPercent,
                'gst_amount'  => $gstAmount,

                'discount_percent' => 0,
                'discount_amount'  => $discount,

                'taxable_amount' => $taxable,
                'total_amount'   => $total,
            ]);

            if (!$purchaseItem) {
                throw new \Exception(
                    'Purchase Item creation failed for Item ID: ' . $poItem->item_id
                );
            }

            /*
            |--------------------------------------------------------------------------
            | STEP 9 → STOCK MOVEMENT CREATE
            |--------------------------------------------------------------------------
            */

            $stockMovement = \App\Models\StockMovement::create([
                'item_id'  => $poItem->item_id,
                'batch_id' => $batch->id,

                'type' => 'purchase',

                'quantity'      => $qty,
                'running_stock' => $qty,

                'reference_id'   => $purchase->id,
                'reference_type' => 'Purchase',

                'user_id' => auth()->id(),

                'remarks' => 'Auto from PO #' . $order->order_number,
                'transaction_date' => now(),
            ]);

            if (!$stockMovement) {
                throw new \Exception(
                    'Stock movement failed for Item ID: ' . $poItem->item_id
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | STEP 10 → PURCHASE TOTAL UPDATE
        |--------------------------------------------------------------------------
        */

        $net = $totalAmount - $totalDiscount + $totalGST;

        $purchase->update([
            'total_amount'   => $totalAmount,
            'total_gst'      => $totalGST,
            'total_discount' => $totalDiscount,
            'net_amount'     => $net,
        ]);

        /*
        |--------------------------------------------------------------------------
        | STEP 11 → SUPPLIER LEDGER
        |--------------------------------------------------------------------------
        */

        $lastBalance = \App\Models\SupplierLedger::where(
            'supplier_id',
            $order->supplier_id
        )
        ->latest()
        ->value('balance_after') ?? 0;

        $ledger = \App\Models\SupplierLedger::create([
            'supplier_id' => $order->supplier_id,

            'reference_id'   => $purchase->id,
            'reference_type' => 'purchase',

            'debit'  => $net,
            'credit' => 0,

            'balance_after' => $lastBalance + $net,

            'transaction_date' => now(),
            'entry_by' => auth()->id(),

            'remarks' => 'Auto Purchase from PO #' . $order->order_number,
        ]);

        if (!$ledger) {
            throw new \Exception('Supplier ledger creation failed');
        }

        /*
        |--------------------------------------------------------------------------
        | STEP 12 → FINAL LOCK
        |--------------------------------------------------------------------------
        */

        $order->published_for_sale = 1;
        $order->save();
        $order->refresh();

        if ((int) $order->published_for_sale !== 1) {
            throw new \Exception(
                'published_for_sale flag not updated'
            );
        }

        DB::commit();

        return back()->with(
            'success',
            'PO Converted to Purchase Successfully'
        );

    } catch (\Exception $e) {

        DB::rollBack();

        dd(
            'ERROR => ' . $e->getMessage(),
            'LINE => ' . $e->getLine(),
            'FILE => ' . $e->getFile()
        );
    }
}
    public function convert($id)
    {
        return redirect()->route('purchase.create', [
            'po_id' => $id
        ]);
    }

    public function show($id)
{
    $po = PurchaseOrder::with(['supplier', 'items.item'])->findOrFail($id);

    return view('purchase_orders.show', compact('po'));
}

}