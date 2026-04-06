<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::table('purchase_items', function (Blueprint $table) {

        // NEW FIELDS
        $table->decimal('mrp', 15, 2)->default(0)->after('free_quantity');

        $table->decimal('gst_percent', 5, 2)->default(0)->after('ptr');
        $table->decimal('gst_amount', 15, 2)->default(0)->after('gst_percent');

        $table->decimal('discount_percent', 5, 2)->default(0)->after('gst_amount');
        $table->decimal('discount_amount', 15, 2)->default(0)->after('discount_percent');

        $table->decimal('taxable_amount', 15, 2)->default(0)->after('discount_amount');
        $table->decimal('total_amount', 15, 2)->default(0)->after('taxable_amount');

        // EXTRA ERP FIELDS
        $table->string('barcode')->nullable();
        $table->string('rack')->nullable();
        $table->string('hsn_code')->nullable();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
