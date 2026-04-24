<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->integer('loose_stock')
                ->default(0)
                ->after('stock');
        });

        // old data auto fill
        DB::statement("
            UPDATE batches b
            JOIN items i ON b.item_id = i.id
            SET b.loose_stock = b.stock * i.conversion_factor
        ");
    }

    public function down(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->dropColumn('loose_stock');
        });
    }
};