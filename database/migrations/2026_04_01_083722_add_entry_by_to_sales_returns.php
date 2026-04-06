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
    Schema::table('sales_returns', function (Blueprint $table) {
        $table->unsignedBigInteger('entry_by')->nullable()->after('net_amount');
    });
}

public function down()
{
    Schema::table('sales_returns', function (Blueprint $table) {
        $table->dropColumn('entry_by');
    });
}
};
