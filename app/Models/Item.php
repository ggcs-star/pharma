<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Item extends Model
{
    /*
    |--------------------------------------------------------------------------
    | FILLABLE
    |--------------------------------------------------------------------------
    */
    protected $fillable = [

        // 🔹 BASIC
        'name',
        'slug',

        // 🔹 USER APP
        'main_image',
        'description',
        'product_highlights',
        'brand',

        // 🔹 ERP CORE
        'gst_percent',
        'pack_id',
        'number_of_units',
        'unit_id',
        'category_id',
        'sub_category_id',

        // 🔹 MEDICAL INFO
        'rack',
        'hsn_code',
        'sch_type',
        'manufacturer_id',
        'molecule',
        'barcode',

        // 🔹 STOCK ALERT
        'min_threshold',
        'max_threshold',

        // 🔹 FLAGS
        'need_prescription',
        'not_for_online_sale',
        'inactive',
        'block_purchase',
        'service_item',
        'sell_loose',
        'override_loose',

        // 🔹 CONVERSION
        'conversion_factor',
        'unit_ratio',

        // 🔹 EXPIRY / DISCOUNT
        'max_self_life',
        'max_discount',

        // 🔹 EXTRA
        'notes'
    ];

    /*
    |--------------------------------------------------------------------------
    | CASTS (AUTO JSON → ARRAY)
    |--------------------------------------------------------------------------
    */
    protected $casts = [
        'product_highlights' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function packType()
    {
        return $this->belongsTo(PackType::class, 'pack_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class, 'manufacturer_id');
    }

    // 🔥 GALLERY IMAGES
    public function images()
    {
        return $this->hasMany(ItemImage::class);
    }

    public function batches()
    {
        return $this->hasMany(Batch::class, 'item_id');
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class, 'item_id');
    }

    public function salesItems()
    {
        return $this->hasMany(SalesItem::class, 'item_id');
    }

    public function purchaseReturnItems()
    {
        return $this->hasMany(PurchaseReturnItem::class, 'item_id');
    }

    public function salesReturnItems()
    {
        return $this->hasMany(SalesReturnItem::class, 'item_id');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS (S3 IMAGE URL)
    |--------------------------------------------------------------------------
    */

    // 🔹 Main Image URL
    public function getMainImageUrlAttribute()
    {
        return $this->main_image
            ? Storage::disk('s3')->url($this->main_image)
            : null;
    }

    // 🔹 Gallery Images URLs
    public function getGalleryUrlsAttribute()
    {
        return $this->images->map(function ($img) {
            return Storage::disk('s3')->url($img->image);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | ERP HELPER METHODS
    |--------------------------------------------------------------------------
    */

    // ✔ Check item sellable or not
    public function isSellable()
    {
        return !$this->inactive && !$this->block_purchase;
    }

    // ✔ Total Stock (optional enable)
    // public function getAvailableStockAttribute()
    // {
    //     return $this->batches()->sum('stock');
    // }

    // ✔ FIFO batches (optional enable)
    // public function fifoBatches()
    // {
    //     return $this->batches()
    //         ->where('stock', '>', 0)
    //         ->orderBy('expiry_date');
    // }



    public function catalogs()
{
    return $this->hasMany(SupplierItemCatalog::class);
}
}