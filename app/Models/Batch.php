<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    protected $fillable = [
        'item_id',
        'batch_code',
        'expiry_date',
        'stock',
        'mrp',
        'ptr',
        'discount',
        'margin',
        'markup',
        'selling_price'
    ];

    /*
    |--------------------------------------------------------------------------
    | Casting
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'expiry_date' => 'date',
        'stock' => 'decimal:2',
        'mrp' => 'decimal:2',
        'ptr' => 'decimal:2',
        'discount' => 'decimal:2',
        'margin' => 'decimal:2',
        'markup' => 'decimal:2',
        'selling_price' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class, 'batch_id');
    }

    public function salesItems()
    {
        return $this->hasMany(SalesItem::class, 'batch_id');
    }

    public function salesReturnItems()
    {
        return $this->hasMany(SalesReturnItem::class, 'batch_id');
    }

    public function purchaseReturnItems()
    {
        return $this->hasMany(PurchaseReturnItem::class, 'batch_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Stock Methods (SAFE 🔥)
    |--------------------------------------------------------------------------
    */

    public function reduceStock($qty)
    {
        if ($this->stock < $qty) {
            throw new \Exception("Insufficient stock in batch {$this->batch_code}");
        }

        $this->decrement('stock', $qty);
    }

    public function increaseStock($qty)
    {
        $this->increment('stock', $qty);
    }

    /*
    |--------------------------------------------------------------------------
    | ERP Helpers
    |--------------------------------------------------------------------------
    */

    public function isExpired()
    {
        return now()->gt($this->expiry_date);
    }

    public function isNearExpiry($days = 90)
    {
        return now()->diffInDays($this->expiry_date, false) <= $days;
    }

    public function isAvailable()
    {
        return $this->stock > 0 && !$this->isExpired();
    }

    public function isLowStock($limit = 10)
    {
        return $this->stock <= $limit;
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeAvailable($query)
    {
        return $query->where('stock', '>', 0)
                     ->whereDate('expiry_date', '>=', now())
                     ->orderBy('expiry_date', 'asc');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getDisplayNameAttribute()
    {
        return $this->batch_code . ' (Exp: ' . $this->expiry_date->format('m/Y') . ')';
    }
    
}
