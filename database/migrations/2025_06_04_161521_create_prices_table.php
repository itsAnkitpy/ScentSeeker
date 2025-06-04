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
        Schema::create('prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perfume_id')->constrained()->onDelete('cascade');
            $table->foreignId('seller_id')->constrained()->onDelete('cascade');
            $table->decimal('price', 8, 2);
            $table->string('currency', 3)->default('INR');
            $table->string('stock_status')->nullable(); // E.g., 'in_stock', 'out_of_stock', 'pre_order'
            $table->string('product_url')->nullable();
            $table->timestamp('last_updated')->nullable();
            $table->text('offer_details')->nullable();
            $table->integer('size_ml')->nullable();
            $table->string('item_type')->nullable(); // E.g., 'full_bottle', 'decant'
            $table->timestamps();

            $table->index(['perfume_id', 'seller_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prices');
    }
};
