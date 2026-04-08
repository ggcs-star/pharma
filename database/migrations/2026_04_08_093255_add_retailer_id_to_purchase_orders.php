<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ✅ STEP 1: ADD COLUMN IF NOT EXISTS
        if (!Schema::hasColumn('purchase_orders', 'retailer_id')) {

            Schema::table('purchase_orders', function (Blueprint $table) {
                $table->unsignedBigInteger('retailer_id')
                      ->nullable()
                      ->after('supplier_id');
            });
        }

        // ✅ STEP 2: GET VALID USER (RETAILER)
        $user = DB::table('users')->select('id')->first();

        if ($user) {
            // ✅ FIX OLD DATA
            DB::table('purchase_orders')
                ->whereNull('retailer_id')
                ->orWhere('retailer_id', 0)
                ->update(['retailer_id' => $user->id]);
        }

        // ✅ STEP 3: ADD FOREIGN KEY (RELATION)
        Schema::table('purchase_orders', function (Blueprint $table) {

            try {
                $table->foreign('retailer_id')
                      ->references('id')
                      ->on('users')
                      ->cascadeOnDelete();
            } catch (\Exception $e) {
                // already exists → ignore
            }

        });
    }

    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {

            try {
                $table->dropForeign(['retailer_id']);
            } catch (\Exception $e) {}

            if (Schema::hasColumn('purchase_orders', 'retailer_id')) {
                $table->dropColumn('retailer_id');
            }
        });
    }
};