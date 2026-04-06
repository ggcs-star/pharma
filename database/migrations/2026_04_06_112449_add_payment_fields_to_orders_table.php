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
        Schema::table('orders', function (Blueprint $table) {

            // 🔥 ADD NEW COLUMNS
            $table->string('payment_id')->nullable()->after('payment_mode');
            $table->string('payment_status')->default('pending')->after('payment_id');
            $table->unsignedBigInteger('address_id')->nullable()->after('payment_status');

            // 🔥 FOREIGN KEY (optional but recommended)
            $table->foreign('address_id')
                  ->references('id')
                  ->on('addresses')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            // 🔥 DROP FOREIGN KEY FIRST
            $table->dropForeign(['address_id']);

            // 🔥 DROP COLUMNS
            $table->dropColumn([
                'payment_id',
                'payment_status',
                'address_id'
            ]);
        });
    }
};