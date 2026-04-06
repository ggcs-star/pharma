<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_number',
        'bill_date',
        'bill_id',
        'amount',
        'net_amount',
        'utr_number',
        'status',
        'payment_mode'
    ];

    public function bill()
    {
        return $this->belongsTo(Bill::class, 'bill_id');
    }
}