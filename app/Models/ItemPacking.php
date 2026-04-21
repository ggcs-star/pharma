<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemPacking extends Model
{
    protected $fillable = [
        'item_id',
        'pack_id',
        'qty',
        'packaging_detail',
        'product_form'
    ];
    public function packType()
{
    return $this->belongsTo(\App\Models\PackType::class, 'pack_id');
}
}
