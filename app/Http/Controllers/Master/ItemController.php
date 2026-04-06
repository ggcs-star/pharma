<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Manufacturer;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\PackType;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ItemController extends Controller
{

/*----------------------------------------------------
ITEM LIST
----------------------------------------------------*/
public function index()
{
    $items = Item::with([
        'manufacturer',
        'category',
        'subCategory',
        'unit',
        'packType',
        'images'
    ])->latest()->paginate(20);

    return view('master.items.index', compact('items'));
}

public function import(Request $request)
{
    try {
        $file = $request->file('file');

        $handle = fopen($file->getRealPath(), 'r');

        $header = fgetcsv($handle);

        while (($row = fgetcsv($handle)) !== false) {

            // skip broken rows
            if (count($row) != count($header)) {
                continue;
            }

$data = array_combine($header, $row);

if (!isset($data['pack'])) {
    return back()->with('error', 'CSV HEADER ISSUE: pack column missing');
}
            if (empty($data['name'])) continue;
$categoryId = $this->getCategoryId($data['category'] ?? 'Medicine');

$subCategoryId = $this->getSubCategoryId(
    $data['sub_category'] ?? 'General',
    $categoryId
);

$manufacturerId = $this->getManufacturerId($data['manufacturer'] ?? 'Generic');

\App\Models\Item::create([
    'name' => $data['name'],
    'slug' => \Str::slug($data['name']),
    'gst_percent' => $data['gst_percent'] ?? 0,

    'pack_id' => $this->getPackId($data['pack'] ?? 'Strip'),
    'unit_id' => $this->getUnitId($data['unit'] ?? 'Tablet'),

    'category_id' => $categoryId,
    'sub_category_id' => $subCategoryId,
    'manufacturer_id' => $manufacturerId,

    'number_of_units' => $data['number_of_units'] ?? 1,

    // 🔥 ALL REQUIRED FIELDS MANUAL
    'min_threshold' => $data['min_threshold'] ?? 0,
    'max_threshold' => $data['max_threshold'] ?? 0,
    'conversion_factor' => $data['conversion_factor'] ?? 1,

    'need_prescription' => $data['need_prescription'] ?? 0,
    'not_for_online_sale' => $data['not_for_online_sale'] ?? 0,
    'inactive' => $data['inactive'] ?? 0,
    'block_purchase' => $data['block_purchase'] ?? 0,
    'service_item' => $data['service_item'] ?? 0,
    'sell_loose' => $data['sell_loose'] ?? 0,
    'override_loose' => $data['override_loose'] ?? 0,
    'unit_ratio' => $data['unit_ratio'] ?? 1,
    'max_self_life' => $data['max_self_life'] ?? 0, 
    'max_discount' => $data['max_discount'] ?? 0,
]);
        }

        fclose($handle);

        return back()->with('success', '✅ CSV Imported Successfully');

    } catch (\Exception $e) {

        return back()->with('error', $e->getMessage());
    }
}
private function getCategoryId($name)
{
    if (!$name) return null;

    return Category::firstOrCreate(['name' => $name])->id;
}

private function getSubCategoryId($name, $categoryId = null)
{
    if (!$name) return null;

    return SubCategory::firstOrCreate([
        'name' => $name,
        'category_id' => $categoryId
    ])->id;
}

private function getManufacturerId($name)
{
    if (!$name) return null;

    return Manufacturer::firstOrCreate(['name' => $name])->id;
}

private function getUnitId($name)
{
    if (!$name) return null;

    return Unit::firstOrCreate(['name' => $name])->id;
}

private function getPackId($name)
{
    if (!$name) return null;

    return PackType::firstOrCreate(['name' => $name])->id;
}
/*----------------------------------------------------
CREATE FORM
----------------------------------------------------*/
public function create()
{
    return view('master.items.create', [
        'manufacturers' => Manufacturer::select('id','name')->get(),
        'categories' => Category::select('id','name')->get(),
        'subCategories' => SubCategory::select('id','name')->get(),
        'units' => Unit::select('id','name')->get(),
        'packTypes' => PackType::select('id','name')->get(),
    ]);
}

/*----------------------------------------------------
STORE
----------------------------------------------------*/
public function store(Request $request)
{
    Log::info('ITEM STORE START', $request->all());

    DB::beginTransaction();

    try {

        $mainImagePath = null;

        if ($request->hasFile('main_image')) {
            Log::info('Uploading main image...');
            $mainImagePath = Storage::disk('s3')
                ->put('items/main', $request->file('main_image'));
        }

        $item = Item::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),

            'main_image' => $mainImagePath,

            'description' => $request->description,
            'product_highlights' => $request->product_highlights,
            'brand' => $request->brand,

            'gst_percent' => $request->gst_percent ?? 0,

            'pack_id' => $request->pack_id,
            'number_of_units' => $request->number_of_units ?? 1,
            'unit_id' => $request->unit_id,

            'category_id' => $request->category_id,
            'sub_category_id' => $request->sub_category_id,

            'rack' => $request->rack,
            'hsn_code' => $request->hsn_code,
            'sch_type' => $request->sch_type,

            'manufacturer_id' => $request->manufacturer_id,
            'molecule' => $request->molecule,

            'barcode' => $request->barcode,

            'min_threshold' => $request->min_threshold ?? 0,
            'max_threshold' => $request->max_threshold ?? 0,

            'need_prescription' => $request->boolean('need_prescription'),
            'not_for_online_sale' => $request->boolean('not_for_online_sale'),

            'inactive' => $request->boolean('inactive'),
            'block_purchase' => $request->boolean('block_purchase'),

            'service_item' => $request->boolean('service_item'),

            'sell_loose' => $request->boolean('sell_loose'),
            'override_loose' => $request->boolean('override_loose'),

            'conversion_factor' => $request->conversion_factor ?? 1,
            'unit_ratio' => $request->unit_ratio ?? 1,

            'max_self_life' => $request->max_self_life ?? 0,
            'max_discount' => $request->max_discount ?? 0,

            'notes' => $request->notes
        ]);

        Log::info('Item Created ID: ' . $item->id);

        // 🔹 GALLERY
$galleryImages = $request->file('gallery_images');

if (!empty($galleryImages)) {

    foreach ($galleryImages as $index => $image) {

        if ($image && $image->isValid()) {

            try {
                $path = Storage::disk('s3')->put('items/gallery', $image);

                $item->images()->create([
                    'image' => $path,
                    'sort_order' => $index
                ]);

                Log::info('Gallery Uploaded: ' . $path);

            } catch (\Exception $e) {
                Log::error('Gallery Upload Error: ' . $e->getMessage());
            }

        } else {
            Log::warning('Invalid gallery file detected');
        }
    }
}

        DB::commit();

        Log::info('ITEM STORE SUCCESS');

        return redirect()->route('items.index')
            ->with('success', 'Item Created Successfully');

    } catch (\Exception $e) {

        DB::rollBack();

        Log::error('ITEM STORE ERROR: ' . $e->getMessage());
        Log::error($e->getTraceAsString());

        return back()->with('error', $e->getMessage());
    }
}

/*----------------------------------------------------
EDIT
----------------------------------------------------*/
public function edit(Item $item)
{
    $item->load('images');

    return view('master.items.edit', [
        'item' => $item,
        'manufacturers' => Manufacturer::select('id','name')->get(),
        'categories' => Category::select('id','name')->get(),
        'subCategories' => SubCategory::select('id','name')->get(),
        'units' => Unit::select('id','name')->get(),
        'packTypes' => PackType::select('id','name')->get(),
    ]);
}

/*----------------------------------------------------
UPDATE
----------------------------------------------------*/
public function update(Request $request, Item $item)
{
    Log::info('ITEM UPDATE START', ['item_id' => $item->id]);

    DB::beginTransaction();

    try {

        // 🔹 MAIN IMAGE
        if ($request->hasFile('main_image')) {

            Log::info('Replacing main image');

            if ($item->main_image) {
                Storage::disk('s3')->delete($item->main_image);
            }

            $item->main_image = Storage::disk('s3')
                ->put('items/main', $request->file('main_image'));
        }

        // 🔹 UPDATE DATA
        $item->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),

            'description' => $request->description,
            'product_highlights' => $request->product_highlights,
            'brand' => $request->brand,

            'gst_percent' => $request->gst_percent ?? 0,
            'pack_id' => $request->pack_id,
            'unit_id' => $request->unit_id,

            'category_id' => $request->category_id,
            'sub_category_id' => $request->sub_category_id,

            'manufacturer_id' => $request->manufacturer_id,
        ]);

        // 🔹 DELETE GALLERY
        if ($request->deleted_images) {

            foreach ($request->deleted_images as $id) {

                $img = $item->images()->find($id);

                if ($img) {
                    Storage::disk('s3')->delete($img->image);
                    $img->delete();
                }
            }
        }

        // 🔹 ADD NEW GALLERY
       $galleryImages = $request->file('gallery_images');

if (!empty($galleryImages)) {

    foreach ($galleryImages as $index => $image) {

        if ($image && $image->isValid()) {

            try {
                $path = Storage::disk('s3')->put('items/gallery', $image);

                $item->images()->create([
                    'image' => $path,
                    'sort_order' => $index
                ]);

                Log::info('Update Gallery Uploaded: ' . $path);

            } catch (\Exception $e) {
                Log::error('Update Gallery Error: ' . $e->getMessage());
            }

        } else {
            Log::warning('Invalid gallery file in update');
        }
    }
}

        DB::commit();

        Log::info('ITEM UPDATE SUCCESS');

        return redirect()->route('items.index')
            ->with('success', 'Item Updated Successfully');

    } catch (\Exception $e) {

        DB::rollBack();

        Log::error('ITEM UPDATE ERROR: ' . $e->getMessage());
        Log::error($e->getTraceAsString());

        return back()->with('error', $e->getMessage());
    }
}

/*----------------------------------------------------
DELETE
----------------------------------------------------*/
public function destroy(Item $item)
{
    Log::info('ITEM DELETE START', ['item_id' => $item->id]);

    DB::beginTransaction();

    try {

        if ($item->main_image) {
            Storage::disk('s3')->delete($item->main_image);
        }

        foreach ($item->images as $img) {
            Storage::disk('s3')->delete($img->image);
        }

        $item->delete();

        DB::commit();

        Log::info('ITEM DELETE SUCCESS');

        return redirect()->route('items.index')
            ->with('success', 'Item Deleted');

    } catch (\Exception $e) {

        DB::rollBack();

        Log::error('ITEM DELETE ERROR: ' . $e->getMessage());
        Log::error($e->getTraceAsString());

        return back()->with('error', $e->getMessage());
    }
}

}