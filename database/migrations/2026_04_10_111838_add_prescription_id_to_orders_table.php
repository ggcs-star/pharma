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
    Schema::table('orders', function (Blueprint $table) {
        $table->foreignId('prescription_id')
              ->nullable()
              ->after('user_id')
              ->constrained('prescriptions')
              ->onDelete('set null');
    });
}

public function down()
{
    Schema::table('orders', function (Blueprint $table) {
        $table->dropForeign(['prescription_id']);
        $table->dropColumn('prescription_id');
    });
}
};
