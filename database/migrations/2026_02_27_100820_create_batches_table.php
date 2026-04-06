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
Schema::create('batches', function (Blueprint $table) {
    $table->id();

    $table->foreignId('item_id')
          ->constrained('items')
          ->cascadeOnDelete();

    $table->string('batch_code');
    $table->date('expiry_date')->index();

    $table->decimal('stock', 12, 2)->default(0);

    $table->decimal('mrp', 10, 2);
    $table->decimal('ptr', 10, 2);           // Purchase Trade Rate
    $table->decimal('discount', 5, 2)->default(0);
    $table->decimal('margin', 5, 2)->default(0);
    $table->decimal('markup', 5, 2)->default(0);
    $table->decimal('selling_price', 10, 2);

    $table->timestamps();

    $table->unique(['item_id', 'batch_code']);
});
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
