<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
   public function run(): void
{
    $this->call([
    ManufacturerSeeder::class,   // ✅ FIRST (important)
    DoctorSeeder::class,
    CategorySeeder::class,
    SubCategorySeeder::class,
    SupplierSeeder::class,
    CustomerSeeder::class,

    UnitSeeder::class,
    PackTypeSeeder::class,

    ItemsSeeder::class,          // ❌ ALWAYS LAST
]);
}
}