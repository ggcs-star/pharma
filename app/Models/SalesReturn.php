<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesReturn extends Model
{
  protected $fillable = [
    'sale_id',
    'customer_id',
    'return_number',
    'return_date',
    'net_amount', // ✅ change
    'entry_by'      // ✅ add (controller me use ho raha hai)
];

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function items()
    {
        return $this->hasMany(SalesReturnItem::class, 'sales_return_id');
    }
}