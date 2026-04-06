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
        Schema::create('customer_ledgers', function (Blueprint $table) {
            $table->id();

            // 👤 Customer
            $table->foreignId('customer_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // 🔗 Reference (Sale / Return etc.)
            $table->unsignedBigInteger('reference_id')->nullable();

            // 🔥 Transaction Type
            $table->enum('type', ['sale', 'payment', 'return']);

            // 💰 Accounting
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->decimal('balance', 15, 2)->default(0);

            // 📅 Date
            $table->date('transaction_date');

            // 📝 Description
            $table->text('description')->nullable();

            // 👤 Created By (User)
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamps();

            // ⚡ Performance Index
            $table->index(['customer_id', 'transaction_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_ledgers');
    }
};