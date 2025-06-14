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
        Schema::create('staging_perfumes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('import_batch_id')->nullable()->index();
            $table->string('source_identifier')->nullable()->index();
            $table->json('raw_data_payload')->nullable();
            $table->string('validation_status')->nullable()->default('pending')->index();
            $table->string('processing_status')->nullable()->default('new')->index();
            $table->json('error_details')->nullable();
            $table->unsignedBigInteger('is_duplicate_of_staged_id')->nullable();
            $table->foreign('is_duplicate_of_staged_id')->references('id')->on('staging_perfumes')->onDelete('set null');
            $table->unsignedBigInteger('matched_production_perfume_id')->nullable();
            // Assuming 'perfumes' table exists and has 'id' as primary key
            // Add foreign key constraint if 'perfumes' table migration is guaranteed to run before this.
            // For now, we'll just add the column. A separate migration can add constraints if needed.
            // $table->foreign('matched_production_perfume_id')->references('id')->on('perfumes')->onDelete('set null');
            $table->float('confidence_score')->nullable();
            $table->timestamp('imported_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->string('seller_provided_perfume_id')->nullable()->index();
            $table->string('perfume_name_raw')->nullable();
            $table->string('brand_name_raw')->nullable();
            $table->string('concentration_raw')->nullable();
            $table->string('size_raw')->nullable();
            $table->string('gender_raw')->nullable();
            $table->text('description_raw')->nullable();
            $table->json('notes_raw')->nullable();
            $table->string('image_url_raw')->nullable();
            $table->string('seller_product_url_raw')->nullable();
            $table->string('category_raw')->nullable();
            $table->string('sku_raw')->nullable();
            $table->timestamps();

            $table->index(['source_identifier', 'seller_provided_perfume_id'], 'idx_source_seller_perfume_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staging_perfumes');
    }
};
