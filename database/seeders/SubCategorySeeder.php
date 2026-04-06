<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubCategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('sub_categories')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('sub_categories')->insert([
            ['name'=>'NSAIDs','category_id'=>1],
            ['name'=>'Analgesics','category_id'=>1],

            ['name'=>'Penicillins','category_id'=>2],

            ['name'=>'Oral Antihistamines','category_id'=>3],

            ['name'=>'Multivitamins','category_id'=>4],

            ['name'=>'Antacids','category_id'=>5],

            ['name'=>'Antifungals','category_id'=>6],
        ]);
    }
}