<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SupplierItem extends Model
{
    protected $fillable = [
        'supplier_id',
        'name',
        'slug',
        'main_image',
        'description',
        'brand',
        'selling_price',
        'product_highlights',
        'inactive'
    ];

    protected $casts = [
        'product_highlights' => 'array',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

public function getMainImageUrlAttribute()
{
    if (!$this->main_image) {
        return null;
    }

    // ✅ full URL case
    if (str_starts_with($this->main_image, 'http')) {
        return $this->main_image;
    }

    // ✅ S3 case
    return Storage::disk('s3')->url($this->main_image);
}
}