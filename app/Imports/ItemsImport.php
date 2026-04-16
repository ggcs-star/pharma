<?php
namespace App\Imports;

use App\Models\Item;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Manufacturer;
use App\Models\Unit;
use App\Models\PackType;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;

class ItemsImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        $header = $rows->first()->toArray();
        $rows = $rows->skip(1);

        foreach ($rows as $row) {

            $row = $row->toArray();

            if (count($row) != count($header)) continue;

            $data = array_combine($header, $row);

            if (empty($data['name'])) continue;

            $categoryId = Category::firstOrCreate(['name' => $data['category'] ?? 'Medicine'])->id;

            $subCategoryId = SubCategory::firstOrCreate([
                'name' => $data['sub_category'] ?? 'General',
                'category_id' => $categoryId
            ])->id;

            $manufacturerId = Manufacturer::firstOrCreate([
                'name' => $data['manufacturer'] ?? 'Generic'
            ])->id;

            Item::create([
                'name' => $data['name'],
                'slug' => Str::slug($data['name']),
                'gst_percent' => $data['gst_percent'] ?? 0,

                'pack_id' => PackType::firstOrCreate(['name' => $data['pack'] ?? 'Strip'])->id,
                'unit_id' => Unit::firstOrCreate(['name' => $data['unit'] ?? 'Tablet'])->id,

                'category_id' => $categoryId,
                'sub_category_id' => $subCategoryId,
                'manufacturer_id' => $manufacturerId,

                'number_of_units' => $data['number_of_units'] ?? 1,

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
    }
}