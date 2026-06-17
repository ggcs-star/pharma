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
        'notes',
        // 🔹 MEDICINE CONTENT
'medicine_type',
'introduction',
'how_to_use',
'safety_advise',
'if_miss',
'how_it_works',
'interaction',

// 🔹 USES / STORAGE
'primary_use',
'storage',
'common_side_effect',

// 🔹 SAFETY INTERACTIONS
'alcohol_interaction',
'pregnancy_interaction',
'lactation_interaction',
'driving_interaction',
'kidney_interaction',
'liver_interaction',

// 🔹 EXTRA JSON
'fact_box',
'quick_tips',
'q_a',

// 🔹 MANUFACTURER INFO
'manufacturer_address',
'country_of_origin',
'manufacturer_details',
'marketer_details',
    ];

    /*
    |--------------------------------------------------------------------------
    | CASTS (AUTO JSON → ARRAY)
    |--------------------------------------------------------------------------
    */
protected $casts = [

    'product_highlights' => 'array',

    'fact_box' => 'array',

    'quick_tips' => 'array',

    'q_a' => 'array',

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
    if (!$this->main_image) {
        return null;
    }

    // ✅ If already full URL (Excel case)
    if (str_starts_with($this->main_image, 'http')) {
        return $this->main_image;
    }

    // ✅ If stored in S3
    return Storage::disk('s3')->url($this->main_image);
}

    // 🔹 Gallery Images URLs
    public function getGalleryUrlsAttribute()
{
    return $this->images->map(function ($img) {

        if (str_starts_with($img->image, 'http')) {
            return $img->image;
        }

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

public function packings()
{
    return $this->hasMany(ItemPacking::class);
}

    public function catalogs()
{
    return $this->hasMany(SupplierItemCatalog::class);
}

public function getSafetyAdviceAttribute()
{
    return [

        'alcohol' => $this->alcohol_interaction,

        'pregnancy' => $this->pregnancy_interaction,

        'lactation' => $this->lactation_interaction,

        'driving' => $this->driving_interaction,

        'kidney' => $this->kidney_interaction,

        'liver' => $this->liver_interaction,

    ];
}

public function substitutes()
{
    return self::where('id', '!=', $this->id)
        ->where('molecule', $this->molecule)
        ->limit(10);
}


}