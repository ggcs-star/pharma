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
    Schema::create('sales_returns', function (Blueprint $table) {
        $table->id();

        $table->foreignId('sale_id')
              ->constrained('sales')
              ->cascadeOnDelete();

        $table->foreignId('customer_id')
              ->constrained('customers')
              ->cascadeOnDelete();

        $table->string('return_number');

        $table->date('return_date');

        $table->decimal('net_amount', 15, 2)->default(0);

        $table->timestamps(); // Recommended even if diagram me nahi ho
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_returns');
    }
};
