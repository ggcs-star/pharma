<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_items', function (Blueprint $table) {

            // 🔥 remove old columns
            if (Schema::hasColumn('purchase_items', 'discount')) {
                $table->dropColumn('discount');
            }

            if (Schema::hasColumn('purchase_items', 'gst')) {
                $table->dropColumn('gst');
            }

            if (Schema::hasColumn('purchase_items', 'amount')) {
                $table->dropColumn('amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('purchase_items', function (Blueprint $table) {

            // rollback support (optional but good)
            $table->decimal('discount', 15, 2)->nullable();
            $table->decimal('gst', 15, 2)->nullable();
            $table->decimal('amount', 15, 2)->nullable();
        });
    }
};