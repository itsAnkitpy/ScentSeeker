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
        Schema::create('staging_prices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('import_batch_id')->nullable()->index();
            $table->string('source_identifier')->nullable()->index();
            $table->json('raw_data_payload')->nullable();
            $table->string('validation_status')->nullable()->default('pending');
            $table->string('processing_status')->nullable()->default('new');
            $table->json('error_details')->nullable();
            $table->unsignedBigInteger('is_duplicate_of_staged_id')->nullable();
            $table->foreign('is_duplicate_of_staged_id')->references('id')->on('staging_prices')->onDelete('set null');
            $table->unsignedBigInteger('matched_production_perfume_id')->nullable();
            // Assuming 'perfumes' table exists
            // $table->foreign('matched_production_perfume_id')->references('id')->on('perfumes')->onDelete('set null');
            $table->unsignedBigInteger('matched_production_price_id')->nullable();
            // Assuming 'prices' table exists
            // $table->foreign('matched_production_price_id')->references('id')->on('prices')->onDelete('set null');
            $table->float('confidence_score')->nullable();
            $table->timestamp('imported_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->string('staged_perfume_identifier')->nullable()->index();
            $table->decimal('price_raw', 10, 2)->nullable();
            $table->string('currency_raw')->nullable();
            $table->decimal('discount_price_raw', 10, 2)->nullable();
            $table->string('availability_raw')->nullable();
            $table->string('seller_specific_price_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staging_prices');
    }
};
