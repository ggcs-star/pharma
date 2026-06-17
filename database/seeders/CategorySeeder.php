<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | RESET TABLE
        |--------------------------------------------------------------------------
        */

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('categories')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        /*
        |--------------------------------------------------------------------------
        | HEALTH CONCERN CATEGORIES
        |--------------------------------------------------------------------------
        */

        DB::table('categories')->insert([

            [
                'id' => 1,
                'name' => 'Diabetes',
                'slug' => 'diabetes',
            ],

            [
                'id' => 2,
                'name' => 'Heart Care',
                'slug' => 'heart-care',
            ],

            [
                'id' => 3,
                'name' => 'Stomach Care',
                'slug' => 'stomach-care',
            ],

            [
                'id' => 4,
                'name' => 'Liver Care',
                'slug' => 'liver-care',
            ],

            [
                'id' => 5,
                'name' => 'Bone, Joint & Muscle Care',
                'slug' => 'bone-joint-muscle-care',
            ],

            [
                'id' => 6,
                'name' => 'Kidney Care',
                'slug' => 'kidney-care',
            ],

            [
                'id' => 7,
                'name' => 'Derma Care',
                'slug' => 'derma-care',
            ],

            [
                'id' => 8,
                'name' => 'Respiratory Care',
                'slug' => 'respiratory-care',
            ],

            [
                'id' => 9,
                'name' => 'Eye Care',
                'slug' => 'eye-care',
            ],
        ]);
    }
}