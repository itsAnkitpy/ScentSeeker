<?php

namespace Tests\Feature\DataIngestion;

use App\Models\Perfume;
use App\Models\Seller;
use App\Models\StagingPerfume;
use App\Models\StagingPrice;
use App\Services\DataIngestion\StagingProcessorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StagingProcessorTest extends TestCase
{
    use RefreshDatabase;

    protected StagingProcessorService $processor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->processor = new StagingProcessorService();
    }

    /**
     * Test staging perfume can be created with valid data.
     */
    public function test_staging_perfume_can_be_created(): void
    {
        $stagingPerfume = StagingPerfume::create([
            'import_batch_id' => 'test-batch-001',
            'source_identifier' => 'test_seller',
            'perfume_name_raw' => 'Test Perfume',
            'brand_name_raw' => 'Test Brand',
            'validation_status' => 'pending',
            'processing_status' => 'new',
        ]);

        $this->assertDatabaseHas('staging_perfumes', [
            'import_batch_id' => 'test-batch-001',
            'perfume_name_raw' => 'Test Perfume',
        ]);
    }

    /**
     * Test staging price can be created and linked to staging perfume.
     */
    public function test_staging_price_can_be_created(): void
    {
        $stagingPerfume = StagingPerfume::create([
            'import_batch_id' => 'test-batch-001',
            'source_identifier' => 'test_seller',
            'perfume_name_raw' => 'Test Perfume',
            'brand_name_raw' => 'Test Brand',
            'validation_status' => 'pending',
            'processing_status' => 'new',
        ]);

        $stagingPrice = StagingPrice::create([
            'import_batch_id' => 'test-batch-001',
            'source_identifier' => 'test_seller',
            'staging_perfume_id' => $stagingPerfume->id,
            'price_raw' => '99.99',
            'currency_raw' => 'USD',
            'validation_status' => 'pending',
            'processing_status' => 'new',
        ]);

        $this->assertDatabaseHas('staging_prices', [
            'import_batch_id' => 'test-batch-001',
            'price_raw' => '99.99',
        ]);
    }

    /**
     * Test processing returns an array result.
     */
    public function test_processing_returns_array(): void
    {
        $result = $this->processor->processStagedData('nonexistent-batch');

        $this->assertIsArray($result);
    }

    /**
     * Test batch deactivation returns an array.
     */
    public function test_batch_deactivation_returns_array(): void
    {
        $result = $this->processor->performBatchDeactivation('test-batch-001');

        $this->assertIsArray($result);
    }

    /**
     * Test staging perfume status changes after processing.
     */
    public function test_staging_perfume_status_updates_after_processing(): void
    {
        $batchId = 'status-test-batch';

        $stagingPerfume = StagingPerfume::create([
            'import_batch_id' => $batchId,
            'source_identifier' => 'status_test_seller',
            'perfume_name_raw' => 'Status Test Perfume',
            'brand_name_raw' => 'Status Test Brand',
            'validation_status' => 'pending',
            'processing_status' => 'new',
        ]);

        // Process the batch
        $this->processor->processStagedData($batchId);

        // Refresh from database
        $stagingPerfume->refresh();

        // Status should have changed from 'new'
        $this->assertNotEquals('new', $stagingPerfume->processing_status);
    }
}
