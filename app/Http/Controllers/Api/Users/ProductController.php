<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /* ===============================
        PRODUCT LIST
    =============================== */
    public function index()
    {
        $items = Item::whereHas('batches', function ($q) {
            $q->where('stock', '>', 0)
              ->where('expiry_date', '>', now());
        })
        ->with([
            'images',
            'manufacturer:id,name',
            'category:id,name',
            'subCategory:id,name',
            'unit:id,name',
            'packType:id,name',
            'batches'
        ])
        ->get()
        ->map(function ($item) {

            // ✅ Get valid batch
            $validBatch = $item->batches
                ->where('stock', '>', 0)
                ->where('expiry_date', '>', now())
                ->sortBy('expiry_date')
                ->first();

            // 🔥 fallback (important)
            if (!$validBatch) {
                $validBatch = $item->batches->sortBy('expiry_date')->first();
            }

            // ✅ PRICE
$item->price = $validBatch->sale_price 
            ?? $validBatch->selling_price 
            ?? $validBatch->mrp 
            ?? 0;            $item->mrp = $validBatch->mrp ?? 0;
            $item->batch_id = $validBatch->id ?? null;

            // ✅ STOCK
            $item->in_stock = $validBatch ? true : false;

            // ✅ IMAGE (S3)
            $item->image = $item->main_image
                ? Storage::disk('s3')->url($item->main_image)
                : null;

            $item->main_image_url = $item->image;

            // ✅ GALLERY IMAGES
            $item->images->map(function ($img) {
                $img->image_url = $img->image
                    ? Storage::disk('s3')->url($img->image)
                    : null;
                return $img;
            });

            return $item;
        });

        return response()->json([
            'status' => true,
            'data' => $items
        ]);
    }

    /* ===============================
        SINGLE PRODUCT
    =============================== */
    public function show($id)
{
    $item = Item::with([
        'images',
        'manufacturer',
        'category',
        'subCategory',
        'unit',
        'packType',
        'batches'
    ])->findOrFail($id);

    // ✅ Get valid batch
    $validBatch = $item->batches
        ->where('stock', '>', 0)
        ->where('expiry_date', '>', now())
        ->sortBy('expiry_date')
        ->first();

    // 🔥 fallback
    if (!$validBatch) {
        $validBatch = $item->batches->sortBy('expiry_date')->first();
    }

    // 🔥 FINAL SAFE PRICE LOGIC
    $price = 0;
    $mrp = 0;
    $batchId = null;

    if ($validBatch) {
        $price = $validBatch->sale_price 
              ?? $validBatch->selling_price 
              ?? $validBatch->mrp 
              ?? 0;

        $mrp = $validBatch->mrp ?? 0;
        $batchId = $validBatch->id;
    }

    // ✅ ASSIGN
    $item->price = $price;
    $item->mrp = $mrp;
    $item->batch_id = $batchId;

    // ✅ STOCK
    $item->in_stock = $validBatch ? true : false;

    // ✅ IMAGE
    $item->image = $item->main_image
        ? Storage::disk('s3')->url($item->main_image)
        : null;

    $item->main_image_url = $item->image;

    // ✅ GALLERY
    $item->images->map(function ($img) {
        $img->image_url = $img->image
            ? Storage::disk('s3')->url($img->image)
            : null;
        return $img;
    });

    return response()->json([
        'status' => true,
        'data' => $item
    ]);
}
    public function byCategory($id)
{
    $items = Item::where('category_id', $id)
        ->whereHas('batches', function ($q) {
            $q->where('stock', '>', 0)
              ->where('expiry_date', '>', now());
        })
        ->with(['images', 'batches'])
        ->get()
        ->map(function ($item) {

            $batch = $item->batches
                ->where('stock', '>', 0)
                ->where('expiry_date', '>', now())
                ->sortBy('expiry_date')
                ->first();

            if (!$batch) return null;

            return [
                'id' => $item->id,
                'name' => $item->name,
                'price' => $batch->sale_price ?? $batch->mrp ?? 0,
                'image' => $item->main_image 
                    ? \Storage::disk('s3')->url($item->main_image)
                    : null,
                'batch_id' => $batch->id
            ];
        })
        ->filter()
        ->values();

    return response()->json([
        'status' => true,
        'data' => $items
    ]);
}
}
