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
    Schema::create('items', function (Blueprint $table) {
        $table->id();

        $table->string('name');
        $table->decimal('gst_percent', 5, 2);

        $table->foreignId('pack_id')->constrained('pack_types')->cascadeOnDelete();
        $table->integer('number_of_units');

        $table->foreignId('unit_id')->constrained()->cascadeOnDelete();

        $table->foreignId('category_id')->constrained()->cascadeOnDelete();
        $table->foreignId('sub_category_id')->constrained()->cascadeOnDelete();

        $table->string('rack')->nullable();
        $table->string('hsn_code')->nullable();
        $table->string('sch_type')->nullable();

        $table->foreignId('manufacturer_id')->constrained()->cascadeOnDelete();

        $table->string('molecule')->nullable();
        $table->string('barcode')->nullable();

        $table->integer('min_threshold');
        $table->integer('max_threshold');

        $table->boolean('need_prescription');
        $table->boolean('not_for_online_sale');
        $table->boolean('inactive');
        $table->boolean('block_purchase');
        $table->boolean('service_item');
        $table->boolean('sell_loose');
        $table->boolean('override_loose');

        $table->decimal('conversion_factor', 10, 2);
        $table->decimal('unit_ratio', 10, 2);

        $table->integer('max_self_life');
        $table->decimal('max_discount', 5, 2);

        $table->text('notes')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
