<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesItem extends Model
{
    protected $fillable = [
        'sale_id',
        'item_id',
        'batch_id',
        'quantity',

        // 🔥 ADD THESE
        'sale_type',
        'unit_qty',

        'selling_price',
        'mrp',
        'discount',
        'gst',
        'gst_amount',
        'amount'
    ];

    /*
    |--------------------------------------------------------------------------
    | Casting
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_qty' => 'integer',

        'selling_price' => 'decimal:2',
        'mrp' => 'decimal:2',
        'discount' => 'decimal:2',
        'gst' => 'decimal:2',
        'gst_amount' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isLooseSale()
    {
        return $this->sale_type === 'loose';
    }

    public function isStripSale()
    {
        return $this->sale_type === 'strip';
    }

    public function getDisplayQtyAttribute()
    {
        if ($this->sale_type === 'loose') {
            return $this->unit_qty . ' Tablets';
        }

        return $this->quantity . ' Strips';
    }
}