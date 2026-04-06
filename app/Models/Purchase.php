<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'supplier_id',
        'invoice_number',
        'bill_id',
        'entry_by',
        'gst',
        'discount',
        'net_amount',
        'due_date',
        'total_amount',
        'total_gst',
        'total_discount',
            'payment_type' 

    ];

    /*
    |--------------------------------------------------------------------------
    | Casting
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'due_date' => 'date',
        'total_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'total_gst' => 'decimal:2',
        'total_discount' => 'decimal:2',
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
        return $this->hasMany(PurchaseItem::class);
    }

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    public function returns()
    {
        return $this->hasMany(PurchaseReturn::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'entry_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Attributes
    |--------------------------------------------------------------------------
    */

    public function getTotalItemsAttribute()
    {
        return $this->items->sum('quantity');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeLatestFirst($query)
    {
        return $query->orderBy('id','desc');
    }
}