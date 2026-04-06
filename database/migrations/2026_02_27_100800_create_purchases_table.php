<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();

            $table->foreignId('supplier_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->string('invoice_number');

            $table->unsignedBigInteger('bill_id')->nullable();

            $table->foreignId('entry_by')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->decimal('gst', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('net_amount', 15, 2)->default(0);

            $table->date('due_date')->nullable();

            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('total_gst', 15, 2)->default(0);
            $table->decimal('total_discount', 15, 2)->default(0);

            $table->timestamps();

            $table->index('invoice_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};