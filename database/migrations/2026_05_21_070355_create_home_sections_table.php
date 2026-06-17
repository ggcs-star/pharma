<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_sections', function (Blueprint $table) {

            $table->id();

            $table->string('title');

            $table->string('slug')->unique();

            /*
            |--------------------------------------------------------------------------
            | SECTION TYPE
            |--------------------------------------------------------------------------
            |
            | manual
            | category
            | latest
            |
            */

            $table->string('type')
                ->default('manual');

            /*
            |--------------------------------------------------------------------------
            | CATEGORY LINK
            |--------------------------------------------------------------------------
            */

            $table->foreignId('category_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | STATUS
            |--------------------------------------------------------------------------
            */

            $table->boolean('is_active')
                ->default(1);

            /*
            |--------------------------------------------------------------------------
            | SORT ORDER
            |--------------------------------------------------------------------------
            */

            $table->integer('sort_order')
                ->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_sections');
    }
};  