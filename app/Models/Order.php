<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'prescription_id', // 🔥 ADD THIS
        'status',
        'total',
        'payment_id',
        'payment_mode',
        'payment_status',
        'address_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    // 🔥 ADD THIS (VERY IMPORTANT)
    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }
}