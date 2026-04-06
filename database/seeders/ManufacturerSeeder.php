<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ManufacturerSeeder extends Seeder
{
    public function run(): void
    {
  DB::table('manufacturers')->insert([
    ['id' => 1, 'name' => 'Sun Pharma'],
    ['id' => 2, 'name' => 'Cipla'],
    ['id' => 3, 'name' => 'Dr Reddy'],
    ['id' => 4, 'name' => 'Alkem'],
    ['id' => 5, 'name' => 'Torrent Pharma'],

    ['id' => 18, 'name' => 'Zydus'],
    ['id' => 19, 'name' => 'Glenmark'],
    ['id' => 39, 'name' => 'Abbott'],
    ['id' => 40, 'name' => 'Mankind'],
    ['id' => 41, 'name' => 'Lupin'],
    ['id' => 42, 'name' => 'Pfizer'],
    ['id' => 43, 'name' => 'GSK'],
]);
    }
}