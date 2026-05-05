<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Batch;
use App\Models\Supplier;
use App\Models\Item;
use App\Models\SupplierLedger;
use App\Models\StockMovement;

class PurchaseController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Generate Next Invoice Number
    |--------------------------------------------------------------------------
    */
//  private function generateNextInvoiceNumber()
// {
//     return DB::transaction(function () {
//         // Get all invoice numbers and find the maximum
//         $maxInvoiceNumber = Purchase::whereNotNull('invoice_number')
//             ->where('invoice_number', '!=', '')
//             ->get()
//             ->map(function ($purchase) {
//                 return (int) $purchase->invoice_number;
//             })
//             ->max();
        
//         if (!$maxInvoiceNumber || $maxInvoiceNumber == 0) {
//             return 1;
//         }
        
//         return $maxInvoiceNumber + 1;
//     });
// }
    
    /*
    |--------------------------------------------------------------------------
    | Purchase List
    |--------------------------------------------------------------------------
    */
public function index(Request $request)
{
    $query = Purchase::with('supplier', 'user')
        ->latest();
    
    // Search by invoice number
    if ($request->filled('search')) {
        $query->where('invoice_number', 'LIKE', '%' . $request->search . '%');
    }
    
    // Filter by supplier
    if ($request->filled('supplier_id')) {
        $query->where('supplier_id', $request->supplier_id);
    }
    
    // Filter by date range
if ($request->filled('from_date')) {
    $query->whereDate('purchase_date', '>=', $request->from_date);
}

if ($request->filled('to_date')) {
    $query->whereDate('purchase_date', '<=', $request->to_date);
}
    
    // Calculate summary statistics
    $totalPurchases = $query->count();
    $totalAmount = $query->sum('net_amount');
    $totalGST = $query->sum('total_gst');
    $totalDiscount = $query->sum('total_discount');
    
    // Get paginated results
    $purchases = $query->paginate(10);
    
    // Get suppliers for filter dropdown
    $suppliers = Supplier::orderBy('name')->get();
    
    return view('purchase.index', compact('purchases', 'suppliers', 
        'totalPurchases', 'totalAmount', 'totalGST', 'totalDiscount'));
}

    /*
    |--------------------------------------------------------------------------
    | Create Purchase
    |--------------------------------------------------------------------------
    */
public function create()
{
    $suppliers = Supplier::all();

    $items = Item::with('packings')
        ->select(
            'id',
            'name',
            'gst_percent',
            'barcode',
            'rack',
            'hsn_code'
        )
        ->get();

    return view('purchase.create', compact(
        'suppliers',
        'items'
    ));
}
    /*
    |--------------------------------------------------------------------------
    | AJAX : Get Item Details
    |--------------------------------------------------------------------------
    */
    public function getItem($id)
{
    $item = Item::with('packings')->findOrFail($id);

    return response()->json([
        'gst_percent' => $item->gst_percent ?? 0,
        'barcode' => $item->barcode ?? '',
        'rack' => $item->rack ?? '',
        'hsn_code' => $item->hsn_code ?? '',

        // Batch-based system → default manual entry
        'mrp' => 0,
        'purchase_rate' => 0,

        // packing from item_packings table
        'packing' => optional(
            $item->packings->first()
        )->packaging_detail ?? '',
    ]);
}
    

    /*
    |--------------------------------------------------------------------------
    | Store Purchase
    |--------------------------------------------------------------------------
    */
public function store(Request $request)
{
    $request->validate([
        'supplier_id' => 'required|exists:suppliers,id',
        'invoice_number' => 'required|string|max:255|unique:purchases,invoice_number',
        'purchase_date' => 'required|date',
        'payment_type' => 'nullable|string|max:50',
        'extra_charges' => 'nullable|numeric|min:0',
        'round_off' => 'nullable|numeric',
        'items' => 'required|array|min:1',

        'items.*.item_id' => 'required|exists:items,id',
        'items.*.quantity' => 'required|numeric|min:0.01',
        'items.*.free_quantity' => 'nullable|numeric|min:0',
        'items.*.batch_number' => 'required|string|max:255',
        'items.*.expiry_date' => 'required|date|after_or_equal:today',
        'items.*.purchase_rate' => 'required|numeric|min:0',
        'items.*.mrp' => 'required|numeric|min:0',

        'items.*.gst_percent' => 'nullable|numeric|min:0|max:100',
        'items.*.discount_percent' => 'nullable|numeric|min:0|max:100',
        'items.*.discount_amount' => 'nullable|numeric|min:0',

        'items.*.barcode' => 'nullable|string|max:255',
        'items.*.rack' => 'nullable|string|max:255',
        'items.*.hsn_code' => 'nullable|string|max:255',
    ]);

    DB::beginTransaction();

    try {

        /*
        |--------------------------------------------------------------------------
        | Invoice Lock
        |--------------------------------------------------------------------------
        */

        $exists = Purchase::where('invoice_number', $request->invoice_number)
            ->lockForUpdate()
            ->exists();

        if ($exists) {
            throw new \Exception(
                "Invoice number already exists. Refresh and try again."
            );
        }

        $totalAmount = 0;
        $totalGST = 0;
        $totalDiscount = 0;

        /*
        |--------------------------------------------------------------------------
        | Create Purchase
        |--------------------------------------------------------------------------
        */

        $purchase = Purchase::create([
            'supplier_id' => $request->supplier_id,
            'invoice_number' => $request->invoice_number,
            'purchase_date' => $request->purchase_date ?? now(),
            'payment_type' => $request->payment_type ?? 'Pending',
            'entry_by' => auth()->id(),

            'total_amount' => 0,
            'net_amount' => 0,
            'total_gst' => 0,
            'total_discount' => 0,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Loop Items
        |--------------------------------------------------------------------------
        */

        foreach ($request->items as $itemData) {

            $qty = (float) $itemData['quantity'];
            $freeQty = (float) ($itemData['free_quantity'] ?? 0);

            // strip qty
            $totalQty = $qty + $freeQty;

            if ($totalQty <= 0) {
                continue;
            }

            $rate = (float) $itemData['purchase_rate'];
            $mrp = (float) $itemData['mrp'];
            $gst = (float) ($itemData['gst_percent'] ?? 0);

            $discP = (float) ($itemData['discount_percent'] ?? 0);
            $discA = (float) ($itemData['discount_amount'] ?? 0);

            /*
            |--------------------------------------------------------------------------
            | Calculations
            |--------------------------------------------------------------------------
            */

            $basic = $qty * $rate;

            if ($discA == 0 && $discP > 0) {
                $discA = ($basic * $discP) / 100;
            }

            $discA = min($discA, $basic);

            $taxable = $basic - $discA;
            $gstAmount = ($taxable * $gst) / 100;
            $total = $taxable + $gstAmount;

            $totalAmount += $total;
            $totalGST += $gstAmount;
            $totalDiscount += $discA;

            /*
            |--------------------------------------------------------------------------
            | Batch Logic
            |--------------------------------------------------------------------------
            */

            $batchCode = trim($itemData['batch_number']);
            $expiry = $itemData['expiry_date'];

            if (\Carbon\Carbon::parse($expiry)->isPast()) {
                throw new \Exception(
                    "Batch {$batchCode} expired: {$expiry}"
                );
            }

            /*
            |--------------------------------------------------------------------------
            | STRIP → TABLET CONVERSION
            |--------------------------------------------------------------------------
            | Example:
            | 50 strip × 10 = 500 tablets
            |--------------------------------------------------------------------------
            */

            $itemMaster = Item::find($itemData['item_id']);

            $conversionFactor = (int) (
                $itemMaster->conversion_factor ?? 10
            );

        

            /*
            |--------------------------------------------------------------------------
            | Batch Find
            |--------------------------------------------------------------------------
            */

            $batch = Batch::where('item_id', $itemData['item_id'])
                ->where('batch_code', $batchCode)
                ->whereDate('expiry_date', $expiry)
                ->lockForUpdate()
                ->first();

            if ($batch) {

                /*
                |--------------------------------------------------------------------------
                | Existing Batch Update
                |--------------------------------------------------------------------------
                */

                // strip stock
               $batch->increment('stock', $totalQty);

$batch->mrp = $mrp;
$batch->ptr = $rate;
$batch->selling_price = $mrp;
$batch->save();
            } else {

                /*
                |--------------------------------------------------------------------------
                | New Batch Create
                |--------------------------------------------------------------------------
                */

                $batch = Batch::create([
                    'item_id' => $itemData['item_id'],
                    'batch_code' => $batchCode,
                    'expiry_date' => $expiry,

                    // strip stock
                    'stock' => $totalQty,

                    // loose tablet stock
'loose_stock' => 0,
                    'mrp' => $mrp,
                    'ptr' => $rate,
                    'selling_price' => $mrp,

                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Purchase Item Create
            |--------------------------------------------------------------------------
            */

            PurchaseItem::create([
                'purchase_id' => $purchase->id,
                'item_id' => $itemData['item_id'],
                'batch_id' => $batch->id,

                'quantity' => $qty,
                'free_quantity' => $freeQty,

                'mrp' => $mrp,
                'ptr' => $rate,

                'gst_percent' => $gst,
                'gst_amount' => $gstAmount,

                'discount_percent' => $discP,
                'discount_amount' => $discA,

                'taxable_amount' => $taxable,
                'total_amount' => $total,

                'barcode' => $itemData['barcode'] ?? null,
                'rack' => $itemData['rack'] ?? null,
                'hsn_code' => $itemData['hsn_code'] ?? null,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Stock Movement
            |--------------------------------------------------------------------------
            */

            StockMovement::create([
                'item_id' => $itemData['item_id'],
                'batch_id' => $batch->id,

                'type' => 'purchase',

                'quantity' => $totalQty,
                'running_stock' => $batch->stock,

                'reference_id' => $purchase->id,
                'reference_type' => 'Purchase',

                'user_id' => auth()->id(),

                'remarks' => "Purchase #{$purchase->invoice_number} - {$batchCode}",
                'transaction_date' => $request->purchase_date ?? now(),
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Totals
        |--------------------------------------------------------------------------
        */

        $extra = (float) ($request->extra_charges ?? 0);
        $round = (float) ($request->round_off ?? 0);

        $net = $totalAmount + $extra + $round;

        $purchase->update([
            'total_amount' => $totalAmount,
            'total_gst' => $totalGST,
            'total_discount' => $totalDiscount,
            'net_amount' => $net,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Supplier Ledger
        |--------------------------------------------------------------------------
        */

        $lastBalance = SupplierLedger::where(
            'supplier_id',
            $request->supplier_id
        )
        ->latest()
        ->value('balance_after') ?? 0;

        SupplierLedger::create([
            'supplier_id' => $request->supplier_id,

            'reference_id' => $purchase->id,
            'reference_type' => 'purchase',

            'debit' => $net,
            'credit' => 0,

            'balance_after' => $lastBalance + $net,

            'transaction_date' => $request->purchase_date ?? now(),
            'entry_by' => auth()->id(),

            'remarks' => "Purchase #{$request->invoice_number}",
        ]);

        DB::commit();

        return redirect()
            ->route('purchase.index')
            ->with(
                'success',
                "Purchase #{$request->invoice_number} created successfully."
            );

    } catch (\Exception $e) {

        DB::rollBack();

        return back()
            ->with(
                'error',
                'Failed to create purchase: ' . $e->getMessage()
            )
            ->withInput();
    }
}
public function update(Request $request, $id)
{
    DB::beginTransaction();

    try {
        $purchase = Purchase::with('items')->findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | STEP 1: OLD STOCK REVERSE
        |--------------------------------------------------------------------------
        */
        foreach ($purchase->items as $oldItem) {

            $batch = Batch::lockForUpdate()->find($oldItem->batch_id);

            if ($batch) {
                $totalQty = $oldItem->quantity + ($oldItem->free_quantity ?? 0);

                $batch->stock -= $totalQty;
                $batch->save();

             StockMovement::create([
    'item_id' => $oldItem->item_id,
    'batch_id' => $batch->id,
'type' => 'purchase',
    'quantity' => -$totalQty,
    'running_stock' => $batch->stock, // 🔥 ADD THIS
    'reference_id' => $purchase->id,
    'reference_type' => 'PurchaseEdit',
    'user_id' => auth()->id(),
    'remarks' => 'Reversed due to purchase edit',
    'transaction_date' => now()
]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | STEP 2: DELETE OLD ITEMS
        |--------------------------------------------------------------------------
        */
        $purchase->items()->delete();

        /*
        |--------------------------------------------------------------------------
        | STEP 3: RE-CREATE PURCHASE (Same as store logic)
        |--------------------------------------------------------------------------
        */

        $totalAmount = 0;
        $totalGST = 0;
        $totalDiscount = 0;

        foreach ($request->items as $itemData) {

            $qty = (float) $itemData['quantity'];
            $freeQty = (float) ($itemData['free_quantity'] ?? 0);
            $totalQty = $qty + $freeQty;

            $rate = (float) $itemData['purchase_rate'];
            $mrp = (float) $itemData['mrp'];
            $gst = (float) ($itemData['gst_percent'] ?? 0);

            $basic = $qty * $rate;
            $gstAmount = ($basic * $gst) / 100;
            $total = $basic + $gstAmount;

            $totalAmount += $total;
            $totalGST += $gstAmount;

            // Batch
            $batch = Batch::where('item_id', $itemData['item_id'])
                ->where('batch_code', $itemData['batch_number'])
                ->lockForUpdate()
                ->first();

            if ($batch) {
                $batch->increment('stock', $totalQty);
            }

            // Purchase Item
            PurchaseItem::create([
                'purchase_id' => $purchase->id,
                'item_id' => $itemData['item_id'],
                'batch_id' => $batch->id,

                'quantity' => $qty,
                'free_quantity' => $freeQty,

                'mrp' => $mrp,
                'ptr' => $rate,

                'gst_percent' => $gst,
                'gst_amount' => $gstAmount,

                'taxable_amount' => $basic,
                'total_amount' => $total,
            ]);

            // Stock Movement
            StockMovement::create([
                'item_id' => $itemData['item_id'],
                'batch_id' => $batch->id,
'type' => 'purchase',
                'quantity' => $totalQty,
                'reference_id' => $purchase->id,
                'reference_type' => 'PurchaseEdit',
                'user_id' => auth()->id(),
                'remarks' => 'Purchase updated',
                'transaction_date' => now(),
                'running_stock' => $batch->stock,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | STEP 4: UPDATE PURCHASE TOTALS
        |--------------------------------------------------------------------------
        */

        $purchase->update([
            'supplier_id' => $request->supplier_id,
'purchase_date' => $request->purchase_date ?? $purchase->purchase_date,
                'payment_type' => $request->payment_type, // 🔥 ADD THIS

            'total_amount' => $totalAmount,
            'total_gst' => $totalGST,
            'net_amount' => $totalAmount,
        ]);

        DB::commit();

        return redirect()->route('purchase.index')
            ->with('success', 'Purchase updated successfully');

    } catch (\Exception $e) {
        DB::rollBack();

        return back()->with('error', $e->getMessage());
    }
}
public function edit($id)
{
    $purchase = Purchase::with([
        'items.item',
        'items.batch',
        'supplier'
    ])->findOrFail($id);

    $suppliers = Supplier::all();
    $items = Item::select('id', 'name', 'gst_percent')->get();

    return view('purchase.edit', compact(
        'purchase',
        'suppliers',
        'items'
    ));
}

    /*
    |--------------------------------------------------------------------------
    | Show Purchase
    |--------------------------------------------------------------------------
    */
    public function show(Purchase $purchase)
    {
        $purchase->load([
            'supplier',
            'items.item',
            'items.batch'
        ]);

        return view('purchase.show', compact('purchase'));
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Purchase
    |--------------------------------------------------------------------------
    */
    public function destroy(Purchase $purchase)
    {
        DB::beginTransaction();

        try {
            foreach ($purchase->items as $item) {
                $batch = Batch::where('id', $item->batch_id)
                    ->lockForUpdate()
                    ->first();

                if ($batch) {
                    $totalQuantity = $item->quantity + ($item->free_quantity ?? 0);

                    if ($batch->stock < $totalQuantity) {
                        throw new \Exception("Stock mismatch for batch {$batch->batch_code}. Current stock: {$batch->stock}, Attempting to deduct: {$totalQuantity}");
                    }

                    $batch->stock -= $totalQuantity;
                    $batch->save();

                   StockMovement::create([
    'item_id' => $item->item_id,
    'batch_id' => $batch->id,
'type' => 'purchase',
    'quantity' => -$totalQuantity,
    'running_stock' => $batch->stock, // 🔥 ADD THIS
    'reference_id' => $purchase->id,
    'reference_type' => 'PurchaseDelete',
    'user_id' => auth()->id(),
    'remarks' => 'Stock reversed due to purchase deletion',
'transaction_date' => $purchase->purchase_date ?? now()]);
                }
            }

            $lastBalance = SupplierLedger::where('supplier_id', $purchase->supplier_id)
                ->orderBy('id', 'desc')
                ->value('balance_after') ?? 0;

            SupplierLedger::create([
                'supplier_id' => $purchase->supplier_id,
                'reference_id' => $purchase->id,
                'reference_type' => 'purchase_delete',
                'debit' => 0,
                'credit' => $purchase->net_amount,
                'balance_after' => $lastBalance - $purchase->net_amount,
                'transaction_date' => now(),
                'entry_by' => auth()->id(),
                'remarks' => 'Purchase deleted - Reversal entry'
            ]);

            $purchase->items()->delete();
            $purchase->delete();

            DB::commit();

            return redirect()->route('purchase.index')
                ->with('success', 'Purchase deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Failed to delete purchase: ' . $e->getMessage());
        }
    }
public function searchItems(Request $request)
{
    try {
        $search = $request->get('search', '');

        if (!$search || strlen($search) < 2) {
            return response()->json([]);
        }

        // normalize input
        $search = strtolower(str_replace('-', ' ', $search));
        $words = explode(' ', $search);

        $query = Item::query();

        foreach ($words as $word) {
            $query->whereRaw(
                "REPLACE(LOWER(name), '-', ' ') LIKE ?", 
                ["%{$word}%"]
            );
        }

        $items = $query->limit(10)
            ->get(['id', 'name', 'gst_percent']);

        return response()->json($items);

    } catch (\Exception $e) {
        \Log::error('Search Error: ' . $e->getMessage());
        return response()->json([]);
    }
}
}