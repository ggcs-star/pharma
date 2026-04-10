<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierStock extends Model
{
    protected $fillable = [
        'supplier_item_catalog_id',
        'qty',
        'type',
        'reference_id',
        'note'
    ];

   

    public function catalog()
    {
        return $this->belongsTo(SupplierItemCatalog::class, 'supplier_item_catalog_id');
    }

   

    public function isPurchase()
    {
        return $this->type === 'purchase';
    }

    public function isSale()
    {
        return $this->type === 'sale';
    }
}