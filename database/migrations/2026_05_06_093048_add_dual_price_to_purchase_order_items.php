<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('purchase_order_items', function (Blueprint $table) {

            // 🔥 Add after final_mrp for clean structure
            $table->decimal('offline_price', 10, 2)
                  ->nullable()
                  ->after('final_mrp');

            $table->decimal('online_price', 10, 2)
                  ->nullable()
                  ->after('offline_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_order_items', function (Blueprint $table) {

            $table->dropColumn([
                'offline_price',
                'online_price'
            ]);
        });
    }
};