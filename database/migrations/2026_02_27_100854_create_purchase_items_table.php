<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::create('purchase_items', function (Blueprint $table) {
        $table->id();

        $table->foreignId('purchase_id')
              ->constrained('purchases')
              ->cascadeOnDelete();

        $table->foreignId('item_id')
              ->constrained('items')
              ->cascadeOnDelete();

        $table->foreignId('batch_id')
              ->constrained('batches')
              ->cascadeOnDelete();

        $table->decimal('quantity', 15, 2);
        $table->decimal('free_quantity', 15, 2);

        $table->decimal('ptr', 15, 2);
        $table->decimal('discount', 15, 2);
        $table->decimal('gst', 15, 2);

        $table->decimal('amount', 15, 2);

        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_items');
    }
};
