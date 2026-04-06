<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {

            // 🔹 BASIC
            $table->string('slug')->unique()->nullable()->after('name');

            // 🔹 IMAGE (S3 PATH)
            $table->string('main_image')->nullable()->after('slug');

            // 🔹 USER APP DATA
            $table->text('description')->nullable()->after('main_image');
            $table->json('product_highlights')->nullable()->after('description');
            $table->string('brand')->nullable()->after('product_highlights');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {

            $table->dropColumn([
                'slug',
                'main_image',
                'description',
                'product_highlights',
                'brand'
            ]);
        });
    }
};