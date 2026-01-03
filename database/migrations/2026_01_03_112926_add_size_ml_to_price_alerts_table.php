<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('price_alerts', function (Blueprint $table) {
            // Add size_ml column - nullable means alert for ANY size
            $table->integer('size_ml')->nullable()->after('perfume_id');
            // Add index for faster lookups by size
            $table->index(['perfume_id', 'size_ml']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('price_alerts', function (Blueprint $table) {
            $table->dropIndex(['perfume_id', 'size_ml']);
            $table->dropColumn('size_ml');
        });
    }
};

