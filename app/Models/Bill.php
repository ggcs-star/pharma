<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $fillable = [
        'bill_code',
        'bill_date',
        'invoice_number',
        'gst',
        'discount',
        'net_amount',
        'bill_type',
        'supplier_id',
        'customer_id'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'bill_id');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class, 'bill_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'bill_id');
    }
}