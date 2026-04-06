<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('units')->insert([
            ['name' => 'Tablet', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bottle', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Piece', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Strip', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}