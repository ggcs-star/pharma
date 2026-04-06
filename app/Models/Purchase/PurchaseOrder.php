<?php

namespace App\Models\Purchase;

use Illuminate\Database\Eloquent\Model;
use App\Models\Supplier;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'supplier_id',
        'order_number',
        'order_date',
        'status',
        'total_amount',
        'total_gst',
        'total_discount',
        'net_amount'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }
}