<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    protected $fillable = [
        'purchase_id',
        'item_id',
        'batch_id',
        'quantity',
        'free_quantity',
        'returned_quantity',
        'mrp',
        'ptr',
        'discount',           // ← Add this
        'gst',                // ← Add this (missing)
        'gst_percent',
        'gst_amount',
        'discount_percent',
        'discount_amount',    // ← Add this (missing)
        'taxable_amount',
        'total_amount',
        'amount',
        'barcode',
        'rack',
        'hsn_code',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'free_quantity' => 'decimal:2',
        'returned_quantity' => 'decimal:2',
        'mrp' => 'decimal:2',
        'ptr' => 'decimal:2',
        'discount' => 'decimal:2',
        'gst' => 'decimal:2',
        'gst_percent' => 'decimal:2',
        'gst_amount' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'taxable_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function returnItems()
    {
        return $this->hasMany(PurchaseReturnItem::class);
    }

    public function getAvailableQtyAttribute()
    {
        return $this->quantity - ($this->returned_quantity ?? 0);
    }

    public function getTotalQtyAttribute()
    {
        return $this->quantity + ($this->free_quantity ?? 0);
    }

    public function scopeWithStock($query)
    {
        return $query->whereRaw('quantity > COALESCE(returned_quantity, 0)');
    }

    
}