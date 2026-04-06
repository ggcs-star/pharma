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
      Schema::create('purchase_order_items', function (Blueprint $table) {
    $table->id();

    $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
    $table->foreignId('item_id')->constrained()->cascadeOnDelete();

    $table->decimal('quantity', 15,2);
    $table->decimal('rate', 15,2);

    $table->decimal('gst_percent', 5,2)->default(0);
    $table->decimal('gst_amount', 15,2)->default(0);

    $table->decimal('discount_percent', 5,2)->default(0);
    $table->decimal('discount_amount', 15,2)->default(0);

    $table->decimal('taxable_amount', 15,2)->default(0);
    $table->decimal('total_amount', 15,2)->default(0);

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
    }
};
