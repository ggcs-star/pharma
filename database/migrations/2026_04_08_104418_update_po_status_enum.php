<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 🔥 STEP 1: TEMP CHANGE (allow all values)
        DB::statement("
            ALTER TABLE purchase_orders 
            MODIFY status VARCHAR(50)
        ");

        // 🔥 STEP 2: FIX OLD DATA
        DB::statement("
            UPDATE purchase_orders 
            SET status = 'pending' 
            WHERE status = 'draft'
        ");

        DB::statement("
            UPDATE purchase_orders 
            SET status = 'confirmed' 
            WHERE status = 'approved'
        ");

        DB::statement("
            UPDATE purchase_orders 
            SET status = 'delivered' 
            WHERE status = 'completed'
        ");

        // 🔥 STEP 3: FINAL ENUM SET
        DB::statement("
            ALTER TABLE purchase_orders 
            MODIFY status ENUM(
                'pending',
                'confirmed',
                'processing',
                'dispatched',
                'delivered',
                'cancelled',
                'rejected'
            ) DEFAULT 'pending'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE purchase_orders 
            MODIFY status ENUM(
                'draft',
                'approved',
                'completed',
                'cancelled'
            ) DEFAULT 'draft'
        ");
    }
};
