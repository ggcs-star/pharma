<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\CustomerLedger;
use App\Models\SupplierLedger;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function createPayment(array $data)
    {
        return DB::transaction(function () use ($data) {

            // Generate Payment Code
            $lastPayment = Payment::lockForUpdate()->latest()->first();
            $nextNumber = $lastPayment ? $lastPayment->id + 1 : 1;

            $paymentCode = 'PAY-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

            $payment = Payment::create([
                'payment_code' => $paymentCode,
                'payment_date' => now(),
                'payment_type' => $data['payment_type'],
                'customer_id'  => $data['customer_id'] ?? null,
                'supplier_id'  => $data['supplier_id'] ?? null,
                'amount'       => $data['amount'],
                'payment_mode' => $data['payment_mode'],
                'reference_no' => $data['reference_no'] ?? null,
                'entry_by'     => auth()->id(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | CUSTOMER PAYMENT
            |--------------------------------------------------------------------------
            */
            if ($data['payment_type'] === 'customer') {

                $lastBalance = CustomerLedger::where('customer_id', $data['customer_id'])
                    ->lockForUpdate()
                    ->latest()
                    ->value('balance') ?? 0;

                $newBalance = $lastBalance - $data['amount'];

                CustomerLedger::create([
                    'customer_id'    => $data['customer_id'],
                    'reference_id'   => $payment->id,
                    'reference_type' => 'payment',
                    'debit'          => $data['amount'],
                    'credit'         => 0,
                    'balance'        => $newBalance,
                    'transaction_date'=> now(),
                    'entry_by'       => auth()->id()
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | SUPPLIER PAYMENT
            |--------------------------------------------------------------------------
            */
            if ($data['payment_type'] === 'supplier') {

                $lastBalance = SupplierLedger::where('supplier_id', $data['supplier_id'])
                    ->lockForUpdate()
                    ->latest()
                    ->value('balance') ?? 0;

                $newBalance = $lastBalance - $data['amount'];

                SupplierLedger::create([
                    'supplier_id'    => $data['supplier_id'],
                    'reference_id'   => $payment->id,
                    'reference_type' => 'payment',
                    'debit'          => 0,
                    'credit'         => $data['amount'],
                    'balance'        => $newBalance,
                    'transaction_date'=> now(),
                    'entry_by'       => auth()->id()
                ]);
            }

            return $payment;
        });
    }
}