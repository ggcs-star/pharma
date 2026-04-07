<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierItemsTable extends Migration
{
    public function up()
    {
        Schema::create('supplier_items', function (Blueprint $table) {
            $table->id();

            // 🔹 Supplier Relation (optional but recommended)
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();

            // 🔹 Basic Info
            $table->string('name');
            $table->string('slug')->unique()->nullable();

            // 🔹 Image & Description
            $table->string('main_image')->nullable();
            $table->text('description')->nullable();

            // 🔹 Product Info
            $table->string('brand')->nullable();

            // 🔥 Selling Price
            $table->decimal('selling_price', 10, 2);

            // 🔹 Extra
            $table->json('product_highlights')->nullable();

            // 🔹 Status
            $table->boolean('inactive')->default(false);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('supplier_items');
    }
}
