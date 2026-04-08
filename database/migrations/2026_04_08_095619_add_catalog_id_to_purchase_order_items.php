<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ✅ column add if not exists
        if (!Schema::hasColumn('purchase_order_items', 'supplier_item_catalog_id')) {

            Schema::table('purchase_order_items', function (Blueprint $table) {
                $table->unsignedBigInteger('supplier_item_catalog_id')
                      ->nullable()
                      ->after('item_id');
            });
        }

        // ✅ optional: existing data fix (if needed)
        DB::table('purchase_order_items')
            ->whereNull('supplier_item_catalog_id')
            ->update(['supplier_item_catalog_id' => 1]); // only if valid exists

        // ✅ foreign key add
        Schema::table('purchase_order_items', function (Blueprint $table) {
            try {
                $table->foreign('supplier_item_catalog_id')
                      ->references('id')
                      ->on('supplier_item_catalogs')
                      ->cascadeOnDelete();
            } catch (\Exception $e) {}
        });
    }

    public function down(): void
    {
        Schema::table('purchase_order_items', function (Blueprint $table) {

            try {
                $table->dropForeign(['supplier_item_catalog_id']);
            } catch (\Exception $e) {}

            if (Schema::hasColumn('purchase_order_items', 'supplier_item_catalog_id')) {
                $table->dropColumn('supplier_item_catalog_id');
            }
        });
    }
};