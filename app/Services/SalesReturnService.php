<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\SalesReturn;
use App\Models\SalesReturnItem;
use App\Models\Batch;
use App\Models\CustomerLedger;
use Illuminate\Support\Facades\DB;

class SalesReturnService
{
    public function createReturn(array $data)
    {
        return DB::transaction(function () use ($data) {

            $sale = Sale::findOrFail($data['sale_id']);
            $totalReturnAmount = 0;

            // 1️⃣ Create Sales Return Entry
            $salesReturn = SalesReturn::create([
                'sale_id'     => $sale->id,
                'customer_id' => $sale->customer_id,
                'return_date' => now(),
                'entry_by'    => auth()->id(),
            ]);

            // 2️⃣ Loop Return Items
            foreach ($data['items'] as $itemData) {

                $batch = Batch::lockForUpdate()->findOrFail($itemData['batch_id']);

                // Increase stock back
                $batch->increment('stock', $itemData['quantity']);

                $lineTotal = $itemData['quantity'] * $itemData['selling_price'];

                SalesReturnItem::create([
                    'sales_return_id' => $salesReturn->id,
                    'sale_id'         => $sale->id,
                    'item_id'         => $itemData['item_id'],
                    'batch_id'        => $itemData['batch_id'],
                    'quantity'        => $itemData['quantity'],
                    'selling_price'   => $itemData['selling_price'],
                    'amount'          => $lineTotal
                ]);

                $totalReturnAmount += $lineTotal;
            }

            $salesReturn->update([
'net_amount' => $totalReturnAmount  
          ]);


            // 3️⃣ Ledger Entry (DEBIT because sale credit reduce)
            $lastBalance = CustomerLedger::where('customer_id', $sale->customer_id)
                ->lockForUpdate()
                ->latest()
                ->value('balance') ?? 0;

            $newBalance = $lastBalance - $totalReturnAmount;

            CustomerLedger::create([
                'customer_id'    => $sale->customer_id,
                'reference_id'   => $salesReturn->id,
                'reference_type' => 'sale_return',
                'debit'          => $totalReturnAmount,
                'credit'         => 0,
                'balance'        => $newBalance,
                'transaction_date'=> now(),
                'entry_by'       => auth()->id()
            ]);

            return $salesReturn;
        });
    }
}