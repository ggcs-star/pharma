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
use Maatwebsite\Excel\Facades\Excel;

class ItemController extends Controller
{

/*----------------------------------------------------
ITEM LIST
----------------------------------------------------*/
public function index(Request $request)
{
    $query = Item::with([
        'manufacturer',
        'category',
        'subCategory',
        'unit',
        'images',
            'packings.packType' // 🔥 ADD THIS

    ]);

    // 🔍 SEARCH
    if ($request->search) {
        $query->where(function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->search . '%')
              ->orWhere('brand', 'like', '%' . $request->search . '%')
              ->orWhere('molecule', 'like', '%' . $request->search . '%');
        });
    }

    // 🏭 MANUFACTURER
    if ($request->manufacturer) {
        $query->where('manufacturer_id', $request->manufacturer);
    }

    // 📦 CATEGORY
    if ($request->category) {
        $query->where('category_id', $request->category);
    }

    // 🚦 STATUS
    if ($request->status == 'active') {
        $query->where('inactive', 0);
    }

    if ($request->status == 'inactive') {
        $query->where('inactive', 1);
    }

    $items = $query->latest()->paginate(20)->withQueryString();

    return view('master.items.index', [
        'items' => $items,
        'manufacturers' => Manufacturer::all(),
        'categories' => Category::all(),
    ]);
}

// public function import(Request $request)
// {
//     try {
//         ini_set('memory_limit', '1024M');
//         set_time_limit(0);

//         DB::beginTransaction();

//         $file = $request->file('file');

//         if (!$file) {
//             return back()->with('error', 'File not uploaded');
//         }

//         $data = Excel::toArray([], $file);
//         $sheet = $data[0];

//         if (empty($sheet)) {
//             return back()->with('error', 'Excel file is empty');
//         }

//         // =========================
//         // HEADER CLEAN
//         // =========================
//         $header = array_map(function ($h) {
//             return strtolower(trim(str_replace([' ', '-'], '_', $h)));
//         }, $sheet[0]);

//         $header = array_filter($header);
//         $header = array_values($header);

//         Log::info('IMPORT HEADER CLEAN', $header);

//         $processedCount = 0;
//         $skippedCount = 0;

//         // Process from row 1 (skip header)
//         for ($rowIndex = 1; $rowIndex < count($sheet); $rowIndex++) {
            
//             $row = $sheet[$rowIndex];
            
//             // Skip completely empty rows
//             if (empty(array_filter($row))) {
//                 $skippedCount++;
//                 continue;
//             }
            
//             // Ensure row has enough columns
//             $row = array_pad($row, count($header), null);
//             $row = array_slice($row, 0, count($header));
            
//             // Create associative array
//             $rowData = [];
//             foreach ($header as $idx => $key) {
//                 $rowData[$key] = $row[$idx] ?? null;
//             }

//             Log::info("ROW DATA (Row {$rowIndex})", $rowData);

//             // =========================
//             // NAME (Required field)
//             // =========================
//             $name = $rowData['product_name'] ?? $rowData['name'] ?? null;

//             if (empty($name)) {
//                 Log::warning("EMPTY NAME SKIPPED at row {$rowIndex}");
//                 $skippedCount++;
//                 continue;
//             }

//             // =========================
//             // CATEGORY
//             // =========================
//             $categoryId = $this->getCategoryId('Medicine');
//             $subCategoryId = $this->getSubCategoryId('General', $categoryId);

//             // =========================
//             // MANUFACTURER
//             // =========================
//             $marketer = $rowData['marketer'] ?? 'Generic';
//             $manufacturerName = trim(explode(',', $marketer)[0]);
//             $manufacturerId = $this->getManufacturerId($manufacturerName);

//             // =========================
//             // QTY
//             // =========================
//             $units = 1;
//             if (!empty($rowData['qty'])) {
//                 preg_match('/\d+/', (string)$rowData['qty'], $matches);
//                 $units = $matches[0] ?? 1;
//             }

//             // =========================
//             // IMAGE FIX
//             // =========================
//             $imagePath = null;
//             $galleryImages = [];

//             $imageUrls = $rowData['image_url'] ?? null;

//             if (!empty($imageUrls)) {
//                 // Handle different separators
//                 $separator = strpos($imageUrls, '|') !== false ? '|' : (strpos($imageUrls, ',') !== false ? ',' : null);
                
//                 if ($separator) {
//                     $urls = array_map('trim', explode($separator, $imageUrls));
//                 } else {
//                     $urls = [trim($imageUrls)];
//                 }

//                 foreach ($urls as $index => $url) {
//                     if (!empty($url) && filter_var($url, FILTER_VALIDATE_URL)) {
//                         if ($index == 0) {
//                             $imagePath = $url; // main image
//                         } else {
//                             $galleryImages[] = $url; // gallery
//                         }
//                     }
//                 }
//             }

//             // =========================
//             // PRESCRIPTION
//             // =========================
//             $prescription = 0;
//             $prescriptionRequired = $rowData['prescription_required'] ?? null;

//             if (!empty($prescriptionRequired)) {
//                 $prescription = in_array(
//                     strtolower(trim($prescriptionRequired)),
//                     ['yes', '1', 'true', 'required']
//                 ) ? 1 : 0;
//             }

//             // =========================
//             // CREATE / UPDATE ITEM
//             // =========================
//             $itemData = [
//                 'slug' => Str::slug($name),
//                 'manufacturer_id' => $manufacturerId,
//                 'category_id' => $categoryId,
//                 'sub_category_id' => $subCategoryId,
//                 'pack_id' => $this->getPackId($rowData['package'] ?? 'Strip'),
//                 'unit_id' => $this->getUnitId($rowData['product_form'] ?? 'Tablet'),
//                 'number_of_units' => $units,
//                 'gst_percent' => $rowData['gst'] ?? 0,
//                 'hsn_code' => $rowData['hsn'] ?? null,
//                 'rack' => $rowData['rack'] ?? null,
//                 'barcode' => $rowData['barcode'] ?? null,
//                 'molecule' => $rowData['composition'] ?? null,
//                 'main_image' => $imagePath,
//                 'description' => $rowData['description'] ?? $rowData['introduction'] ?? null,
//                 'brand' => $rowData['marketer'] ?? null,
//                 'need_prescription' => $prescription,
//                 'product_highlights' => json_encode([
//                     'primary_use' => $rowData['primary_use'] ?? null,
//                     'side_effect' => $rowData['common_side_effect'] ?? null,
//                 ]),
//                 // DEFAULTS
//                 'min_threshold' => 0,
//                 'max_threshold' => 0,
//                 'conversion_factor' => 1,
//                 'unit_ratio' => 1,
//                 'max_self_life' => 0,
//                 'max_discount' => 0,
//                 'not_for_online_sale' => 0,
//                 'inactive' => 0,
//                 'block_purchase' => 0,
//                 'service_item' => 0,
//                 'sell_loose' => 0,
//                 'override_loose' => 0,
//                 'notes' => json_encode($rowData),
//             ];

//             // Check if item exists to update or create new
//             $item = Item::where('name', $name)->first();
            
//             if ($item) {
//                 $item->update($itemData);
//                 Log::info("ITEM UPDATED: {$item->id} - {$name}");
//             } else {
//                 $item = Item::create($itemData);
//                 Log::info("ITEM CREATED: {$item->id} - {$name}");
//             }

//             // =========================
//             // SAVE GALLERY IMAGES (Delete old ones if updating)
//             // =========================
//             if (!empty($galleryImages)) {
//                 // Optional: Delete existing gallery images when updating
//                 // DB::table('item_images')->where('item_id', $item->id)->delete();
                
//                 foreach ($galleryImages as $key => $img) {
//                     DB::table('item_images')->updateOrInsert(
//                         [
//                             'item_id' => $item->id,
//                             'image' => $img,
//                         ],
//                         [
//                             'sort_order' => $key + 1,
//                             'updated_at' => now(),
//                             'created_at' => now(),
//                         ]
//                     );
//                 }
//             }

//             $processedCount++;
//         }

//         DB::commit();

//         $message = "✅ Excel Imported Successfully. Processed: {$processedCount} items, Skipped: {$skippedCount} rows.";
        
//         Log::info($message);
        
//         return back()->with('success', $message);

//     } catch (\Exception $e) {
//         DB::rollBack();
//         Log::error('IMPORT ERROR: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
//         return back()->with('error', 'Import failed: ' . $e->getMessage());
//     }
// }
// public function import(Request $request)
// {
//     try {
//         ini_set('memory_limit', '1024M');
//         set_time_limit(0);

//         DB::beginTransaction();

//         $file = $request->file('file');

//         if (!$file) {
//             return back()->with('error', 'File not uploaded');
//         }

//         $data = Excel::toArray([], $file);
//         $sheet = $data[0];

//         if (empty($sheet)) {
//             return back()->with('error', 'Excel file is empty');
//         }

//         // =========================
//         // HEADER CLEAN
//         // =========================
//         $header = array_map(function ($h) {
//             return strtolower(trim(str_replace([' ', '-'], '_', $h)));
//         }, $sheet[0]);

//         $header = array_filter($header);
//         $header = array_values($header);

//         Log::info('IMPORT HEADER CLEAN', $header);

//         $processedCount = 0;
//         $skippedCount = 0;

//         // Process from row 1 (skip header)
//         for ($rowIndex = 1; $rowIndex < count($sheet); $rowIndex++) {
            
//             $row = $sheet[$rowIndex];
            
//             // Skip completely empty rows
//             if (empty(array_filter($row))) {
//                 $skippedCount++;
//                 continue;
//             }
            
//             // Ensure row has enough columns
//             $row = array_pad($row, count($header), null);
//             $row = array_slice($row, 0, count($header));
            
//             // Create associative array
//             $rowData = [];
//             foreach ($header as $idx => $key) {
//                 $rowData[$key] = $row[$idx] ?? null;
//             }

//             Log::info("ROW DATA (Row {$rowIndex})", $rowData);

//             // =========================
//             // NAME (Required field)
//             // =========================
//             $name = $rowData['product_name'] ?? $rowData['name'] ?? null;

//             if (empty($name)) {
//                 Log::warning("EMPTY NAME SKIPPED at row {$rowIndex}");
//                 $skippedCount++;
//                 continue;
//             }

//             // Add timestamp to make name unique if needed
//             // Uncomment if you want to force unique names on each import
//             // $name = $name . ' - ' . now()->format('Y-m-d H:i:s');

//             // =========================
//             // CATEGORY
//             // =========================
//             $categoryId = $this->getCategoryId('Medicine');
//             $subCategoryId = $this->getSubCategoryId('General', $categoryId);

//             // =========================
//             // MANUFACTURER
//             // =========================
//             $marketer = $rowData['marketer'] ?? 'Generic';
//             $manufacturerName = trim(explode(',', $marketer)[0]);
//             $manufacturerId = $this->getManufacturerId($manufacturerName);

//             // =========================
//             // QTY
//             // =========================
//             $units = 1;
//             if (!empty($rowData['qty'])) {
//                 preg_match('/\d+/', (string)$rowData['qty'], $matches);
//                 $units = $matches[0] ?? 1;
//             }

//             // =========================
//             // IMAGE FIX
//             // =========================
//             $imagePath = null;
//             $galleryImages = [];

//             $imageUrls = $rowData['image_url'] ?? null;

//             if (!empty($imageUrls)) {
//                 $separator = strpos($imageUrls, '|') !== false ? '|' : (strpos($imageUrls, ',') !== false ? ',' : null);
                
//                 if ($separator) {
//                     $urls = array_map('trim', explode($separator, $imageUrls));
//                 } else {
//                     $urls = [trim($imageUrls)];
//                 }

//                 foreach ($urls as $index => $url) {
//                     if (!empty($url) && filter_var($url, FILTER_VALIDATE_URL)) {
//                         if ($index == 0) {
//                             $imagePath = $url;
//                         } else {
//                             $galleryImages[] = $url;
//                         }
//                     }
//                 }
//             }

//             // =========================
//             // PRESCRIPTION
//             // =========================
//             $prescription = 0;
//             $prescriptionRequired = $rowData['prescription_required'] ?? null;

//             if (!empty($prescriptionRequired)) {
//                 $prescription = in_array(
//                     strtolower(trim($prescriptionRequired)),
//                     ['yes', '1', 'true', 'required']
//                 ) ? 1 : 0;
//             }

//             // =========================
//             // CREATE NEW ITEM (ALWAYS) 🔥
//             // =========================
//             $itemData = [
//                 'name' => $name,  // Include name in create
//                 'slug' => Str::slug($name) . '-' . uniqid(), // Add unique ID to slug
//                 'manufacturer_id' => $manufacturerId,
//                 'category_id' => $categoryId,
//                 'sub_category_id' => $subCategoryId,
//                 'pack_id' => $this->getPackId($rowData['package'] ?? 'Strip'),
//                 'unit_id' => $this->getUnitId($rowData['product_form'] ?? 'Tablet'),
//                 'number_of_units' => $units,
//                 'gst_percent' => $rowData['gst'] ?? 0,
//                 'hsn_code' => $rowData['hsn'] ?? null,
//                 'rack' => $rowData['rack'] ?? null,
//                 'barcode' => $rowData['barcode'] ?? null,
//                 'molecule' => $rowData['composition'] ?? null,
//                 'main_image' => $imagePath,
//                 'description' => $rowData['description'] ?? $rowData['introduction'] ?? null,
//                 'brand' => $rowData['marketer'] ?? null,
//                 'need_prescription' => $prescription,
//                 'product_highlights' => json_encode([
//                     'primary_use' => $rowData['primary_use'] ?? null,
//                     'side_effect' => $rowData['common_side_effect'] ?? null,
//                 ]),
//                 'min_threshold' => 0,
//                 'max_threshold' => 0,
//                 'conversion_factor' => 1,
//                 'unit_ratio' => 1,
//                 'max_self_life' => 0,
//                 'max_discount' => 0,
//                 'not_for_online_sale' => 0,
//                 'inactive' => 0,
//                 'block_purchase' => 0,
//                 'service_item' => 0,
//                 'sell_loose' => 0,
//                 'override_loose' => 0,
//                 'notes' => json_encode($rowData),
//             ];

//             // ALWAYS CREATE NEW RECORD
//             $item = Item::create($itemData);
            
//             Log::info("ITEM CREATED: {$item->id} - {$name}");

//             // =========================
//             // SAVE GALLERY IMAGES
//             // =========================
//             if (!empty($galleryImages)) {
//                 foreach ($galleryImages as $key => $img) {
//                     DB::table('item_images')->insert([
//                         'item_id' => $item->id,
//                         'image' => $img,
//                         'sort_order' => $key + 1,
//                         'created_at' => now(),
//                         'updated_at' => now(),
//                     ]);
//                 }
//             }

//             $processedCount++;
//         }

//         DB::commit();

//         $message = "✅ Excel Imported Successfully. Created: {$processedCount} new items, Skipped: {$skippedCount} rows.";
        
//         Log::info($message);
        
//         return back()->with('success', $message);

//     } catch (\Exception $e) {
//         DB::rollBack();
//         Log::error('IMPORT ERROR: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
//         return back()->with('error', 'Import failed: ' . $e->getMessage());
//     }
// }
public function import(Request $request)
{
    try {
        ini_set('memory_limit', '1024M');
        set_time_limit(0);

        DB::beginTransaction();

        $file = $request->file('file');

        if (!$file) {
            return back()->with('error', 'File not uploaded');
        }

        $data = Excel::toArray([], $file);
        $sheet = $data[0];

        if (empty($sheet)) {
            return back()->with('error', 'Excel file is empty');
        }

        // =========================
        // HEADER CLEAN
        // =========================
$header = array_map(function ($h) {

    $h = preg_replace('/([a-z])([A-Z])/', '$1_$2', $h);

    $h = strtolower(trim($h));

    $h = str_replace([' ', '-'], '_', $h);

    return $h;

}, $sheet[0]);

        $header = array_filter($header);
        $header = array_values($header);

        Log::info('IMPORT HEADER CLEAN', $header);

        $processedCount = 0;
        $skippedCount = 0;

        // Process from row 1 (skip header)
        for ($rowIndex = 1; $rowIndex < count($sheet); $rowIndex++) {
            
            $row = $sheet[$rowIndex];
            
            // Skip completely empty rows
            if (empty(array_filter($row))) {
                $skippedCount++;
                continue;
            }
            
            // Ensure row has enough columns
            $row = array_pad($row, count($header), null);
            $row = array_slice($row, 0, count($header));
            
            // Create associative array
            $rowData = [];
            foreach ($header as $idx => $key) {
                $rowData[$key] = $row[$idx] ?? null;
            }

            Log::info("ROW DATA (Row {$rowIndex})", $rowData);

            // =========================
            // NAME (Required field)
            // =========================
            $name = $rowData['product_name'] ?? $rowData['name'] ?? null;

            if (empty($name)) {
                Log::warning("EMPTY NAME SKIPPED at row {$rowIndex}");
                $skippedCount++;
                continue;
            }

// =========================
// AUTO CATEGORY DETECTION
// =========================

$text = strtolower(

    ($rowData['product_name'] ?? '') . ' ' .

    ($rowData['composition'] ?? '') . ' ' .

    ($rowData['introduction'] ?? '') . ' ' .

    ($rowData['primary_use'] ?? '')
);

$categoryName = 'General';

/*
|--------------------------------------------------------------------------
| DIABETES
|--------------------------------------------------------------------------
*/

if (

    str_contains($text, 'metformin') ||

    str_contains($text, 'glimepiride') ||

    str_contains($text, 'diabetes')

) {

    $categoryName = 'Diabetes';
}

/*
|--------------------------------------------------------------------------
| HEART CARE
|--------------------------------------------------------------------------
*/

elseif (

    str_contains($text, 'atorvastatin') ||

    str_contains($text, 'telmisartan') ||

    str_contains($text, 'cardiac') ||

    str_contains($text, 'blood pressure')

) {

    $categoryName = 'Heart Care';
}

/*
|--------------------------------------------------------------------------
| STOMACH CARE
|--------------------------------------------------------------------------
*/

elseif (

    str_contains($text, 'pantoprazole') ||

    str_contains($text, 'rabeprazole') ||

    str_contains($text, 'acidity') ||

    str_contains($text, 'gastric')

) {

    $categoryName = 'Stomach Care';
}

/*
|--------------------------------------------------------------------------
| LIVER CARE
|--------------------------------------------------------------------------
*/

elseif (

    str_contains($text, 'ursodeoxycholic') ||

    str_contains($text, 'liver')

) {

    $categoryName = 'Liver Care';
}

/*
|--------------------------------------------------------------------------
| BONE / JOINT
|--------------------------------------------------------------------------
*/

elseif (

    str_contains($text, 'calcium') ||

    str_contains($text, 'joint') ||

    str_contains($text, 'arthritis')

) {

    $categoryName = 'Bone, Joint & Muscle Care';
}

/*
|--------------------------------------------------------------------------
| KIDNEY CARE
|--------------------------------------------------------------------------
*/

elseif (

    str_contains($text, 'renal') ||

    str_contains($text, 'kidney')

) {

    $categoryName = 'Kidney Care';
}

/*
|--------------------------------------------------------------------------
| DERMA CARE
|--------------------------------------------------------------------------
*/

elseif (

    str_contains($text, 'skin') ||

    str_contains($text, 'clindamycin') ||

    str_contains($text, 'acne')

) {

    $categoryName = 'Derma Care';
}

/*
|--------------------------------------------------------------------------
| RESPIRATORY CARE
|--------------------------------------------------------------------------
*/

elseif (

    str_contains($text, 'asthma') ||

    str_contains($text, 'cough') ||

    str_contains($text, 'montelukast')

) {

    $categoryName = 'Respiratory Care';
}

/*
|--------------------------------------------------------------------------
| EYE CARE
|--------------------------------------------------------------------------
*/

elseif (

    str_contains($text, 'eye') ||

    str_contains($text, 'ophthalmic')

) {

    $categoryName = 'Eye Care';
}

// =========================
// CATEGORY CREATE
// =========================

$categoryId = $this->getCategoryId($categoryName);

$subCategoryId = $this->getSubCategoryId(
    'General',
    $categoryId
);
            // =========================
            // MANUFACTURER
            // =========================
            $marketer = $rowData['marketer'] ?? 'Generic';
            $manufacturerName = trim(explode(',', $marketer)[0]);
            $manufacturerId = $this->getManufacturerId($manufacturerName);

            // =========================
            // QTY
            // =========================
            $units = 1;
            if (!empty($rowData['qty'])) {
                preg_match('/\d+/', (string)$rowData['qty'], $matches);
                $units = $matches[0] ?? 1;
            }

            // =========================
            // IMAGE FIX
            // =========================
            $imagePath = null;
            $galleryImages = [];

            $imageUrls = $rowData['image_url'] ?? null;

            if (!empty($imageUrls)) {
                $separator = strpos($imageUrls, '|') !== false ? '|' : (strpos($imageUrls, ',') !== false ? ',' : null);
                
                if ($separator) {
                    $urls = array_map('trim', explode($separator, $imageUrls));
                } else {
                    $urls = [trim($imageUrls)];
                }

                foreach ($urls as $index => $url) {
                    if (!empty($url) && filter_var($url, FILTER_VALIDATE_URL)) {
                        if ($index == 0) {
                            $imagePath = $url;
                        } else {
                            $galleryImages[] = $url;
                        }
                    }
                }
            }

            // =========================
            // PRESCRIPTION (FIXED) 🔥
            // =========================
            $prescription = 0;
            // Check both possible column names from Excel
            $prescriptionRequired = $rowData['prescription_required'] ?? $rowData['need_prescription'] ?? null;
            
            if (!empty($prescriptionRequired)) {
                $prescription = in_array(
                    strtolower(trim($prescriptionRequired)),
                    ['yes', '1', 'true', 'required', 'prescription required', 'prescription']
                ) ? 1 : 0;
            }

            // =========================
            // CREATE NEW ITEM (ALWAYS)
            // =========================
      $itemData = [

    'name' => $name,

    'slug' => Str::slug($name) . '-' . uniqid(),

    'manufacturer_id' => $manufacturerId,

    'category_id' => $categoryId,

    'sub_category_id' => $subCategoryId,

    'pack_id' => $this->getPackId($rowData['package'] ?? 'Strip'),

    'unit_id' => $this->getUnitId($rowData['product_form'] ?? 'Tablet'),

    'number_of_units' => $units,

    'gst_percent' => $rowData['gst'] ?? 0,

    'hsn_code' => $rowData['hsn'] ?? null,

    'rack' => $rowData['rack'] ?? null,

    'barcode' => $rowData['barcode'] ?? null,

    'molecule' => $rowData['composition'] ?? null,

    'main_image' => $imagePath,

    'description' => $rowData['description'] ?? null,

    'brand' => $this->extractBrandName($name),

    'need_prescription' => $prescription,

    // =====================================================
    // NEW MEDICINE CONTENT
    // =====================================================

    'medicine_type' => $rowData['medicine_type'] ?? null,

    'introduction' => $rowData['introduction'] ?? null,

    'how_to_use' => $rowData['how_to_use'] ?? null,

    'safety_advise' => $rowData['safety_advise'] ?? null,

    'if_miss' => $rowData['if_miss'] ?? null,

    'how_it_works' => $rowData['how_it_works'] ?? null,

    'interaction' => $rowData['interaction'] ?? null,

    // =====================================================
    // USES / STORAGE / SIDE EFFECTS
    // =====================================================

    'primary_use' => $rowData['primary_use'] ?? null,

    'storage' => $rowData['storage'] ?? null,

    'common_side_effect' => $rowData['common_side_effect'] ?? null,

    // =====================================================
    // SAFETY INTERACTIONS
    // =====================================================

 'alcohol_interaction' => $rowData['alcohol_interaction'] ?? null,

'pregnancy_interaction' => $rowData['pregnancy_interaction'] ?? null,

'lactation_interaction' => $rowData['lactation_interaction'] ?? null,

'driving_interaction' => $rowData['driving_interaction'] ?? null,

'kidney_interaction' => $rowData['kidney_interaction'] ?? null,

'liver_interaction' => $rowData['liver_interaction'] ?? null,

    // =====================================================
    // EXTRA INFO
    // =====================================================

    'fact_box' => json_encode([
        'primary_use' => $rowData['primary_use'] ?? null,
    ]),

'quick_tips' => $rowData['quick_tips'] ?? null,
'q_a' => $rowData['q_a'] ?? null,

    // =====================================================
    // MANUFACTURER INFO
    // =====================================================

    'manufacturer_address' => $rowData['manufacturer_address'] ?? null,

    'country_of_origin' => $rowData['country_of_origin'] ?? null,

'manufacturer_details' => null,
    'marketer_details' => $rowData['marketer_details'] ?? null,

    // =====================================================
    // SETTINGS
    // =====================================================

    'min_threshold' => 0,

    'max_threshold' => 0,

    'conversion_factor' => 1,

    'unit_ratio' => 1,

    'max_self_life' => 0,

    'max_discount' => 0,

    'not_for_online_sale' => 0,

    'inactive' => 0,

    'block_purchase' => 0,

    'service_item' => 0,

    'sell_loose' => 0,

    'override_loose' => 0,

    'notes' => json_encode($rowData),

];

            // ALWAYS CREATE NEW RECORD
            $item = Item::create($itemData);
            
            Log::info("ITEM CREATED: {$item->id} - {$name} - Prescription: {$prescription}");
// =========================
// PACKAGING SAVE 🔥
// =========================

$packagingDetail = $rowData['packaging_detail'] ?? null;

// 🔥 qty extract (smart)
$units = 1;

if (!empty($rowData['qty'])) {
    preg_match('/\d+/', (string)$rowData['qty'], $matches);
    $units = $matches[0] ?? 1;
}
elseif (!empty($packagingDetail)) {
    preg_match('/\d+/', (string)$packagingDetail, $matches);
    $units = $matches[0] ?? 1;
}

// 🔥 clean pack name
$packName = strtolower(trim($rowData['package'] ?? 'strip'));
$packName = ucfirst($packName);

// 🔥 insert into item_packings
DB::table('item_packings')->updateOrInsert(
    [
        'item_id' => $item->id,
        'pack_id' => $this->getPackId($packName),
        'qty' => $units,
    ],
    [
        'packaging_detail' => $packagingDetail,
        'product_form' => $rowData['product_form'] ?? null,
        'updated_at' => now(),
        'created_at' => now(),
    ]
);
            // =========================
            // SAVE GALLERY IMAGES
            // =========================
            if (!empty($galleryImages)) {
                foreach ($galleryImages as $key => $img) {
                    DB::table('item_images')->insert([
                        'item_id' => $item->id,
                        'image' => $img,
                        'sort_order' => $key + 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            $processedCount++;
        }

        DB::commit();

        $message = "✅ Excel Imported Successfully. Created: {$processedCount} new items, Skipped: {$skippedCount} rows.";
        
        Log::info($message);
        
        return back()->with('success', $message);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('IMPORT ERROR: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
        return back()->with('error', 'Import failed: ' . $e->getMessage());
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
    if (!$name) {
        return null;
    }

    $name = trim($name);

    // Case-insensitive search
    $manufacturer = Manufacturer::whereRaw(
        'LOWER(name) = ?',
        [strtolower($name)]
    )->first();

    // Existing found
    if ($manufacturer) {
        return $manufacturer->id;
    }

    // Create new
    return Manufacturer::create([
        'name' => $name,
        'status' => 1
    ])->id;
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
'slug' => $this->generateSlug($request->name),  
          'main_image' => $mainImagePath,


            'description' => $request->description,
            'brand' => $request->brand,
// =====================================================
// MEDICINE CONTENT
// =====================================================

'medicine_type' => $request->medicine_type,

'introduction' => $request->introduction,

'how_to_use' => $request->how_to_use,

'safety_advise' => $request->safety_advise,

'if_miss' => $request->if_miss,

'how_it_works' => $request->how_it_works,

'interaction' => $request->interaction,

// =====================================================
// USES / STORAGE
// =====================================================

'primary_use' => $request->primary_use,

'storage' => $request->storage,

'common_side_effect' => $request->common_side_effect,

// =====================================================
// SAFETY INTERACTIONS
// =====================================================

'alcohol_interaction' => $request->alcohol_interaction,

'pregnancy_interaction' => $request->pregnancy_interaction,

'lactation_interaction' => $request->lactation_interaction,

'driving_interaction' => $request->driving_interaction,

'kidney_interaction' => $request->kidney_interaction,

'liver_interaction' => $request->liver_interaction,

// =====================================================
// EXTRA JSON
// =====================================================

'fact_box' => $request->fact_box,

'quick_tips' => $request->quick_tips,

'q_a' => $request->q_a,

// =====================================================
// MANUFACTURER INFO
// =====================================================

'manufacturer_address' => $request->manufacturer_address,

'country_of_origin' => $request->country_of_origin,

'manufacturer_details' => $request->manufacturer_details,

'marketer_details' => $request->marketer_details,
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

        return redirect()->route('master.items.index')
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
    DB::beginTransaction();

    try {

        // =====================================================
        // MAIN IMAGE
        // =====================================================

        if ($request->hasFile('main_image')) {

            if ($item->main_image) {
                Storage::disk('s3')->delete($item->main_image);
            }

            $item->main_image = Storage::disk('s3')
                ->put('items/main', $request->file('main_image'));
        }

        // =====================================================
        // REMOVE MAIN IMAGE
        // =====================================================

        if ($request->remove_main_image) {

            if ($item->main_image) {
                Storage::disk('s3')->delete($item->main_image);
            }

            $item->main_image = null;
        }

        // =====================================================
        // UPDATE ITEM
        // =====================================================

        $item->update([

            // BASIC
            'name' => $request->name,

            'slug' => $this->generateSlug($request->name, $item->id),

            'description' => $request->description,

            'brand' => $request->brand,

            // =====================================================
            // MEDICINE CONTENT
            // =====================================================

            'medicine_type' => $request->medicine_type,

            'introduction' => $request->introduction,

            'how_to_use' => $request->how_to_use,

            'safety_advise' => $request->safety_advise,

            'if_miss' => $request->if_miss,

            'how_it_works' => $request->how_it_works,

            'interaction' => $request->interaction,

            // =====================================================
            // USES / STORAGE
            // =====================================================

            'primary_use' => $request->primary_use,

            'storage' => $request->storage,

            'common_side_effect' => $request->common_side_effect,

            // =====================================================
            // SAFETY INTERACTIONS
            // =====================================================

            'alcohol_interaction' => $request->alcohol_interaction,

            'pregnancy_interaction' => $request->pregnancy_interaction,

            'lactation_interaction' => $request->lactation_interaction,

            'driving_interaction' => $request->driving_interaction,

            'kidney_interaction' => $request->kidney_interaction,

            'liver_interaction' => $request->liver_interaction,

            // =====================================================
            // EXTRA JSON
            // =====================================================

            'fact_box' => $request->fact_box,

            'quick_tips' => $request->quick_tips,

            'q_a' => $request->q_a,

            // =====================================================
            // MANUFACTURER INFO
            // =====================================================

            'manufacturer_address' => $request->manufacturer_address,

            'country_of_origin' => $request->country_of_origin,

            'manufacturer_details' => $request->manufacturer_details,

            'marketer_details' => $request->marketer_details,

            // =====================================================
            // ERP CORE
            // =====================================================

            'gst_percent' => $request->gst_percent ?? 0,

            'pack_id' => $request->pack_id,

            'unit_id' => $request->unit_id,

            'number_of_units' => $request->number_of_units ?? 1,

            'category_id' => $request->category_id,

            'sub_category_id' => $request->sub_category_id,

            'manufacturer_id' => $request->manufacturer_id,

            'molecule' => $request->molecule,

            'barcode' => $request->barcode,

            'rack' => $request->rack,

            'hsn_code' => $request->hsn_code,

            'sch_type' => $request->sch_type,

            // =====================================================
            // STOCK ALERT
            // =====================================================

            'min_threshold' => $request->min_threshold ?? 0,

            'max_threshold' => $request->max_threshold ?? 0,

            // =====================================================
            // CONVERSION
            // =====================================================

            'conversion_factor' => $request->conversion_factor ?? 1,

            'unit_ratio' => $request->unit_ratio ?? 1,

            // =====================================================
            // SETTINGS
            // =====================================================

            'max_discount' => $request->max_discount ?? 0,

            'inactive' => $request->boolean('inactive'),

            'need_prescription' => $request->boolean('need_prescription'),

            'not_for_online_sale' => $request->boolean('not_for_online_sale'),

            'block_purchase' => $request->boolean('block_purchase'),

            'service_item' => $request->boolean('service_item'),

            'sell_loose' => $request->boolean('sell_loose'),

            'override_loose' => $request->boolean('override_loose'),

            // =====================================================
            // EXTRA
            // =====================================================

            'notes' => $request->notes,

        ]);

        // =====================================================
        // BATCHES
        // =====================================================

        $item->batches()->delete();

        if ($request->batches) {

            foreach ($request->batches as $batch) {

                $item->batches()->create([

                    'batch_code' => $batch['batch_code'] ?? null,

                    'stock' => $batch['stock'] ?? 0,

                    'expiry_date' => $batch['expiry_date'] ?? null,

                    'mrp' => $batch['mrp'] ?? 0,

                    'ptr' => $batch['ptr'] ?? 0,

                    'discount' => $batch['discount'] ?? 0,

                    'margin' => $batch['margin'] ?? 0,

                    'markup' => $batch['markup'] ?? 0,

                    'selling_price' => $batch['selling_price'] ?? 0,

                ]);
            }
        }

        // =====================================================
        // DELETE GALLERY
        // =====================================================

        if ($request->deleted_gallery) {

            $deleted = json_decode($request->deleted_gallery, true);

            foreach ($deleted as $imgData) {

                $img = $item->images()->find($imgData['id']);

                if ($img) {

                    Storage::disk('s3')->delete($img->image);

                    $img->delete();
                }
            }
        }

        // =====================================================
        // ADD NEW GALLERY
        // =====================================================

        if ($request->hasFile('gallery_images')) {

            foreach ($request->file('gallery_images') as $index => $image) {

                $path = Storage::disk('s3')
                    ->put('items/gallery', $image);

                $item->images()->create([

                    'image' => $path,

                    'sort_order' => $index

                ]);
            }
        }

        DB::commit();

        return redirect()
            ->route('master.items.index')
            ->with('success', 'Item Updated Successfully');

    } catch (\Exception $e) {

        DB::rollBack();

        Log::error('ITEM UPDATE ERROR: ' . $e->getMessage());

        Log::error($e->getTraceAsString());

        return back()->with('error', $e->getMessage());
    }
}


// public function update(Request $request, Item $item)
// {
//     Log::info('ITEM UPDATE START', ['item_id' => $item->id]);

//     DB::beginTransaction();

//     try {

//         // 🔹 MAIN IMAGE
//         if ($request->hasFile('main_image')) {

//             Log::info('Replacing main image');

//             if ($item->main_image) {
//                 Storage::disk('s3')->delete($item->main_image);
//             }

//             $item->main_image = Storage::disk('s3')
//                 ->put('items/main', $request->file('main_image'));
//         }

//         // 🔹 UPDATE DATA
//         $item->update([
//             'name' => $request->name,
//             'slug' => Str::slug($request->name),

//             'description' => $request->description,
//             'product_highlights' => $request->product_highlights,
//             'brand' => $request->brand,

//             'gst_percent' => $request->gst_percent ?? 0,
//             'pack_id' => $request->pack_id,
//             'unit_id' => $request->unit_id,

//             'category_id' => $request->category_id,
//             'sub_category_id' => $request->sub_category_id,

//             'manufacturer_id' => $request->manufacturer_id,
//         ]);

//         // 🔹 DELETE GALLERY
//         if ($request->deleted_images) {

//             foreach ($request->deleted_images as $id) {

//                 $img = $item->images()->find($id);

//                 if ($img) {
//                     Storage::disk('s3')->delete($img->image);
//                     $img->delete();
//                 }
//             }
//         }

//         // 🔹 ADD NEW GALLERY
//        $galleryImages = $request->file('gallery_images');

// if (!empty($galleryImages)) {

//     foreach ($galleryImages as $index => $image) {

//         if ($image && $image->isValid()) {

//             try {
//                 $path = Storage::disk('s3')->put('items/gallery', $image);

//                 $item->images()->create([
//                     'image' => $path,
//                     'sort_order' => $index
//                 ]);

//                 Log::info('Update Gallery Uploaded: ' . $path);

//             } catch (\Exception $e) {
//                 Log::error('Update Gallery Error: ' . $e->getMessage());
//             }

//         } else {
//             Log::warning('Invalid gallery file in update');
//         }
//     }
// }

//         DB::commit();

//         Log::info('ITEM UPDATE SUCCESS');

//         return redirect()->route('master.items.index')
//             ->with('success', 'Item Updated Successfully');

//     } catch (\Exception $e) {

//         DB::rollBack();

//         Log::error('ITEM UPDATE ERROR: ' . $e->getMessage());
//         Log::error($e->getTraceAsString());

//         return back()->with('error', $e->getMessage());
//     }
// }

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

        return redirect()->route('master.items.index')
            ->with('success', 'Item Deleted');

    } catch (\Exception $e) {

        DB::rollBack();

        Log::error('ITEM DELETE ERROR: ' . $e->getMessage());
        Log::error($e->getTraceAsString());

        return back()->with('error', $e->getMessage());
    }
}
private function extractBrandName($name)
{
    if (!$name) {
        return null;
    }

    $removeWords = [
        'tablet',
        'tablets',
        'capsule',
        'capsules',
        'syrup',
        'injection',
        'cream',
        'gel',
        'drops',
        'dsr',
        'forte',
        'plus',
        'mr',
        'xl',
        'sr'
    ];

    $words = explode(' ', strtolower(trim($name)));

    $filtered = array_filter($words, function ($word) use ($removeWords) {

        // remove numbers
        if (preg_match('/^[0-9]+$/', $word)) {
            return false;
        }

        return !in_array($word, $removeWords);
    });

    return ucfirst(array_values($filtered)[0] ?? '');
}
private function generateSlug($name, $id = null)
{
    $baseSlug = \Illuminate\Support\Str::slug($name);
    $slug = $baseSlug;
    $i = 1;

    while (
        \App\Models\Item::where('slug', $slug)
            ->when($id, fn($q) => $q->where('id', '!=', $id))
            ->exists()
    ) {
        $slug = $baseSlug . '-' . $i++;
    }

    return $slug;
}

}