<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Sale;
use App\Models\SalesItem;
use App\Models\Batch;
use App\Models\SalesReturn;
use App\Models\SalesReturnItem;
use App\Models\StockMovement;
use App\Models\CustomerLedger;
use Illuminate\Support\Facades\Auth;

class SalesReturnController extends Controller
{
    // =====================
    // LIST (INDEX)
    // =====================
    public function index()
    {
        $returns = SalesReturn::with(['sale', 'customer'])
            ->latest()
            ->paginate(10);

        return view('sales_return.index', compact('returns'));
    }

    // =====================
    // CREATE (BILL BASED)
    // =====================
    public function create(Request $request)
    {
        $sale = null;

        if ($request->bill_number) {

            $sale = Sale::with(['items.item', 'items.batch'])
                ->where('bill_number', $request->bill_number)
                ->first();

            if ($sale) {
                foreach ($sale->items as $item) {

                    $returnedQty = SalesReturnItem::where('sale_item_id', $item->id)
                        ->sum('qty_return');

                    $item->returned_qty = $returnedQty;
                }
            }
        }

        return view('sales_return.create', compact('sale'));
    }

    // =====================
    // STORE
    // =====================
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {

            $sale = Sale::findOrFail($request->sale_id);

            // AUTO RETURN NUMBER
$last = SalesReturn::latest()->first();
$nextNumber = $last ? ((int) substr($last->return_number, 3)) + 1 : 1;
$returnNumber = 'SR-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

$return = SalesReturn::create([
    'return_number' => $returnNumber, // 🔥 FIX
    'sale_id' => $sale->id,
    'customer_id' => $sale->customer_id,
    'return_date' => now(),
'net_amount' => 0,
    'entry_by' => Auth::id(),

]);

            $total = 0;

            foreach ($request->items as $row) {

                $returnQty = (float) ($row['qty_return'] ?? 0);

                if ($returnQty <= 0) continue;

                $saleItem = SalesItem::findOrFail($row['sale_item_id']);

                // VALIDATION
                $alreadyReturned = SalesReturnItem::where('sale_item_id', $saleItem->id)
                    ->sum('qty_return');

                $allowedQty = $saleItem->quantity - $alreadyReturned;

                if ($returnQty > $allowedQty) {
                    throw new \Exception("Return qty exceeds allowed");
                }

                $batch = Batch::lockForUpdate()->findOrFail($saleItem->batch_id);

                // STOCK ADD BACK
                $batch->increment('stock', $returnQty);

                // STOCK MOVEMENT
                StockMovement::create([
                    'item_id' => $saleItem->item_id,
                    'batch_id' => $batch->id,
                    'type' => 'SALE_RETURN',
                    'quantity' => $returnQty,
                    'running_stock' => $batch->stock,
                    'reference_id' => $return->id,
                    'reference_type' => SalesReturn::class,
                    'user_id' => Auth::id(),
                    'transaction_date' => now()
                ]);

                $amount = $returnQty * $saleItem->selling_price;

                SalesReturnItem::create([
                    'sales_return_id' => $return->id,
                    'sale_item_id'    => $saleItem->id,
                    'item_id'         => $saleItem->item_id,
                    'batch_id'        => $batch->id,
                    'qty_return'      => $returnQty,
                    'rate'            => $saleItem->selling_price,
                    'amount'          => $amount
                ]);

                $total += $amount;
            }

$return->update(['net_amount' => $total]);
            // LEDGER ENTRY
            if ($sale->customer_id) {

                $lastBalance = CustomerLedger::where('customer_id', $sale->customer_id)
                    ->latest()
                    ->value('balance') ?? 0;

                $newBalance = $lastBalance - $total;

                CustomerLedger::create([
                    'customer_id' => $sale->customer_id,
                    'reference_id' => $return->id,
                    'reference_type' => SalesReturn::class,
                    'debit' => 0,
                    'credit' => $total,
                    'balance' => $newBalance,
                    'transaction_date' => now(),
                    'entry_by' => Auth::id(),
                ]);
            }

            DB::commit();

            return redirect()->route('sales.return.index')
                ->with('success', 'Return processed successfully');

        } catch (\Exception $e) {

            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    // =====================
    // SHOW (DETAIL VIEW)
    // =====================
    public function show($id)
    {
        $return = SalesReturn::with([
            'sale',
            'customer',
            'items.item',
            'items.batch'
        ])->findOrFail($id);

        return view('sales_return.show', compact('return'));
    }
}