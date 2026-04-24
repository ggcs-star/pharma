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
   Schema::table('purchase_orders', function (Blueprint $table) {
    $table->boolean('stock_received')->default(false);
    $table->boolean('price_updated')->default(false);
    $table->boolean('published_for_sale')->default(false);
    $table->decimal('final_mrp', 10, 2)->nullable();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            //
        });
    }
};
