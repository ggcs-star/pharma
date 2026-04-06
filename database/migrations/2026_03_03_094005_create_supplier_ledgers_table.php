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
 Schema::create('supplier_ledgers', function (Blueprint $table) {
    $table->id();

    $table->foreignId('supplier_id')
          ->constrained()
          ->cascadeOnDelete();

    $table->decimal('debit', 15, 2)->default(0);
    $table->decimal('credit', 15, 2)->default(0);

    $table->decimal('balance_after', 15, 2);

    $table->date('transaction_date');

    $table->unsignedBigInteger('reference_id')->nullable();
    $table->string('reference_type')->nullable();

    $table->string('payment_mode')->nullable();
    $table->text('remarks')->nullable();

    $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

    $table->timestamps();

    $table->index(['supplier_id']);
    $table->index(['reference_type', 'reference_id']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_ledgers');
    }
};
