<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Batch;
use App\Models\PurchaseReturn;

use App\Services\PurchaseReturnService;

class PurchaseReturnController extends Controller
{

    /*
    |----------------------------------------------------------
    | LIST PAGE
    |----------------------------------------------------------
    */
    public function index()
    {
        $returns = PurchaseReturn::with('supplier', 'purchase')
            ->latest()
            ->paginate(20);

        return view('purchase_returns.index', compact('returns'));
    }


    /*
    |----------------------------------------------------------
    | CREATE FORM (PURCHASE BASED ✅)
    |----------------------------------------------------------
    */
    public function create()
    {
        $purchaseItems = PurchaseItem::with([
            'item',
            'batch',
            'purchase.supplier'
        ])
        ->whereRaw('quantity > COALESCE(returned_quantity, 0)')
        ->get();

        return view('purchase_returns.create', compact('purchaseItems'));
    }


    /*
    |----------------------------------------------------------
    | GET PURCHASE ITEM (AJAX 🔥)
    |----------------------------------------------------------
    */
    public function getPurchaseItem($id)
    {
        $pi = PurchaseItem::with([
            'item',
            'batch',
            'purchase.supplier'
        ])->findOrFail($id);

        return response()->json([
            'purchase_item_id' => $pi->id,
            'item_id'     => $pi->item_id,
            'batch_id'    => $pi->batch_id,
            'batch_code'  => $pi->batch->batch_code,
            'supplier'    => $pi->purchase->supplier->name,
            'invoice'     => $pi->purchase->invoice_number,
            'stock'       => $pi->batch->stock,
            'rate'        => $pi->ptr,
            'purchase_id' => $pi->purchase_id,
        ]);
    }


    /*
    |----------------------------------------------------------
    | STORE RETURN (SERVICE 🔥)
    |----------------------------------------------------------
    */
    public function store(Request $request, PurchaseReturnService $service)
    {
        try {

            $request->validate([
                'purchase_id' => 'required|exists:purchases,id',
                'items' => 'required|array|min:1',

                'items.*.purchase_item_id' => 'required|exists:purchase_items,id',
                'items.*.batch_id' => 'required|exists:batches,id',
                'items.*.quantity' => 'required|numeric|min:0.01',
            ]);

            // 🔥 EMPTY ROW REMOVE
            $items = collect($request->items)
                ->filter(fn($i) => !empty($i['quantity']) && $i['quantity'] > 0)
                ->values()
                ->toArray();

            if (empty($items)) {
                throw new \Exception("No valid return items");
            }

            $service->createReturn([
                'purchase_id' => $request->purchase_id,
                'items' => $items
            ]);

            return redirect()->route('purchase-return.index')
                ->with('success', 'Purchase Return Completed');

        } catch (\Exception $e) {

            return back()->with('error', $e->getMessage())->withInput();
        }
    }


    /*
    |----------------------------------------------------------
    | DELETE RETURN 🔥
    |----------------------------------------------------------
    */
    public function destroy(PurchaseReturn $return)
    {
        \DB::beginTransaction();

        try {

            foreach ($return->items as $item) {

                // STOCK BACK
                $batch = Batch::find($item->batch_id);
                if ($batch) {
                    $batch->increment('stock', $item->quantity);
                }

                // RETURNED QTY BACK
                $purchaseItem = PurchaseItem::find($item->purchase_item_id);
                if ($purchaseItem) {
                    $purchaseItem->decrement('returned_quantity', $item->quantity);
                }

                // STOCK MOVEMENT
                \App\Models\StockMovement::create([
                    'item_id' => $item->item_id,
                    'batch_id' => $item->batch_id,
                    'type' => 'purchase_return_delete',
                    'quantity' => $item->quantity,
                        'running_stock' => $newStock, // ✅ FIXED

                    'reference_id' => $return->id,
                    'reference_type' => 'purchase_return_delete',
                    'transaction_date' => now(),
                    'user_id' => auth()->id(),
                ]);
            }

            // LEDGER REVERSE
            $lastBalance = \App\Models\SupplierLedger::where('supplier_id', $return->supplier_id)
                ->latest()
                ->value('balance_after') ?? 0;

            $newBalance = $lastBalance + $return->total_amount;

            \App\Models\SupplierLedger::create([
                'supplier_id'     => $return->supplier_id,
                'reference_id'    => $return->id,
                'reference_type'  => 'purchase_return_delete',
                'debit'           => $return->total_amount,
                'credit'          => 0,
                'balance_after'   => $newBalance,
                'transaction_date'=> now(),
                'entry_by'        => auth()->id(),
                'remarks'         => 'Return deleted'
            ]);

            $return->items()->delete();
            $return->delete();

            \DB::commit();

            return back()->with('success', 'Return deleted');

        } catch (\Exception $e) {

            \DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }
    
}