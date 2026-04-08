<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SupplierItemCatalog;
use App\Models\SupplierStock;
use App\Models\Supplier;
use App\Models\Item;

class SupplierItemCatalogSeeder extends Seeder
{
    public function run()
    {
        // 🔥 Random supplier & items fetch
        $suppliers = Supplier::pluck('id')->toArray();
        $items = Item::pluck('id')->toArray();

        if (empty($suppliers) || empty($items)) {
            $this->command->error('Suppliers or Items not found. Seed them first.');
            return;
        }

        foreach (range(1, 20) as $i) {

            $catalog = SupplierItemCatalog::create([
                'supplier_id' => $suppliers[array_rand($suppliers)],
                'item_id' => $items[array_rand($items)],
                'purchase_price' => rand(50, 200),
                'base_price' => rand(60, 220),
                'retailer_price' => rand(80, 300),
                'retailer_mrp' => rand(100, 350),
                'is_price_editable' => rand(0, 1),
                'current_stock' => 0, // 🔥 always calculated
                'supplier_margin' => rand(5, 20),
                'retailer_margin' => rand(10, 30),
                'expiry_date' => now()->addMonths(rand(3, 24)),
                'batch_no' => 'BATCH-' . strtoupper(uniqid()),
                'gst_percent' => [5, 12, 18][array_rand([5, 12, 18])],
                'free_qty' => rand(0, 10),
                'is_active' => 1
            ]);

            // 🔥 Create stock entries
            $purchaseQty = rand(50, 200);
            $saleQty = rand(10, 50);

            SupplierStock::create([
                'supplier_item_catalog_id' => $catalog->id,
                'qty' => $purchaseQty,
                'type' => 'purchase',
                'reference_id' => rand(1000, 9999),
                'note' => 'Initial Purchase Stock'
            ]);

            SupplierStock::create([
                'supplier_item_catalog_id' => $catalog->id,
                'qty' => -$saleQty,
                'type' => 'sale',
                'reference_id' => rand(1000, 9999),
                'note' => 'Sample Sale Deduction'
            ]);
        }

        $this->command->info('Supplier Item Catalog Seeded Successfully!');
    }
}