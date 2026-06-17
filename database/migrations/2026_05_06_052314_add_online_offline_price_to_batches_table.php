<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->decimal('offline_price', 10, 2)->nullable()->after('selling_price');
            $table->decimal('online_price', 10, 2)->nullable()->after('offline_price');
        });

        // Existing selling_price ko copy karna (important)
        DB::statement("
            UPDATE batches 
            SET offline_price = selling_price,
                online_price = selling_price
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->dropColumn(['offline_price', 'online_price']);
        });
    }
};