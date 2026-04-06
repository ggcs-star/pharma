<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        $doctors = [
            ['name' => 'Dr. Rajesh Kumar - Cardiologist'],
            ['name' => 'Dr. Priya Sharma - General Physician'],
            ['name' => 'Dr. Anil Mehta - Pediatrician'],
            ['name' => 'Dr. Sunita Reddy - Gynecologist'],
            ['name' => 'Dr. Vikram Singh - Orthopedic'],
            ['name' => 'Dr. Neha Gupta - Dermatologist'],
            ['name' => 'Dr. Sanjay Verma - Neurologist'],
            ['name' => 'Dr. Anita Desai - ENT Specialist'],
            ['name' => 'Dr. Manoj Joshi - Ophthalmologist'],
            ['name' => 'Dr. Kavita Nair - Psychiatrist'],
        ];

        foreach ($doctors as $doctor) {
            $exists = DB::table('doctors')->where('name', $doctor['name'])->exists();
            if (!$exists) {
                DB::table('doctors')->insert($doctor);
            }
        }
        
        $this->command->info('Doctors seeded successfully!');
    }
}