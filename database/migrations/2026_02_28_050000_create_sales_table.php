<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations
     */
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {

            $table->id();

            // Invoice
            $table->string('bill_number')->unique();
            $table->date('bill_date');

            // Relations
            $table->foreignId('customer_id')
                  ->nullable()
                  ->constrained('customers')
                  ->nullOnDelete();

            $table->foreignId('doctor_id')
                  ->nullable()
                  ->constrained('doctors')
                  ->nullOnDelete();

            $table->foreignId('entry_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // Amounts
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('gst', 12, 2)->default(0);
            $table->decimal('net_amount', 15, 2)->default(0);

            // Extra Info
            $table->enum('sales_type', ['sale','return_adjust'])->default('sale');
            $table->string('patient_name')->nullable();
            $table->string('patient_contact')->nullable();

            $table->timestamps();

            // Index (performance)
            $table->index('customer_id');
            $table->index('bill_date');
        });
    }

    /**
     * Reverse the migrations
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};