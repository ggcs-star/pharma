<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'Apollo Pharma Distributors',
                'phone' => '9876543210',
                'supplier_code' => 'APD001',
                'gst_in' => '27AAACA1234A1Z5',
                'drug_license' => 'DL/APO/2023/001',
                'email' => 'orders@apollopharma.com',
                'credit_period' => 30,
                'account_no' => '123456789012',
                'ifsc_code' => 'HDFC0001234',
                'address' => '123, Andheri East, Mumbai - 400093',
                'template_id' => null,
            ],
            [
                'name' => 'MediTech Solutions',
                'phone' => '9876543211',
                'supplier_code' => 'MTS002',
                'gst_in' => '27AAACB1234B2Z6',
                'drug_license' => 'DL/MED/2023/002',
                'email' => 'sales@mediatech.com',
                'credit_period' => 45,
                'account_no' => '123456789013',
                'ifsc_code' => 'ICIC0005678',
                'address' => '45, Koramangala, Bangalore - 560034',
                'template_id' => null,
            ],
            [
                'name' => 'HealthCare Wholesalers',
                'phone' => '9876543212',
                'supplier_code' => 'HCW003',
                'gst_in' => '27AAACD1234C3Z7',
                'drug_license' => 'DL/HCW/2023/003',
                'email' => 'info@healthcarewholesale.com',
                'credit_period' => 30,
                'account_no' => '123456789014',
                'ifsc_code' => 'SBIN0009012',
                'address' => '78, Connaught Place, New Delhi - 110001',
                'template_id' => null,
            ],
            [
                'name' => 'Global Pharma Trading',
                'phone' => '9876543213',
                'supplier_code' => 'GPT004',
                'gst_in' => '27AAACE1234D4Z8',
                'drug_license' => 'DL/GPT/2023/004',
                'email' => 'trading@globalpharma.com',
                'credit_period' => 60,
                'account_no' => '123456789015',
                'ifsc_code' => 'AXIS0003456',
                'address' => '234, Anna Nagar, Chennai - 600040',
                'template_id' => null,
            ],
            [
                'name' => 'MediCare Distributors',
                'phone' => '9876543214',
                'supplier_code' => 'MCD005',
                'gst_in' => '27AAACF1234E5Z9',
                'drug_license' => 'DL/MCD/2023/005',
                'email' => 'orders@medicaredist.com',
                'credit_period' => 45,
                'account_no' => '123456789016',
                'ifsc_code' => 'YESB0007890',
                'address' => '567, Park Street, Kolkata - 700016',
                'template_id' => null,
            ],
            [
                'name' => 'Reliable Pharma Suppliers',
                'phone' => '9876543215',
                'supplier_code' => 'RPS006',
                'gst_in' => '27AAACG1234F6Z0',
                'drug_license' => 'DL/RPS/2023/006',
                'email' => 'contact@reliablepharma.com',
                'credit_period' => 30,
                'account_no' => '123456789017',
                'ifsc_code' => 'KARB0001234',
                'address' => '89, Satellite, Ahmedabad - 380015',
                'template_id' => null,
            ],
            [
                'name' => 'LifeLine Medicals',
                'phone' => '9876543216',
                'supplier_code' => 'LML007',
                'gst_in' => '27AAACH1234G7Z1',
                'drug_license' => 'DL/LML/2023/007',
                'email' => 'sales@lifelinemedicals.com',
                'credit_period' => 45,
                'account_no' => '123456789018',
                'ifsc_code' => 'IDIB0004567',
                'address' => '123, FC Road, Pune - 411004',
                'template_id' => null,
            ],
            [
                'name' => 'Vista Pharma Solutions',
                'phone' => '9876543217',
                'supplier_code' => 'VPS008',
                'gst_in' => '27AAACI1234H8Z2',
                'drug_license' => 'DL/VPS/2023/008',
                'email' => 'info@vistapharma.com',
                'credit_period' => 60,
                'account_no' => '123456789019',
                'ifsc_code' => 'UTIB0002345',
                'address' => '456, MG Road, Jaipur - 302001',
                'template_id' => null,
            ],
            [
                'name' => 'Prime Health Distributors',
                'phone' => '9876543218',
                'supplier_code' => 'PHD009',
                'gst_in' => '27AAACJ1234I9Z3',
                'drug_license' => 'DL/PHD/2023/009',
                'email' => 'orders@primehealth.com',
                'credit_period' => 30,
                'account_no' => '123456789020',
                'ifsc_code' => 'CANB0006789',
                'address' => '789, Civil Lines, Lucknow - 226001',
                'template_id' => null,
            ],
            [
                'name' => 'SafeMed Pharmaceuticals',
                'phone' => '9876543219',
                'supplier_code' => 'SMP010',
                'gst_in' => '27AAACK1234J0Z4',
                'drug_license' => 'DL/SMP/2023/010',
                'email' => 'contact@safemed.com',
                'credit_period' => 45,
                'account_no' => '123456789021',
                'ifsc_code' => 'PUNB0001234',
                'address' => '321, Race Course Road, Coimbatore - 641018',
                'template_id' => null,
            ],
        ];

        foreach ($suppliers as $supplier) {
            $exists = DB::table('suppliers')->where('supplier_code', $supplier['supplier_code'])->exists();
            if (!$exists) {
                DB::table('suppliers')->insert($supplier);
            }
        }
        
        $this->command->info('Suppliers seeded successfully!');
    }
}