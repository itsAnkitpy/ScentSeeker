<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Add unique constraint on (name, brand) to prevent duplicate perfumes.
     */
    public function up(): void
    {
        Schema::table('perfumes', function (Blueprint $table) {
            $table->unique(['name', 'brand'], 'perfumes_name_brand_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perfumes', function (Blueprint $table) {
            $table->dropUnique('perfumes_name_brand_unique');
        });
    }
};
