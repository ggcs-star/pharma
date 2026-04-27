<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {

            if (!Schema::hasColumn('stock_movements', 'sale_type')) {
                $table->enum('sale_type', ['strip', 'loose'])
                    ->default('strip')
                    ->after('type');
            }

            if (!Schema::hasColumn('stock_movements', 'movement_unit')) {
                $table->string('movement_unit')
                    ->nullable()
                    ->after('sale_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {

            if (Schema::hasColumn('stock_movements', 'sale_type')) {
                $table->dropColumn('sale_type');
            }

            if (Schema::hasColumn('stock_movements', 'movement_unit')) {
                $table->dropColumn('movement_unit');
            }
        });
    }
};