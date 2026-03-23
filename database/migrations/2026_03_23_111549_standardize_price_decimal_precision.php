<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Widen prices.price and price_histories.price from decimal(8,2)
     * to decimal(10,2) so they match staging_prices and price_alerts.
     */
    public function up(): void
    {
        Schema::table('prices', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->change();
        });

        Schema::table('price_histories', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prices', function (Blueprint $table) {
            $table->decimal('price', 8, 2)->change();
        });

        Schema::table('price_histories', function (Blueprint $table) {
            $table->decimal('price', 8, 2)->change();
        });
    }
};
