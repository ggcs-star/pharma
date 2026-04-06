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
    Schema::create('customers', function (Blueprint $table) {
        $table->id();

        $table->string('name');
        $table->string('contact')->nullable();

        $table->string('flat_number')->nullable();

        $table->decimal('discount', 5, 2)->default(0);
$table->enum('customer_type', ['regular', 'vip'])
      ->default('regular');

        $table->text('address')->nullable();

        $table->foreignId('doctor_id')
              ->nullable()
              ->constrained('doctors')
              ->nullOnDelete();

        $table->string('preferred_language')->nullable();

        $table->date('last_buy_date')->nullable();

        $table->string('city')->nullable();
        $table->string('pincode')->nullable();

        $table->timestamps();

        $table->index('contact');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
