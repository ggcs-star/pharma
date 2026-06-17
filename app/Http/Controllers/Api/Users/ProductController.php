<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;

class ProductController extends Controller
{
    /* =========================================
        PRODUCT LIST
    ========================================= */
    public function index()
    {
        $items = Item::with([
            'images',
            'manufacturer',
            'category',
            'subCategory',
            'unit',
            'packType',
            'packings.packType',
            'batches'
        ])
        ->whereHas('batches', function ($q) {
            $q->where('stock', '>', 0)
              ->where('expiry_date', '>', now());
        })
        ->latest()
        ->get();

        return response()->json([
            'status' => true,
            'data' => $items->map(fn($item) => $this->formatItem($item))
        ]);
    }

    /* =========================================
        SINGLE PRODUCT
    ========================================= */
    public function show($id)
    {
        $item = Item::with([
            'images',
            'manufacturer',
            'category',
            'subCategory',
            'unit',
            'packType',
            'packings.packType',
            'batches'
        ])->findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $this->formatItem($item)
        ]);
    }

    /* =========================================
        BY CATEGORY
    ========================================= */
  /* =========================================
    BY CATEGORY SLUG
========================================= */
public function byCategory($slug)
{
    $items = Item::with([
        'images',
        'manufacturer',
        'category',
        'subCategory',
        'unit',
        'packType',
        'packings.packType',
        'batches'
    ])
    ->whereHas('category', function ($q) use ($slug) {

        $q->where('slug', $slug);

    })
    ->whereHas('batches', function ($q) {

        $q->where('stock', '>', 0)
          ->where('expiry_date', '>', now());

    })
    ->latest()
    ->get();

    return response()->json([

        'status' => true,

        'category_slug' => $slug,

        'data' => $items->map(fn($item) => $this->formatItem($item))
    ]);
}

public function brands()
{
    $brands = Item::with('batches')

        ->whereNotNull('brand')

        ->where('brand', '!=', '')

        ->whereHas('batches', function ($q) {

            $q->where('stock', '>', 0)
              ->where('expiry_date', '>', now());

        })

        ->get()

        ->groupBy('brand')

        ->map(function ($items, $brand) {

            $first = $items->first();

            return [

                'brand' => $brand,

                'image' => filter_var($first->main_image, FILTER_VALIDATE_URL)
                    ? $first->main_image
                    : \Storage::disk('s3')->url($first->main_image),

                'total_products' => $items->count()
            ];
        })

        ->values();

    return response()->json([

        'status' => true,

        'data' => $brands
    ]);
}
public function brandProducts($brand)
{
    $items = Item::with([
        'images',
        'manufacturer',
        'category',
        'subCategory',
        'unit',
        'packType',
        'packings.packType',
        'batches'
    ])
    ->whereRaw('LOWER(brand) = ?', [strtolower($brand)])
    ->whereHas('batches', function ($q) {

        $q->where('stock', '>', 0)
          ->where('expiry_date', '>', now());

    })
    ->latest()
    ->get();

    return response()->json([
        'status' => true,
        'brand' => $brand,
        'data' => $items->map(fn($item) => $this->formatItem($item))
    ]);
}

public function popularCategories()
{
    $categories = Item::with([
            'category',
            'batches'
        ])

        ->whereHas('category')

        ->whereHas('batches', function ($q) {

            $q->where('stock', '>', 0)
              ->where('expiry_date', '>', now());

        })

        ->get()

        ->groupBy(function ($item) {

            return $item->category?->id;
        })

        ->map(function ($items) {

            $first = $items->first();

            return [

                'id' => $first->category?->id,

                'name' => $first->category?->name,

                'slug' => $first->category?->slug,

               'image' => !empty($first->main_image)

    ? (

        filter_var($first->main_image, FILTER_VALIDATE_URL)

            ? $first->main_image

            : \Storage::disk('s3')->url($first->main_image)

    )

    : asset('images/default-category.png'),
                'total_products' => $items->count()
            ];
        })

        ->values();

    return response()->json([

        'status' => true,

        'data' => $categories
    ]);
}


public function homeSections()
{
    $sections = \App\Models\HomeSection::with([
            'items.batches',
            'category'
        ])

        ->where('is_active', 1)

        ->orderBy('sort_order')

        ->get();

    $final = [];

    foreach ($sections as $section) {

        $products = collect();

        /*
        |--------------------------------------------------------------------------
        | MANUAL PRODUCTS
        |--------------------------------------------------------------------------
        */

        if($section->type === 'manual'){

            $products = $section->items

                ->filter(function ($item) {

                    return $item->batches
                        ->where('stock', '>', 0)
                        ->where('expiry_date', '>', now())
                        ->count();
                })

                ->take(15)

                ->map(fn($item) => $this->formatItem($item))

                ->values();
        }

        /*
        |--------------------------------------------------------------------------
        | CATEGORY PRODUCTS
        |--------------------------------------------------------------------------
        */

        elseif($section->type === 'category'){

            $products = Item::with([
                    'images',
                    'manufacturer',
                    'category',
                    'subCategory',
                    'unit',
                    'packType',
                    'packings.packType',
                    'batches'
                ])

                ->where(
                    'category_id',
                    $section->category_id
                )

                ->whereHas('batches', function ($q) {

                    $q->where('stock', '>', 0)
                      ->where('expiry_date', '>', now());

                })

                ->latest()

                ->take(15)

                ->get()

                ->map(fn($item) => $this->formatItem($item));
        }

        /*
        |--------------------------------------------------------------------------
        | LATEST PRODUCTS
        |--------------------------------------------------------------------------
        */

        elseif($section->type === 'latest'){

            $products = Item::with([
                    'images',
                    'manufacturer',
                    'category',
                    'subCategory',
                    'unit',
                    'packType',
                    'packings.packType',
                    'batches'
                ])

                ->whereHas('batches', function ($q) {

                    $q->where('stock', '>', 0)
                      ->where('expiry_date', '>', now());

                })

                ->latest()

                ->take(15)

                ->get()

                ->map(fn($item) => $this->formatItem($item));
        }

        $final[] = [

            'title' => $section->title,

            'slug' => $section->slug,

            'type' => $section->type,

            'products' => $products,
        ];
    }

    return response()->json([

        'status' => true,

        'data' => $final
    ]);
}
    /* =========================================
        FORMAT ITEM
    ========================================= */
    private function formatItem($item)
    {
        // ✅ VALID BATCH
        $batch = $item->batches
            ->where('stock', '>', 0)
            ->where('expiry_date', '>', now())
            ->sortBy('expiry_date')
            ->first();

        if (!$batch) {
            $batch = $item->batches
                ->sortBy('expiry_date')
                ->first();
        }

        return [

            /* =========================================
                BASIC
            ========================================= */

            'id' => $item->id,
            'name' => $item->name,
            'slug' => $item->slug,
            'description' => $item->description,
            'brand' => $item->brand,

            /* =========================================
                IMAGES
            ========================================= */

            'main_image' => filter_var($item->main_image, FILTER_VALIDATE_URL)
    ? $item->main_image
    : \Storage::disk('s3')->url($item->main_image),
'gallery_images' => $item->images->map(function ($img) {

    return [
        'id' => $img->id,

        'image' => filter_var($img->image, FILTER_VALIDATE_URL)
            ? $img->image
            : \Storage::disk('s3')->url($img->image),

        'sort_order' => $img->sort_order,
    ];

})->values(),

            /* =========================================
                CATEGORY
            ========================================= */

            'category' => [
                'id' => $item->category?->id,
                'name' => $item->category?->name,
            ],

            'sub_category' => [
                'id' => $item->subCategory?->id,
                'name' => $item->subCategory?->name,
            ],

            /* =========================================
                MANUFACTURER
            ========================================= */

            'manufacturer' => [
                'id' => $item->manufacturer?->id,
                'name' => $item->manufacturer?->name,
            ],

            /* =========================================
                PACK / UNIT
            ========================================= */

            'pack_type' => [
                'id' => $item->packType?->id,
                'name' => $item->packType?->name,
            ],

            'unit' => [
                'id' => $item->unit?->id,
                'name' => $item->unit?->name,
            ],

            'number_of_units' => $item->number_of_units,

            /* =========================================
                ERP INFO
            ========================================= */

            'gst_percent' => $item->gst_percent,
            'rack' => $item->rack,
            'hsn_code' => $item->hsn_code,
            'sch_type' => $item->sch_type,
            'barcode' => $item->barcode,
            'molecule' => $item->molecule,

            /* =========================================
                PRODUCT DETAILS
            ========================================= */

            'product_highlights' => $item->product_highlights,
            'how_it_works' => $item->how_it_works,
            'interaction' => $item->interaction,
            'manufacturer_details' => $item->manufacturer_details,
            'marketer_details' => $item->marketer_details,
            'country_of_origin' => $item->country_of_origin,
            'q_a' => $item->q_a,
            'quick_tips' => $item->quick_tips,
            'storage' => $item->storage,
            'common_side_effect' => $item->common_side_effect,
            'alcohol_interaction' => $item->alcohol_interaction,
            'pregnancy_interaction' => $item->pregnancy_interaction,
            'lactation_interaction' => $item->lactation_interaction,
            'driving_interaction' => $item->driving_interaction,
            'kidney_interaction' => $item->kidney_interaction,
            'liver_interaction' => $item->liver_interaction,
            'fact_box' => $item->fact_box,

            /* =========================================
                FLAGS
            ========================================= */

            'need_prescription' => (bool) $item->need_prescription,
            'not_for_online_sale' => (bool) $item->not_for_online_sale,
            'inactive' => (bool) $item->inactive,
            'block_purchase' => (bool) $item->block_purchase,
            'service_item' => (bool) $item->service_item,
            'sell_loose' => (bool) $item->sell_loose,
            'override_loose' => (bool) $item->override_loose,

            /* =========================================
                STOCK / PRICE
            ========================================= */

            'batch_id' => $batch?->id,
            'batch_code' => $batch?->batch_code,

            'mrp' => $batch?->mrp ?? 0,
           'online_price' => $batch?->online_price ?? 0,

'offline_price' => $batch?->offline_price ?? 0,

'selling_price' =>
    $batch?->selling_price
    ?? $batch?->online_price
    ?? 0,

// ✅ BACKWARD COMPATIBILITY
'price' =>
    $batch?->online_price
    ?? $batch?->selling_price
    ?? 0,
            'stock' => $batch?->stock ?? 0,
            'expiry_date' => $batch?->expiry_date,

            'in_stock' => $batch ? true : false,

            /* =========================================
                PACKINGS
            ========================================= */

            'packings' => $item->packings->map(function ($packing) {

                return [
                    'id' => $packing->id,
                    'qty' => $packing->qty,

                    'pack_type' => [
                        'id' => $packing->packType?->id,
                        'name' => $packing->packType?->name,
                    ],

                    'packaging_detail' => $packing->packaging_detail,
                    'product_form' => $packing->product_form,
                ];
            }),

            /* =========================================
                TIMESTAMP
            ========================================= */

            'created_at' => $item->created_at,
            'updated_at' => $item->updated_at,
        ];
    }
}