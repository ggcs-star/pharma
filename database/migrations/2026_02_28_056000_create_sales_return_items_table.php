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
        Schema::create('sales_return_items', function (Blueprint $table) {
            $table->id();

            // 🔗 RELATIONS
            $table->foreignId('sales_return_id')
                  ->constrained('sales_returns')
                  ->cascadeOnDelete();

            $table->foreignId('sale_item_id')
                  ->constrained('sales_items')
                  ->cascadeOnDelete();

            $table->foreignId('item_id')
                  ->constrained('items')
                  ->cascadeOnDelete();

            $table->foreignId('batch_id')
                  ->constrained('batches')
                  ->cascadeOnDelete();

            // 📦 QUANTITY LOGIC
            $table->decimal('qty_return', 12, 2)->default(0);   // stock me jayega
            $table->decimal('qty_wastage', 12, 2)->default(0); // loss

            // 💰 PRICING
            $table->decimal('rate', 12, 2);     // original sale rate
            $table->decimal('amount', 15, 2);   // (return + wastage) * rate

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_return_items');
    }
};