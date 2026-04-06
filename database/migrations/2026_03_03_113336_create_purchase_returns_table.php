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
   Schema::create('purchase_returns', function (Blueprint $table) {
    $table->id();

    $table->foreignId('purchase_id')->constrained()->cascadeOnDelete();
    $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();

    $table->string('return_number');
    $table->date('return_date');
    $table->unsignedBigInteger('entry_by');

    $table->decimal('gst', 12, 2)->default(0);
    $table->decimal('discount', 12, 2)->default(0);
    $table->decimal('net_amount', 12, 2)->default(0);
    $table->decimal('total_amount', 12, 2)->default(0);
    $table->decimal('total_gst', 12, 2)->default(0);
    $table->decimal('total_discount', 12, 2)->default(0);

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_returns');
    }
};
