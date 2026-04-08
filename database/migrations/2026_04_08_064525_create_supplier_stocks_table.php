<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierStocksTable extends Migration
{
    public function up()
    {
        Schema::create('supplier_stocks', function (Blueprint $table) {
            $table->id();

            // 🔗 Relation
            $table->foreignId('supplier_item_catalog_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // 📦 Stock Movement
            $table->integer('qty'); // +ve = add, -ve = reduce

            // 📊 Type of entry
            $table->enum('type', [
                'purchase',
                'sale',
                'return',
                'adjustment'
            ]);

            // 🔗 Reference (optional)
            $table->unsignedBigInteger('reference_id')->nullable();

            // 📝 Notes
            $table->text('note')->nullable();

            $table->timestamps();

            // 🔥 Index
            $table->index('supplier_item_catalog_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('supplier_stocks');
    }
}
