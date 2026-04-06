<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('categories')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('categories')->insert([
            ['id'=>1,'name'=>'Pain Relief & Fever'],
            ['id'=>2,'name'=>'Antibiotics'],
            ['id'=>3,'name'=>'Antihistamines & Allergy'],
            ['id'=>4,'name'=>'Vitamins & Supplements'],
            ['id'=>5,'name'=>'Digestive System'],
            ['id'=>6,'name'=>'Skin Care & Topical'],
        ]);
    }
}