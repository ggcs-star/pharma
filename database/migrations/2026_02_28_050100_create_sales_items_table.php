<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations
     */
    public function up(): void
    {
        Schema::create('sales_items', function (Blueprint $table) {

            $table->id();

            // Parent Sale
            $table->foreignId('sale_id')
                  ->constrained('sales')
                  ->cascadeOnDelete();

            // Item
            $table->foreignId('item_id')
                  ->constrained('items')
                  ->cascadeOnDelete();

            // Batch (VERY IMPORTANT FOR PHARMA)
            $table->foreignId('batch_id')
                  ->constrained('batches')
                  ->cascadeOnDelete();

            // Quantity (supports loose selling)
            $table->decimal('quantity', 12, 2);

            // Pricing
            $table->decimal('selling_price', 12, 2);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('gst', 12, 2)->default(0);
            $table->decimal('gst_amount', 12, 2)->default(0);

            // Final amount
            $table->decimal('amount', 15, 2);

            $table->timestamps();

            // Index for performance
            $table->index('sale_id');
            $table->index('item_id');
            $table->index('batch_id');
        });
    }

    /**
     * Reverse the migrations
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_items');
    }
};