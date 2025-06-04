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
        Schema::create('perfumes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('brand');
            $table->text('description')->nullable();
            $table->json('notes')->nullable(); // For structured notes (top, middle, base)
            $table->string('image_url')->nullable();
            $table->string('concentration')->nullable(); // E.g., EDP, EDT, Parfum
            $table->string('gender_affinity')->nullable(); // E.g., Male, Female, Unisex
            $table->year('launch_year')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perfumes');
    }
};
