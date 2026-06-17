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
        Schema::table('items', function (Blueprint $table) {

            // =====================================================
            // MEDICINE CONTENT
            // =====================================================

            $table->string('medicine_type')->nullable()->after('molecule');

            $table->longText('introduction')->nullable()->after('description');

            $table->longText('how_to_use')->nullable()->after('introduction');

            $table->longText('safety_advise')->nullable()->after('how_to_use');

            $table->longText('if_miss')->nullable()->after('safety_advise');

            $table->longText('how_it_works')->nullable()->after('if_miss');

            $table->longText('interaction')->nullable()->after('how_it_works');

            // =====================================================
            // USES / STORAGE / SIDE EFFECTS
            // =====================================================

            $table->text('primary_use')->nullable()->after('interaction');

            $table->text('storage')->nullable()->after('primary_use');

            $table->text('common_side_effect')->nullable()->after('storage');

            // =====================================================
            // SAFETY INTERACTIONS
            // =====================================================

            $table->longText('alcohol_interaction')->nullable()->after('common_side_effect');

            $table->longText('pregnancy_interaction')->nullable()->after('alcohol_interaction');

            $table->longText('lactation_interaction')->nullable()->after('pregnancy_interaction');

            $table->longText('driving_interaction')->nullable()->after('lactation_interaction');

            $table->longText('kidney_interaction')->nullable()->after('driving_interaction');

            $table->longText('liver_interaction')->nullable()->after('kidney_interaction');

            // =====================================================
            // EXTRA INFO
            // =====================================================

            $table->json('fact_box')->nullable()->after('liver_interaction');

            $table->json('quick_tips')->nullable()->after('fact_box');

            $table->json('q_a')->nullable()->after('quick_tips');

            // =====================================================
            // MANUFACTURER / MARKETER INFO
            // =====================================================

            $table->longText('manufacturer_address')->nullable()->after('q_a');

            $table->string('country_of_origin')->nullable()->after('manufacturer_address');

            $table->longText('manufacturer_details')->nullable()->after('country_of_origin');

            $table->longText('marketer_details')->nullable()->after('manufacturer_details');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {

            $table->dropColumn([
                'medicine_type',
                'introduction',
                'how_to_use',
                'safety_advise',
                'if_miss',
                'how_it_works',
                'interaction',

                'primary_use',
                'storage',
                'common_side_effect',

                'alcohol_interaction',
                'pregnancy_interaction',
                'lactation_interaction',
                'driving_interaction',
                'kidney_interaction',
                'liver_interaction',

                'fact_box',
                'quick_tips',
                'q_a',

                'manufacturer_address',
                'country_of_origin',
                'manufacturer_details',
                'marketer_details',
            ]);

        });
    }
};