<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Add performance indexes to prices table for common query patterns.
     */
    public function up(): void
    {
        Schema::table('prices', function (Blueprint $table) {
            // Index for "cheapest price per perfume" queries
            $table->index(['perfume_id', 'price'], 'prices_perfume_price_idx');

            // Index for seller filtering with stock status
            $table->index(['seller_id', 'stock_status'], 'prices_seller_stock_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prices', function (Blueprint $table) {
            $table->dropIndex('prices_perfume_price_idx');
            $table->dropIndex('prices_seller_stock_idx');
        });
    }
};
