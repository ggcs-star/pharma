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
    Schema::create('item_packings', function (Blueprint $table) {
        $table->id();

        $table->foreignId('item_id')->constrained()->cascadeOnDelete();
        $table->foreignId('pack_id')->constrained('pack_types')->cascadeOnDelete();

        $table->integer('qty')->default(1);

        $table->string('packaging_detail')->nullable();
        $table->string('product_form')->nullable();

        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('item_packings');
}
};
