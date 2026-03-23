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
        Schema::table('price_alerts', function (Blueprint $table) {
            // MySQL uses the unique index for the user_id FK — drop FK first
            $table->dropForeign(['user_id']);
            $table->dropUnique(['user_id', 'perfume_id']);
        });

        Schema::table('price_alerts', function (Blueprint $table) {
            // Re-add FK and new composite unique including size_ml
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->unique(['user_id', 'perfume_id', 'size_ml'], 'price_alerts_user_perfume_size_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('price_alerts', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropUnique('price_alerts_user_perfume_size_unique');
        });

        Schema::table('price_alerts', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->unique(['user_id', 'perfume_id']);
        });
    }
};
