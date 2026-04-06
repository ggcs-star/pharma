<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_images', function (Blueprint $table) {
            $table->id();

            // 🔗 ITEM RELATION
            $table->foreignId('item_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // 🔹 IMAGE PATH (S3)
            $table->string('image');

            // 🔹 ORDERING (optional but important)
            $table->integer('sort_order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_images');
    }
};