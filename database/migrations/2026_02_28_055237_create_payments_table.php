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
    Schema::create('payments', function (Blueprint $table) {
        $table->id();

        $table->string('order_number');

        $table->date('bill_date');

        $table->foreignId('bill_id')
              ->constrained('sales')
              ->cascadeOnDelete();

        $table->decimal('amount', 15, 2);

        $table->decimal('net_amount', 15, 2);

        $table->string('utr_number')->nullable();

        $table->string('status')->nullable();

        $table->string('payment_mode')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
