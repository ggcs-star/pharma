<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $doctors = DB::table('doctors')->pluck('id')->toArray();
        
        // Check if customers already exist
        $existingCount = DB::table('customers')->count();
        if ($existingCount >= 10) {
            $this->command->info('Customers already exist. Skipping...');
            return;
        }
        
        $customers = [];
        
        for ($i = 1; $i <= 10; $i++) {
            $customers[] = [
                'name' => $faker->name,
                'contact' => $faker->numerify('##########'),
                'flat_number' => $faker->buildingNumber,
                'discount' => $faker->randomElement([0, 5, 10, 15]),
                'customer_type' => $faker->randomElement(['regular', 'vip']),
                'address' => $faker->address,
                'doctor_id' => $faker->optional(0.5)->randomElement($doctors),
                'preferred_language' => $faker->randomElement(['English', 'Hindi', 'Tamil', 'Telugu']),
                'last_buy_date' => $faker->dateTimeBetween('-1 year', 'now'),
                'city' => $faker->randomElement(['Mumbai', 'Delhi', 'Bangalore', 'Chennai', 'Kolkata', 'Hyderabad']),
                'pincode' => $faker->numerify('######'),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('customers')->insert($customers);
        $this->command->info('Customers seeded successfully!');
    }
}