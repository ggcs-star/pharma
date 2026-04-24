<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_items', function (Blueprint $table) {

            // strip ya loose
            $table->string('sale_type')
                ->default('strip')
                ->after('quantity');

            // actual tablet qty
            $table->integer('unit_qty')
                ->default(0)
                ->after('sale_type');

        });
    }

    public function down(): void
    {
        Schema::table('sales_items', function (Blueprint $table) {

            $table->dropColumn([
                'sale_type',
                'unit_qty'
            ]);

        });
    }
};