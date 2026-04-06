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
Schema::table('purchase_return_items', function (Blueprint $table) {

    if (!Schema::hasColumn('purchase_return_items', 'purchase_item_id')) {
        $table->foreignId('purchase_item_id')
              ->after('purchase_return_id')
              ->constrained()
              ->cascadeOnDelete();
    }

    if (!Schema::hasColumn('purchase_return_items', 'rate')) {
        $table->decimal('rate', 12, 2)
              ->default(0)
              ->after('quantity');
    }

});  }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
