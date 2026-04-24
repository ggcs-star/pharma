<?php

namespace App\Models\Purchase;

use Illuminate\Database\Eloquent\Model;
use App\Models\Supplier;
use App\Models\User; // 🔥 ADD

class PurchaseOrder extends Model
{
   protected $fillable = [
    'supplier_id',
    'retailer_id',
    'order_number',
    'order_date',
    'status',
    'total_amount',
    'total_gst',
    'total_discount',
    'net_amount',

    // NEW FLOW FIELDS
    'stock_received',
    'price_updated',
    'published_for_sale',
    'final_mrp',
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

    // 🔥 NEW RELATION
    public function retailer()
    {
        return $this->belongsTo(User::class, 'retailer_id');
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }
}