<?php

namespace App\Services\DataIngestion;

use App\Models\StagingPerfume;
use App\Models\StagingPrice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class StagingDataService
{
    /**
     * Stages the parsed data into the staging_perfumes and staging_prices tables.
     *
     * @param array $parsedItems An array of items, where each item contains perfume details
     *                           and an array of its prices.
     * @param string $sourceIdentifier The identifier for the data source.
     * @param string $importBatchId A unique ID for the current import batch.
     * @param string $sellerCode The unique code of the seller.
     * @return array An array containing counts of staged perfumes and prices.
     * @throws \Exception if database operations fail.
     */
    public function stageData(array $parsedItems, string $sourceIdentifier, string $importBatchId, string $sellerCode): array
    {
        $stagedPerfumesCount = 0;
        $stagedPricesCount = 0;
        $now = Carbon::now();

        DB::beginTransaction();
        try {
            foreach ($parsedItems as $item) {
                // Ensure essential perfume data is present
                if (empty($item['perfume_name']) || empty($item['brand'])) {
                    Log::channel('ingestion')->warning("Skipping item due to missing perfume name or brand.", [
                        'item_details' => $item,
                        'batch_id' => $importBatchId,
                        'seller_code' => $sellerCode
                    ]);
                    continue;
                }

                $stagingPerfume = StagingPerfume::create([
                    'import_batch_id' => $importBatchId,
                    'source_identifier' => $sourceIdentifier,
                    'seller_code_raw' => $sellerCode, // Added seller_code_raw
                    'raw_data_payload' => $item, // Store the whole parsed item for this perfume
                    'validation_status' => 'pending',
                    'processing_status' => 'new',
                    'imported_at' => $now,
                    'perfume_name_raw' => $item['perfume_name'] ?? null,
                    'brand_name_raw' => $item['brand'] ?? null,
                    'concentration_raw' => $item['concentration'] ?? null,
                    // size_raw is part of price data in the Excel structure
                    'gender_raw' => $item['gender_affinity'] ?? null,
                    'description_raw' => $item['description'] ?? null,
                    'notes_raw' => isset($item['notes']) ? explode(',', $item['notes']) : null, // Assuming notes are comma-separated
                    'image_url_raw' => $item['image_url'] ?? null,
                    'seller_product_url_raw' => $item['product_url'] ?? null, // From price section, but can be general
                    // 'category_raw' - not in current Excel standard
                    // 'sku_raw' - not in current Excel standard
                    // 'seller_provided_perfume_id' - not explicitly in current Excel standard, might be part of raw_data or a specific column later
                ]);
                $stagedPerfumesCount++;

                if (isset($item['prices']) && is_array($item['prices'])) {
                    foreach ($item['prices'] as $priceData) {
                        // Ensure essential price data is present
                        if (!isset($priceData['price']) || !isset($priceData['currency']) || !isset($priceData['size_ml'])) {
                             Log::channel('ingestion')->warning("Skipping price entry due to missing price, currency, or size.", [
                                'item_details' => $priceData,
                                'batch_id' => $importBatchId,
                                'seller_code' => $sellerCode,
                                'perfume_name_for_context' => $item['perfume_name'] ?? 'N/A' // Keep perfume name for context if available
                            ]);
                            continue;
                        }

                        // Add size_raw to StagingPerfume if it's more general, or keep it specific to price
                        // For now, let's update StagingPerfume's size_raw if not already set,
                        // assuming the first price's size is representative or it's a single-size import per row.
                        if (empty($stagingPerfume->size_raw) && isset($priceData['size_ml'])) {
                            $stagingPerfume->size_raw = $priceData['size_ml'] . 'ml'; // Or just the numeric value
                            // $stagingPerfume->save(); // Save if making changes after initial create
                        }


                        StagingPrice::create([
                            'import_batch_id' => $importBatchId,
                            'source_identifier' => $sourceIdentifier,
                            'seller_code_raw' => $sellerCode, // Added seller_code_raw
                            'raw_data_payload' => $priceData, // Store the specific price part
                            'validation_status' => 'pending',
                            'processing_status' => 'new',
                            'imported_at' => $now,
                            'staged_perfume_identifier' => $stagingPerfume->id,
                            'price_raw' => $priceData['price'] ?? null,
                            'currency_raw' => $priceData['currency'] ?? null,
                            // 'discount_price_raw' - not in current Excel standard
                            'availability_raw' => $priceData['stock_status'] ?? 'In Stock',
                            // 'seller_specific_price_id' - not in current Excel standard
                        ]);
                        $stagedPricesCount++;
                    }
                }
                 // Save StagingPerfume again if size_raw was updated after creation
                if ($stagingPerfume->isDirty('size_raw')) {
                    $stagingPerfume->save();
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('ingestion')->error("Failed to stage data: " . $e->getMessage(), [
                'batch_id' => $importBatchId,
                'seller_code' => $sellerCode,
                'exception_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e; // Re-throw to be caught by the command
        }

        return [
            'perfumes_staged' => $stagedPerfumesCount,
            'prices_staged' => $stagedPricesCount,
        ];
    }
}