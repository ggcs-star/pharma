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
    Schema::create('bills', function (Blueprint $table) {
    $table->id();

    $table->string('bill_code');
    $table->date('bill_date');
    $table->string('invoice_number')->nullable();

    $table->decimal('gst', 12, 2)->default(0);
    $table->decimal('discount', 12, 2)->default(0);
    $table->decimal('net_amount', 12, 2)->default(0);

    $table->string('bill_type'); // purchase / sale

    $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
    $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
