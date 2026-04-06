<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('phone')->nullable();

            $table->string('supplier_code')->nullable()->unique();
            $table->string('gst_in')->nullable();
            $table->string('drug_license')->nullable();

            $table->string('email')->nullable();

            $table->integer('credit_period')->default(0);

            $table->string('account_no')->nullable();
            $table->string('ifsc_code')->nullable();

            $table->text('address')->nullable();

            $table->foreignId('template_id')
                  ->nullable()
                  ->constrained('templates')
                  ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};