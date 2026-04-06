<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\SalesItem;
use App\Models\Batch;
use App\Models\Bill;
use App\Models\CustomerLedger;
use Illuminate\Support\Facades\DB;

class SaleService
{
    /*
    |--------------------------------------------------------------------------
    | CREATE SALE
    |--------------------------------------------------------------------------
    */
    public function createSale(array $data)
    {
        return DB::transaction(function () use ($data) {

            $totalAmount = 0;

            // 1️⃣ Create Sale
            $sale = Sale::create([
                'customer_id' => $data['customer_id'],
                'doctor_id'   => $data['doctor_id'] ?? null,
                'gst'         => 0,
                'discount'    => 0,
                'net_amount'  => 0,
                'sales_type'  => $data['sales_type'] ?? 'retail',
                'entry_by'    => auth()->id(),
            ]);

            // 2️⃣ FIFO + Expiry Protected
            foreach ($data['items'] as $itemData) {

                $remainingQty = $itemData['quantity'];

                $batches = Batch::where('item_id', $itemData['item_id'])
                    ->where('stock', '>', 0)
                    ->whereDate('expiry_date', '>=', now())
                    ->orderBy('expiry_date')
                    ->lockForUpdate()
                    ->get();

                foreach ($batches as $batch) {

                    if ($remainingQty <= 0) break;

                    $deductQty = min($batch->stock, $remainingQty);

                    $gross = $deductQty * $itemData['selling_price'];

                    $discount = $itemData['discount'] ?? 0;
                    $gst = $itemData['gst'] ?? 0;

                    $discountAmount = ($gross * $discount) / 100;
                    $taxable = $gross - $discountAmount;
                    $gstAmount = ($taxable * $gst) / 100;

                    $lineTotal = $taxable + $gstAmount;

                    SalesItem::create([
                        'sale_id'       => $sale->id,
                        'item_id'       => $itemData['item_id'],
                        'batch_id'      => $batch->id,
                        'quantity'      => $deductQty,
                        'selling_price' => $itemData['selling_price'],
                        'discount'      => $discount,
                        'gst'           => $gst,
                        'amount'        => $lineTotal
                    ]);

                    $batch->decrement('stock', $deductQty);

                    $remainingQty -= $deductQty;
                    $totalAmount += $lineTotal;
                }

                if ($remainingQty > 0) {
                    throw new \Exception("Insufficient stock for item ID: " . $itemData['item_id']);
                }
            }

            // 3️⃣ Safe Bill Generation
            $lastBill = Bill::lockForUpdate()->latest()->first();
            $nextNumber = $lastBill ? $lastBill->id + 1 : 1;

            $billCode = 'BILL-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

            $bill = Bill::create([
                'bill_code'   => $billCode,
                'bill_date'   => now(),
                'net_amount'  => $totalAmount,
                'bill_type'   => 'sale',
                'customer_id' => $sale->customer_id
            ]);

            $sale->update([
                'bill_id'    => $bill->id,
                'net_amount' => $totalAmount
            ]);

            // 4️⃣ Ledger Entry (SALE = CREDIT)
            $lastBalance = CustomerLedger::where('customer_id', $sale->customer_id)
                ->lockForUpdate()
                ->latest()
                ->value('balance') ?? 0;

            $newBalance = $lastBalance + $totalAmount;

            CustomerLedger::create([
                'customer_id'   => $sale->customer_id,
                'bill_id'       => $bill->id,
                'reference_id'  => $sale->id,
                'reference_type'=> 'sale',
                'debit'         => 0,
                'credit'        => $totalAmount,
                'balance'       => $newBalance,
                'transaction_date' => now(),
                'entry_by'      => auth()->id()
            ]);

            return $sale->load('items');
        });
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE SALE (Reverse Stock + Ledger)
    |--------------------------------------------------------------------------
    */
    public function deleteSale(Sale $sale)
    {
        return DB::transaction(function () use ($sale) {

            // 1️⃣ Restore Stock
            foreach ($sale->items as $item) {
                $item->batch->increment('stock', $item->quantity);
            }

            // 2️⃣ Ledger Reversal (DEBIT)
            $lastBalance = CustomerLedger::where('customer_id', $sale->customer_id)
                ->lockForUpdate()
                ->latest()
                ->value('balance') ?? 0;

            $newBalance = $lastBalance - $sale->net_amount;

            CustomerLedger::create([
                'customer_id'   => $sale->customer_id,
                'bill_id'       => $sale->bill_id,
                'reference_id'  => $sale->id,
                'reference_type'=> 'sale_reverse',
                'debit'         => $sale->net_amount,
                'credit'        => 0,
                'balance'       => $newBalance,
                'transaction_date' => now(),
                'entry_by'      => auth()->id()
            ]);

            $sale->items()->delete();
            $sale->delete();
        });
    }
}