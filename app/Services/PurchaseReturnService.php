<?php

namespace App\Services;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\Batch;
use App\Models\SupplierLedger;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class PurchaseReturnService
{
    public function createReturn(array $data)
    {
        return DB::transaction(function () use ($data) {

            // 🔥 PURCHASE
            $purchase = Purchase::findOrFail($data['purchase_id']);

            $totalReturnAmount = 0;

            // 🔥 CREATE RETURN
            $purchaseReturn = PurchaseReturn::create([
                'purchase_id'   => $purchase->id,
                'supplier_id'   => $purchase->supplier_id,
                'return_number' => 'PR-' . time(),
                'return_date'   => now(),
                'entry_by'      => auth()->id(),
                'total_amount'  => 0,
                'net_amount'    => 0
            ]);

            // 🔁 LOOP ITEMS
            foreach ($data['items'] as $itemData) {

                // 🔥 FIND PURCHASE ITEM
                $purchaseItem = PurchaseItem::where('purchase_id', $purchase->id)
                    ->where('batch_id', $itemData['batch_id'])
                    ->where('item_id', $itemData['item_id'])
                    ->firstOrFail();

                $qty = $itemData['quantity'];

                // 🔥 VALIDATION
                $returnedQty = $purchaseItem->returned_quantity ?? 0;
                $allowedQty  = $purchaseItem->quantity - $returnedQty;

                if ($qty > $allowedQty) {
                    throw new \Exception("Return qty exceeded for item ID {$itemData['item_id']}");
                }

                // 🔥 LOCK BATCH
                $batch = Batch::lockForUpdate()->findOrFail($itemData['batch_id']);

                if ($batch->stock < $qty) {
                    throw new \Exception("Insufficient stock for batch {$batch->batch_code}");
                }

                // 🔥 CALCULATION
                $rate   = $purchaseItem->ptr;
                $amount = $rate * $qty;
                $totalReturnAmount += $amount;

                // 🔥 SAVE RETURN ITEM
                PurchaseReturnItem::create([
                    'purchase_return_id' => $purchaseReturn->id,
                    'purchase_item_id'   => $purchaseItem->id,
                    'item_id'            => $itemData['item_id'],
                    'batch_id'           => $itemData['batch_id'],
                    'quantity'           => $qty,
                    'rate'               => $rate,
                    'amount'             => $amount
                ]);

                // 🔥 STOCK MINUS (BATCH)
                $batch->decrement('stock', $qty);

                // 🔥 UPDATE RETURNED QTY
                $purchaseItem->increment('returned_quantity', $qty);

                // ✅ 🔥 RUNNING STOCK CALCULATION (FIX)
                $lastStock = StockMovement::where('batch_id', $itemData['batch_id'])
                    ->orderBy('id', 'desc')
                    ->value('running_stock') ?? 0;

                $newStock = $lastStock - $qty;

                // 🔥 STOCK MOVEMENT (FIXED)
                StockMovement::create([
                    'item_id'        => $itemData['item_id'],
                    'batch_id'       => $itemData['batch_id'],
                    'type'           => 'purchase_return',
                    'quantity'       => -$qty,
                    'running_stock'  => $newStock, // ✅ FIXED
                    'reference_id'   => $purchaseReturn->id,
                    'reference_type' => 'purchase_return',
                    'user_id'        => auth()->id(),
                    'remarks'        => 'Purchase return',
                    'transaction_date' => now()
                ]);
            }

            // 🔥 UPDATE TOTAL
            $purchaseReturn->update([
                'total_amount' => $totalReturnAmount,
                'net_amount'   => $totalReturnAmount
            ]);

            // 🔥 SUPPLIER LEDGER
            $lastBalance = SupplierLedger::where('supplier_id', $purchase->supplier_id)
                ->lockForUpdate()
                ->orderBy('id', 'desc')
                ->value('balance_after') ?? 0;

            $newBalance = $lastBalance - $totalReturnAmount;

            SupplierLedger::create([
                'supplier_id'     => $purchase->supplier_id,
                'reference_id'    => $purchaseReturn->id,
                'reference_type'  => 'purchase_return',
                'debit'           => 0,
                'credit'          => $totalReturnAmount,
                'balance_after'   => $newBalance,
                'transaction_date'=> now(),
                'entry_by'        => auth()->id(),
                'remarks'         => 'Purchase return'
            ]);

            return $purchaseReturn;
        });
    }
}