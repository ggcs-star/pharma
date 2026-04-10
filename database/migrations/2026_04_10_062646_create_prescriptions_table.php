<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('address_id')->nullable();

            // ✅ S3 full URL store
            $table->string('file_path');

            // optional notes
            $table->text('notes')->nullable();

            // ✅ pharma flow status
            $table->enum('status', ['pending', 'approved', 'rejected'])
                  ->default('pending');

            $table->timestamps();

            // optional relations
            $table->index('user_id');
            $table->index('address_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};