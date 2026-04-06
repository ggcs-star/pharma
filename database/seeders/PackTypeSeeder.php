<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackTypeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('pack_types')->insert([
            ['name' => 'Strip', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bottle', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Box', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tube', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Vial', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}