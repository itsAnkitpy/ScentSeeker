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
        Schema::table('perfumes', function (Blueprint $table) {
            $table->index('name');
            $table->index('brand');
            $table->index(['name', 'brand']); // Composite index for de-duplication queries
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perfumes', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->dropIndex(['brand']);
            $table->dropIndex(['name', 'brand']);
        });
    }
};
