<?php

namespace App\Models\Purchase;

use Illuminate\Database\Eloquent\Model;
use App\Models\Item;
use App\Models\SupplierItemCatalog;

class PurchaseOrderItem extends Model
{
    protected $fillable = [
        'purchase_order_id',
        'item_id',
        'supplier_item_catalog_id', // 🔥 ADD THIS
        'quantity',
        'rate',
        'gst_percent',
        'gst_amount',
        'discount_percent',
        'discount_amount',
        'taxable_amount',
        'total_amount'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'rate' => 'decimal:2',
        'gst_percent' => 'decimal:2',
        'gst_amount' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'taxable_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // 🔥 Order relation
    public function order()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    public function supplierItemCatalog()
{
    return $this->belongsTo(
        \App\Models\SupplierItemCatalog::class,
        'supplier_item_catalog_id'
    );
}
    // 🔥 Item relation
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    // 🔥 NEW: Supplier Catalog relation (MAIN)
    public function catalog()
    {
        return $this->belongsTo(
            SupplierItemCatalog::class,
            'supplier_item_catalog_id'
        );
    }

    // 🔥 OPTIONAL: Direct supplier access (powerful)
    public function supplier()
    {
        return $this->catalog ? $this->catalog->supplier() : null;
    }
}