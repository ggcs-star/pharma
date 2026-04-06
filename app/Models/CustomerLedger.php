<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerLedger extends Model
{
    // ✅ timestamps ON (migration me hai)
    public $timestamps = true;

    protected $fillable = [
        'customer_id',
        'reference_id',
        'type',
        'debit',
        'credit',
        'balance',
        'transaction_date',
        'description',
        'created_by'
    ];

    /**
     * 🔗 Customer Relation
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * 🔗 Created By User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}