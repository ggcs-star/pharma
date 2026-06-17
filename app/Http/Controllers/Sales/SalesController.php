<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\Sale;
use App\Models\SalesItem;
use App\Models\Batch;
use App\Models\Item;
use App\Models\Customer;
use App\Models\Doctor;
use App\Models\CustomerLedger;
use App\Models\StockMovement;

class SalesController extends Controller
{

    // =========================
    // SALES LIST
    // =========================
    public function index(Request $request)
    {
        $sales = Sale::with(['items.item', 'customer', 'doctor'])
            ->when($request->search, function ($query, $search) {
                $query->where('bill_number', 'LIKE', "%{$search}%")
                    ->orWhereHas('customer', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate(20);

        return view('sales.index', compact('sales'));
    }

    // =========================
    // CREATE PAGE
    // =========================
    public function create()
    {
      $items = Item::with(['batches' => function ($q) {
        $q->where(function ($query) {
                $query->where('stock', '>', 0)
                      ->orWhere('loose_stock', '>', 0);
            })
          ->whereDate('expiry_date', '>=', now());
    }])
    ->whereHas('batches', function ($q) {
        $q->where(function ($query) {
                $query->where('stock', '>', 0)
                      ->orWhere('loose_stock', '>', 0);
            })
          ->whereDate('expiry_date', '>=', now());
    })
    ->orderBy('name')
    ->get();

        $customers = Customer::orderBy('name')->get();
        $doctors   = Doctor::orderBy('name')->get();

        return view('sales.create', compact('items', 'customers', 'doctors'));
    }
public function getItemDetails($id)
{
    $item = Item::with('packing')->findOrFail($id);

    return response()->json([
        'pack_type' => $item->packing?->packaging_detail ?? '',
        'gst' => $item->gst_percent ?? 0,

        // 🔥 MOST IMPORTANT
        'conversion_factor' => $item->conversion_factor ?? 1,
    ]);
}
    // =========================
    // STORE SALE
    // =========================
// public function store(Request $request)
// {
//     $validated = $request->validate([
//         'bill_date' => 'required|date',
//         'customer_id' => 'nullable|exists:customers,id',
//         'doctor_id' => 'nullable|exists:doctors,id',
//         'payment_type' => 'required',
//         'items' => 'required|array|min:1',

//         'items.*.item_id' => 'required|exists:items,id',
//         'items.*.batch_id' => 'required|exists:batches,id',
//         'items.*.qty' => 'required|numeric|min:1',
//         'items.*.sale_type' => 'required|in:strip,loose',
//         'items.*.selling_price' => 'required|numeric|min:0',
//     ]);

//     DB::beginTransaction();

//     try {

//         // 🔥 AUTO BILL NUMBER
//         $billNumber = $this->generateBillNumber();

//         $subtotal = 0;
//         $totalGST = 0;
//         $grandTotal = 0;

//         // =========================
//         // CREATE SALE
//         // =========================
//         $sale = Sale::create([
//             'bill_number' => $billNumber,
//             'bill_date' => $validated['bill_date'],
//             'customer_id' => $validated['customer_id'] ?? null,
//             'doctor_id' => $validated['doctor_id'] ?? null,
//             'payment_type' => $validated['payment_type'],
//             'entry_by' => Auth::id(),
//             'status' => 'completed'
//         ]);

//         $itemsData = [];

//         foreach ($validated['items'] as $row) {

//             $item = Item::findOrFail($row['item_id']);
//             $batch = Batch::with('item')->findOrFail($row['batch_id']);

//             $qty = $row['qty'];
//             $saleType = $row['sale_type'];

//             $conversionFactor = $item->conversion_factor ?? 1;

//             /*
//             ========================================
//             STRIP SALE
//             ========================================
//             */

//             if ($saleType === 'strip') {

//                 if ($batch->stock < $qty) {
//                     throw new \Exception("Stock not available for {$item->name}");
//                 }

//                 $base = $qty * $row['selling_price'];

//                 // strip stock reduce
//                 $batch->reduceStock($qty);

//                 $movementUnit = 'Strip';
//                 $unitQty = $qty;
//             }

//             /*
//             ========================================
//             LOOSE TABLET SALE
//             ========================================
//             */

//             else {

//                 if ($batch->loose_stock < $qty) {
//                     throw new \Exception("Loose stock not available for {$item->name}");
//                 }

//                 // Per Tablet Price = Strip Price / Conversion Factor
//                 $perTabletPrice = $row['selling_price'] / $conversionFactor;

//                 $base = $perTabletPrice * $qty;

//                 // loose stock reduce
//                 $batch->reduceLooseStock($qty);

//                 $movementUnit = 'Tablet';
//                 $unitQty = $qty;
//             }

//             /*
//             ========================================
//             GST CALCULATION
//             ========================================
//             */

//             $gstPercent = $item->gst_percent ?? 0;
//             $gstAmount = ($base * $gstPercent) / 100;

//             $final = $base + $gstAmount;

//             /*
//             ========================================
//             SALES ITEM SAVE
//             ========================================
//             */

//             $itemsData[] = [
//                 'sale_id' => $sale->id,
//                 'item_id' => $item->id,
//                 'batch_id' => $batch->id,

//                 // strip equivalent qty
//                 'quantity' => $saleType === 'strip'
//                     ? $qty
//                     : ($qty / $conversionFactor),

//                 'sale_type' => $saleType,
//                 'unit_qty' => $unitQty,

//                 'selling_price' => $row['selling_price'],
//                 'mrp' => $batch->mrp,
//                 'discount' => 0,

//                 'gst' => $gstPercent,
//                 'gst_amount' => $gstAmount,
//                 'amount' => $final,

//                 'created_at' => now(),
//                 'updated_at' => now()
//             ];

//             /*
//             ========================================
//             STOCK MOVEMENT
//             ========================================
//             */

//             StockMovement::create([
//                 'item_id' => $item->id,
//                 'batch_id' => $batch->id,

//                 'type' => 'sale',
//                 'sale_type' => $saleType,
//                 'movement_unit' => $movementUnit,

//                 'quantity' => -$unitQty,

//                 'running_stock' => $saleType === 'strip'
//                     ? $batch->fresh()->stock
//                     : $batch->fresh()->loose_stock,

//                 'reference_id' => $sale->id,
//                 'reference_type' => Sale::class,

//                 'user_id' => Auth::id(),
//                 'remarks' => 'Sale #' . $billNumber,
//             ]);

//             $subtotal += $base;
//             $totalGST += $gstAmount;
//             $grandTotal += $final;
//         }

//         /*
//         ========================================
//         SAVE SALES ITEMS
//         ========================================
//         */

//         SalesItem::insert($itemsData);

//         /*
//         ========================================
//         UPDATE SALE TOTALS
//         ========================================
//         */

//         $sale->update([
//             'total_amount' => $subtotal,
//             'gst' => $totalGST,
//             'net_amount' => $grandTotal
//         ]);

//         /*
//         ========================================
//         CUSTOMER LEDGER
//         ========================================
//         */

//         if (
//             $validated['payment_type'] == 'credit' &&
//             !empty($validated['customer_id'])
//         ) {

//             $lastBalance = CustomerLedger::where(
//                 'customer_id',
//                 $validated['customer_id']
//             )->latest()->value('balance') ?? 0;

//             $newBalance = $lastBalance + $grandTotal;

//             CustomerLedger::create([
//                 'customer_id' => $validated['customer_id'],
//                 'reference_id' => $sale->id,
//                 'type' => 'sale',
//                 'debit' => $grandTotal,
//                 'credit' => 0,
//                 'balance' => $newBalance,
//                 'transaction_date' => now(),
//                 'description' => 'Sale #' . $sale->bill_number,
//                 'created_by' => Auth::id(),
//             ]);
//         }

//         DB::commit();

//         return redirect()
//             ->route('sales.index')
//             ->with('success', 'Sale Saved Successfully');

//     } catch (\Exception $e) {

//         DB::rollBack();

//         return back()->with('error', $e->getMessage());
//     }
// }
public function store(Request $request)
{
    $validated = $request->validate([
        'bill_date' => 'required|date',
        'customer_id' => 'nullable|exists:customers,id',
        'doctor_id' => 'nullable|exists:doctors,id',
        'payment_type' => 'required',
        'items' => 'required|array|min:1',

        'items.*.item_id' => 'required|exists:items,id',
        'items.*.batch_id' => 'required|exists:batches,id',
        'items.*.qty' => 'required|numeric|min:1',
        'items.*.sale_type' => 'required|in:strip,loose',
        'items.*.selling_price' => 'required|numeric|min:0',
    ]);

    DB::beginTransaction();

    try {

        /*
        |--------------------------------------------------------------------------
        | AUTO BILL NUMBER
        |--------------------------------------------------------------------------
        */

        $billNumber = $this->generateBillNumber();

        $subtotal = 0;
        $totalGST = 0;
        $grandTotal = 0;

        /*
        |--------------------------------------------------------------------------
        | CREATE SALE MASTER
        |--------------------------------------------------------------------------
        */

        $sale = Sale::create([
            'bill_number'   => $billNumber,
            'bill_date'     => $validated['bill_date'],
            'customer_id'   => $validated['customer_id'] ?? null,
            'doctor_id'     => $validated['doctor_id'] ?? null,
            'payment_type'  => $validated['payment_type'],
            'entry_by'      => Auth::id(),
            'status'        => 'completed',
        ]);

        $itemsData = [];

        /*
        |--------------------------------------------------------------------------
        | LOOP ALL ITEMS
        |--------------------------------------------------------------------------
        */

        foreach ($validated['items'] as $row) {

            $item = Item::findOrFail($row['item_id']);
            $batch = Batch::with('item')->findOrFail($row['batch_id']);

            $qty = (float) $row['qty'];
            $saleType = $row['sale_type'];

            $conversionFactor = $item->conversion_factor ?? 1;

/*
|--------------------------------------------------------------------------
| ONLY OFFLINE PRICE
|--------------------------------------------------------------------------
*/

$price = $batch->offline_price ?? 0;

$base = 0;
$movementUnit = '';
$unitQty = 0;

            /*
            |--------------------------------------------------------------------------
            | STRIP SALE
            |--------------------------------------------------------------------------
            */

          if ($saleType === 'strip') {

    if ($batch->stock < $qty) {
        throw new \Exception("Stock not available for {$item->name}");
    }

    $base = $qty * $price;

    $batch->reduceStock($qty);

    $movementUnit = 'Strip';
    $unitQty = $qty;
}

/* =========================
   LOOSE SALE
========================= */
else {

    $packSize = $item->pack_qty ?? 10;

    $looseQty = (int) $qty;

    if ($batch->loose_stock < $looseQty) {

        $required = $looseQty - $batch->loose_stock;

        $stripToBreak = ceil($required / $packSize);

        if ($batch->stock < $stripToBreak) {
            throw new \Exception("Insufficient stock for {$item->name}");
        }

        $batch->stock -= $stripToBreak;

        $batch->loose_stock += $stripToBreak * $packSize;
    }

    $batch->loose_stock -= $looseQty;

    /*
    |--------------------------------------------------------------------------
    | PER TABLET PRICE
    |--------------------------------------------------------------------------
    */

    $perTabletPrice = $price / $packSize;

    /*
    |--------------------------------------------------------------------------
    | FINAL AMOUNT
    |--------------------------------------------------------------------------
    */

    $base = $perTabletPrice * $looseQty;

    /*
    |--------------------------------------------------------------------------
    | SAVE TABLET PRICE
    |--------------------------------------------------------------------------
    */

    $price = $perTabletPrice;

    $movementUnit = 'Tablet';

    $unitQty = $looseQty;
}
$batch->save(); 
         /*
|--------------------------------------------------------------------------
| GST CALCULATION
|--------------------------------------------------------------------------
*/

$purchaseItem = \App\Models\PurchaseItem::where('batch_id', $batch->id)
    ->first();

$gstPercent = $purchaseItem->gst_percent ?? 0;

$gstAmount = ($base * $gstPercent) / 100;

$finalAmount = $base + $gstAmount;

            /*
            |--------------------------------------------------------------------------
            | SALES ITEM SAVE DATA
            |--------------------------------------------------------------------------
            */

            $itemsData[] = [
                'sale_id' => $sale->id,
                'item_id' => $item->id,
                'batch_id' => $batch->id,

                /*
                |--------------------------------------------------------------------------
                | IMPORTANT FIX
                |--------------------------------------------------------------------------
                | Save ACTUAL quantity
                |
                | strip → 2 strip = 2
                | loose → 10 tablets = 10
                |
                | NOT:
                | 10 / 10 = 1
                |--------------------------------------------------------------------------
                */

                'quantity' => $qty,

                'sale_type' => $saleType,
                'unit_qty' => $unitQty,

                /*
                | Unit price
                */

'selling_price' => $price,
                /*
                | Batch MRP
                */

    'mrp' => $batch->offline_price ?? $price,

                'discount' => 0,

                /*
                | GST
                */

                'gst' => $gstPercent,
                'gst_amount' => $gstAmount,

                /*
                | Final line total
                */

                'amount' => $finalAmount,

                'created_at' => now(),
                'updated_at' => now(),
            ];

            /*
            |--------------------------------------------------------------------------
            | STOCK MOVEMENT
            |--------------------------------------------------------------------------
            */

            // StockMovement::create([
            //     'item_id' => $item->id,
            //     'batch_id' => $batch->id,

            //     'type' => 'sale',
            //     'sale_type' => $saleType,
            //     'movement_unit' => $movementUnit,

            //     'quantity' => -$unitQty,

            //     'running_stock' => $saleType === 'strip'
            //         ? $batch->fresh()->stock
            //         : $batch->fresh()->loose_stock,

            //     'reference_id' => $sale->id,
            //     'reference_type' => Sale::class,

            //     'user_id' => Auth::id(),
            //     'remarks' => 'Sale #' . $billNumber,
            // ]);
        StockMovement::create([
    'item_id' => $item->id,
    'batch_id' => $batch->id,

    'type' => 'sale',

    // FIX THIS
    'direction' => 'out',

    'sale_type' => $saleType,
    'movement_unit' => $movementUnit,

    'quantity' => -$unitQty,

    'running_stock' => $saleType === 'strip'
        ? $batch->fresh()->stock
        : $batch->fresh()->loose_stock,

    'reference_id' => $sale->id,
    'reference_type' => Sale::class,

    'user_id' => Auth::id(),
    'remarks' => 'Sale #' . $billNumber,
]);

            /*
            |--------------------------------------------------------------------------
            | TOTALS
            |--------------------------------------------------------------------------
            */

            $subtotal += $base;
            $totalGST += $gstAmount;
            $grandTotal += $finalAmount;
        }

        /*
        |--------------------------------------------------------------------------
        | INSERT SALES ITEMS
        |--------------------------------------------------------------------------
        */

        SalesItem::insert($itemsData);

        /*
        |--------------------------------------------------------------------------
        | UPDATE SALE TOTALS
        |--------------------------------------------------------------------------
        */

        $sale->update([
            'total_amount' => $subtotal,
            'gst' => $totalGST,
            'net_amount' => $grandTotal,
        ]);

        /*
        |--------------------------------------------------------------------------
        | CUSTOMER LEDGER (ONLY CREDIT SALE)
        |--------------------------------------------------------------------------
        */

        if (
            $validated['payment_type'] == 'credit' &&
            !empty($validated['customer_id'])
        ) {

            $lastBalance = CustomerLedger::where(
                'customer_id',
                $validated['customer_id']
            )->latest()->value('balance') ?? 0;

            $newBalance = $lastBalance + $grandTotal;

            CustomerLedger::create([
                'customer_id' => $validated['customer_id'],
                'reference_id' => $sale->id,
                'type' => 'sale',
                'debit' => $grandTotal,
                'credit' => 0,
                'balance' => $newBalance,
                'transaction_date' => now(),
                'description' => 'Sale #' . $sale->bill_number,
                'created_by' => Auth::id(),
            ]);
        }

        DB::commit();

        return redirect()
            ->route('sales.index')
            ->with('success', 'Sale Saved Successfully');

    } catch (\Exception $e) {

        DB::rollBack();

        return back()->with(
            'error',
            $e->getMessage()
        );
    }
}
    // =========================
    // SHOW SALE
    // =========================
    public function show($id)
    {
        $sale = Sale::with(['items.item', 'items.batch', 'customer', 'doctor'])
            ->findOrFail($id);

        return view('sales.show', compact('sale'));
    }

    // =========================
    // AUTO BILL NUMBER
    // =========================
    private function generateBillNumber()
    {
        return DB::transaction(function () {

            $last = Sale::lockForUpdate()
                ->orderByDesc('id')
                ->first();

            if (!$last || !$last->bill_number) {
                return 'SAL-0001';
            }

            $number = (int) preg_replace('/[^0-9]/', '', $last->bill_number);
            $next = $number + 1;

            return 'SAL-' . str_pad($next, 4, '0', STR_PAD_LEFT);
        });
    }
}