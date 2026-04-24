<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->enum('sale_type', ['strip', 'loose'])
                ->default('strip')
                ->after('type');

            $table->string('movement_unit')
                ->nullable()
                ->after('sale_type');
        });
    }

    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropColumn([
                'sale_type',
                'movement_unit'
            ]);
        });
    }
};