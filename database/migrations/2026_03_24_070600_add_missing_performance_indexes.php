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
        Schema::table('price_histories', function (Blueprint $table) {
            $table->index('date');
        });

        Schema::table('wishlist_items', function (Blueprint $table) {
            $table->index('perfume_id');
        });

        Schema::table('sellers', function (Blueprint $table) {
            $table->index('name');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('price_histories', function (Blueprint $table) {
            $table->dropIndex(['date']);
        });

        Schema::table('wishlist_items', function (Blueprint $table) {
            $table->dropIndex(['perfume_id']);
        });

        Schema::table('sellers', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->dropIndex(['type']);
        });
    }
};
