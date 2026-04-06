<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
    $table->id();

    $table->foreignId('item_id')->constrained()->cascadeOnDelete();
    $table->foreignId('batch_id')->constrained()->cascadeOnDelete();

    $table->enum('type', [
        'purchase',
        'sale',
        'sale_return',
        'purchase_return',
        'adjustment'
    ]);

    $table->enum('direction', ['in', 'out']);

    $table->decimal('quantity', 12, 2);

    $table->decimal('running_stock', 12, 2);

    $table->unsignedBigInteger('reference_id')->nullable();
    $table->string('reference_type')->nullable();

    $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

    $table->text('remarks')->nullable();

    $table->timestamps();

    $table->index(['item_id', 'batch_id']);
    $table->index('type');
});
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};