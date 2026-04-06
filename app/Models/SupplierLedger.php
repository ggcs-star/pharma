<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierLedger extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'supplier_id',
        'bill_id',
        'reference_id',
        'reference_type',

        'debit',
        'credit',

        'transaction_date',

        'balance_after',

        'payment_id',

        'entry_by'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}