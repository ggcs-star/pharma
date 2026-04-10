<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierItemCatalog extends Model
{
    protected $fillable = [
        'supplier_id',
        'item_id',
        'purchase_price',
        'base_price',
        'retailer_price',
        'retailer_mrp',
        'is_price_editable',
        'current_stock',
        'supplier_margin',
        'retailer_margin',
        'expiry_date',
        'batch_no',
        'gst_percent',
        'free_qty',
        'is_active'
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'is_price_editable' => 'boolean',
        'is_active' => 'boolean',
    ];

    

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function stocks()
    {
        return $this->hasMany(SupplierStock::class);
    }


    // public function getRealStockAttribute()
    // {
    //     return $this->stocks()->sum('qty');
    // }
    public function getTotalPurchaseAttribute()
{
    return $this->stocks()
        ->whereIn('type', ['purchase','inbound'])
        ->sum('qty');
}
public function getTotalSaleAttribute()
{
    return $this->stocks()
        ->whereIn('type', ['sale','outbound'])
        ->sum('qty');
}
public function getRealStockAttribute()
{
    return $this->total_purchase - $this->total_sale;
}

  
    public function isExpired()
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }


    public function getSupplierProfitAttribute()
    {
        return $this->retailer_price - $this->purchase_price;
    }
}