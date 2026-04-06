<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'contact',
        'flat_number',
        'discount',
        'customer_type',
        'address',
        'doctor_id',
        'preferred_language',
        'last_buy_date',
        'city',
        'pincode'
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function salesReturns()
    {
        return $this->hasMany(SalesReturn::class);
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    public function ledgers()
    {
        return $this->hasMany(CustomerLedger::class);
    }
}