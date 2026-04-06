<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('purchase_items', function (Blueprint $table) {
            // Add missing discount_amount column
            if (!Schema::hasColumn('purchase_items', 'discount_amount')) {
                $table->decimal('discount_amount', 15, 2)->default(0)->after('discount_percent');
            }
            
            // Add missing gst column
            if (!Schema::hasColumn('purchase_items', 'gst')) {
                $table->decimal('gst', 15, 2)->default(0)->after('discount');
            }
            
            // Add discount column if missing
            if (!Schema::hasColumn('purchase_items', 'discount')) {
                $table->decimal('discount', 15, 2)->default(0)->after('ptr');
            }
        });
    }

    public function down()
    {
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->dropColumn(['discount_amount', 'gst', 'discount']);
        });
    }
};