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
    Schema::create('doctors', function (Blueprint $table) {
        $table->id();

        $table->string('name');
        $table->string('contact')->nullable();
        $table->string('email')->nullable();

        $table->string('registration_number')->nullable();
        $table->string('professional_credential')->nullable();

        $table->string('medical_speciality')->nullable();

        $table->string('clinic_name')->nullable();
        $table->string('clinic_city')->nullable();
        $table->string('clinic_pincode')->nullable();
        $table->text('clinic_address')->nullable();

        $table->timestamps();

        $table->index('registration_number');
        $table->index('medical_speciality');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
