<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierItemCatalogsTable extends Migration
{
    public function up()
    {
        Schema::create('supplier_item_catalogs', function (Blueprint $table) {
            $table->id();

            // 🔗 Relations
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();

            // 💰 Pricing
            $table->decimal('purchase_price', 10, 2);
            $table->decimal('base_price', 10, 2);
            $table->decimal('retailer_price', 10, 2);
            $table->decimal('retailer_mrp', 10, 2);

            // 🔥 Flexibility
            $table->boolean('is_price_editable')->default(true);

            // 📦 Quick stock (optional cache)
            $table->integer('current_stock')->default(0);

            // 📊 Margins
            $table->decimal('supplier_margin', 5, 2)->nullable();
            $table->decimal('retailer_margin', 5, 2)->nullable();

            // 💊 Pharma Fields
            $table->date('expiry_date')->nullable();
            $table->string('batch_no')->nullable();
            $table->decimal('gst_percent', 5, 2)->nullable();
            $table->integer('free_qty')->default(0);

            // ⚙️ Status
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // 🔥 Performance
            $table->index(['item_id', 'supplier_id']);
            $table->unique(['supplier_id', 'item_id', 'batch_no']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('supplier_item_catalogs');
    }
}