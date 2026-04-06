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
    Schema::table('purchase_return_items', function (Blueprint $table) {
        $table->unsignedBigInteger('purchase_item_id')->after('purchase_return_id');

        $table->foreign('purchase_item_id')
              ->references('id')
              ->on('purchase_items')
              ->onDelete('cascade');
    });
}

public function down()
{
    Schema::table('purchase_return_items', function (Blueprint $table) {
        $table->dropForeign(['purchase_item_id']);
        $table->dropColumn('purchase_item_id');
    });
}
};
