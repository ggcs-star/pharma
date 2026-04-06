<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

use App\Models\User;
use App\Models\Manufacturer;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\PackType;
use App\Models\Unit;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\Batch;
use App\Models\Purchase;
use App\Models\PurchaseItem;

class PharmaSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        DB::beginTransaction();

        try {

            // 🔹 ADMIN
            $user = User::firstOrCreate(
                ['email' => 'admin@pharma.com'],
                [
                    'name' => 'Admin',
                    'password' => Hash::make('password')
                ]
            );

            // 🔹 PACK TYPES
            $packs = [
                PackType::firstOrCreate(['name'=>'Strip']),
                PackType::firstOrCreate(['name'=>'Bottle']),
                PackType::firstOrCreate(['name'=>'Box']),
            ];

            // 🔹 UNITS
            $units = [
                Unit::firstOrCreate(['name'=>'Tablet']),
                Unit::firstOrCreate(['name'=>'Capsule']),
                Unit::firstOrCreate(['name'=>'ML']),
            ];

            // 🔹 MANUFACTURERS
            $manufacturers = [];
            for($i=0;$i<5;$i++){
                $manufacturers[] = Manufacturer::create([
                    'name'=>$faker->unique()->company
                ]);
            }

            // 🔹 CATEGORIES
            $categories = [];
            for($i=0;$i<5;$i++){
                $categories[] = Category::create([
                    'name'=>ucfirst($faker->unique()->word)
                ]);
            }

            // 🔹 SUBCATEGORIES
            $subCategories = [];
            foreach($categories as $category){
                for($i=0;$i<2;$i++){
                    $subCategories[] = SubCategory::create([
                        'category_id'=>$category->id,
                        'name'=>ucfirst($faker->word)
                    ]);
                }
            }

            // 🔹 ITEMS
      $items[] = Item::create([

    'name'=>ucfirst($faker->word).' '.rand(100,650).'mg',
    'gst_percent'=>rand(5,18),

    'pack_id'=>$packs[array_rand($packs)]->id,
    'number_of_units'=>rand(10,20),
    'unit_id'=>$units[array_rand($units)]->id,

    'category_id'=>$categories[array_rand($categories)]->id,
    'sub_category_id'=>$subCategories[array_rand($subCategories)]->id,

    'rack'=>'R'.rand(1,20),
    'hsn_code'=>rand(3000,9999),

    'manufacturer_id'=>$manufacturers[array_rand($manufacturers)]->id,

    'molecule'=>ucfirst($faker->word),
    'barcode'=>rand(100000000,999999999),

    'min_threshold'=>10,
    'max_threshold'=>100,

    'need_prescription'=>false,
    'not_for_online_sale'=>false,
    'inactive'=>false,
    'block_purchase'=>false,

    'service_item'=>false,
    'sell_loose'=>true,
    'override_loose' => false, // 🔥 ADD THIS

    'conversion_factor'=>1,
    'unit_ratio'=>1,

    'max_self_life'=>60,
    'max_discount'=>10,

    'notes'=>$faker->sentence
]);

            // 🔹 SUPPLIERS
            $suppliers = [];
            for($i=0;$i<5;$i++){
                $suppliers[] = Supplier::create([
                    'name'=>$faker->company,
                    'phone'=>$faker->phoneNumber,
                    'supplier_code'=>'SUP'.rand(100,999)
                ]);
            }

            // 🔹 CUSTOMERS
            for($i=0;$i<10;$i++){
                Customer::create([
                    'name'=>$faker->name,
                    'contact'=>$faker->phoneNumber,
                    'discount'=>rand(0,10),
                    'customer_type'=>rand(0,1) ? 'regular':'vip',
                    'address'=>$faker->address,
                    'city'=>$faker->city,
                    'pincode'=>rand(100000,999999)
                ]);
            }

            // 🔥 PURCHASE + BATCH + STOCK
            foreach($suppliers as $supplier){

                $purchase = Purchase::create([
                    'supplier_id'=>$supplier->id,
                    'invoice_number'=>'P'.rand(1000,9999),
                    'entry_by'=>$user->id,
                    'gst'=>0,
                    'discount'=>0,
                    'total_amount'=>0,
                    'net_amount'=>0
                ]);

                $total = 0;

                foreach($items as $item){

                    $qty = rand(50,120);
                    $rate = rand(15,40);
                    $gstPercent = $item->gst_percent;

                    $taxable = $qty * $rate;
                    $gstAmount = ($taxable * $gstPercent) / 100;
                    $final = $taxable + $gstAmount;

                    // 🔹 BATCH
                    $batch = Batch::create([
                        'item_id'=>$item->id,
                        'batch_code'=>'B'.rand(10000,99999),
                        'expiry_date'=>now()->addMonths(rand(12,24)),
                        'stock'=>$qty,
                        'mrp'=>$rate + 10,
                        'ptr'=>$rate,
                        'selling_price'=>$rate + 5
                    ]);

                    // 🔹 PURCHASE ITEM (FINAL FIXED)
                    PurchaseItem::create([
                        'purchase_id'=>$purchase->id,
                        'item_id'=>$item->id,
                        'batch_id'=>$batch->id,

                        'quantity'=>$qty,
                        'returned_quantity'=>0,
                        'free_quantity'=>rand(0,5),

                        'mrp'=>$rate + 10,
                        'ptr'=>$rate,

                        'gst_percent'=>$gstPercent,
                        'gst_amount'=>$gstAmount,

                        'discount_percent'=>0,
                        'discount_amount'=>0,

                        'taxable_amount'=>$taxable,
                        'total_amount'=>$final,

                        'barcode'=>$item->barcode,
                        'rack'=>$item->rack,
                        'hsn_code'=>$item->hsn_code,
                    ]);

                    $total += $final;
                }

                $purchase->update([
                    'total_amount'=>$total,
                    'net_amount'=>$total
                ]);
            }

            DB::commit();

        } catch(\Exception $e){
            DB::rollBack();
            throw $e;
        }
    }
}