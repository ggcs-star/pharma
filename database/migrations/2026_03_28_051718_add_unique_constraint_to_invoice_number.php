<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, fix any NULL invoice numbers
        DB::transaction(function () {
            $purchases = DB::table('purchases')
                ->whereNull('invoice_number')
                ->orderBy('id')
                ->get();
            
            if ($purchases->isNotEmpty()) {
                $counter = 1;
                foreach ($purchases as $purchase) {
                    DB::table('purchases')
                        ->where('id', $purchase->id)
                        ->update(['invoice_number' => (string) $counter]);
                    $counter++;
                }
            }
        });
        
        // Drop the existing index if it exists
        Schema::table('purchases', function (Blueprint $table) {
            // Drop the existing index
            $table->dropIndex(['invoice_number']);
            
            // Add unique constraint
            $table->unique('invoice_number', 'purchases_invoice_number_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            // Drop the unique constraint
            $table->dropUnique('purchases_invoice_number_unique');
            
            // Re-add the regular index
            $table->index('invoice_number');
        });
    }
};