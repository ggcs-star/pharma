<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | NEW COLUMNS
            |--------------------------------------------------------------------------
            */

            $table->string('slug')
                ->nullable()
                ->after('name');
        });

        /*
        |--------------------------------------------------------------------------
        | AUTO GENERATE SLUGS
        |--------------------------------------------------------------------------
        */

        $categories = DB::table('categories')->get();

        foreach ($categories as $category) {

            DB::table('categories')
                ->where('id', $category->id)
                ->update([

                    'slug' => Str::slug($category->name)
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {

            $table->dropColumn('slug');
        });
    }
};