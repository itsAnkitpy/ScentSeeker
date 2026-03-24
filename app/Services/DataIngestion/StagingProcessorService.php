<?php

namespace App\Services\DataIngestion;

use App\Models\Perfume;
use App\Models\Price;
use App\Models\Seller; // Added Seller model
use App\Models\StagingPerfume;
use App\Models\StagingPrice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class StagingProcessorService
{
    /**
     * Processes records from staging tables, validates them,
     * and imports them into production tables (perfumes, prices).
     *
     * @param string|null $importBatchId Optionally process a specific batch.
     * @param int $limit The maximum number of staging perfumes to process in one run.
     * @return array Counts of processed, imported, updated, and failed records.
     */
    public function processStagedData(?string $importBatchId = null, int $limit = 100): array
    {
        $processedCount = 0;
        $perfumesCreated = 0;
        $perfumesUpdated = 0;
        $pricesCreated = 0;
        $pricesUpdated = 0;
        $failedCount = 0;
        $now = Carbon::now();

        $query = StagingPerfume::where('processing_status', 'new')
                    ->where('validation_status', 'pending'); // Or a 'validated' status if you add a separate validation step

        if ($importBatchId) {
            $query->where('import_batch_id', $importBatchId);
        }

        $stagingPerfumesToProcess = $query->with('stagingPrices')->limit($limit)->get();

        if ($stagingPerfumesToProcess->isEmpty()) {
            return [
                'message' => 'No new staging perfumes to process.',
                'processed_count' => $processedCount,
                'perfumes_created' => $perfumesCreated,
                'perfumes_updated' => $perfumesUpdated,
                'prices_created' => $pricesCreated,
                'prices_updated' => $pricesUpdated,
                'failed_count' => $failedCount,
            ];
        }

        foreach ($stagingPerfumesToProcess as $stagedPerfume) {
            DB::beginTransaction();
            try {
                // 0. Retrieve seller_code_raw and find Seller
                $sellerCodeRaw = $stagedPerfume->seller_code_raw;
                if (empty($sellerCodeRaw)) {
                    $stagedPerfume->processing_status = 'failed';
                    $stagedPerfume->validation_status = 'failed';
                    $stagedPerfume->error_details = ['error' => 'Missing seller_code_raw.'];
                    $stagedPerfume->processed_at = $now;
                    $stagedPerfume->save();
                    // Mark associated prices as failed too
                    foreach ($stagedPerfume->stagingPrices as $sp) {
                        $sp->processing_status = 'failed';
                        $sp->validation_status = 'failed';
                        $sp->error_details = ['error' => 'Parent perfume failed due to missing seller_code_raw.'];
                        $sp->processed_at = $now;
                        $sp->save();
                    }
                    $failedCount++;
                    DB::commit();
                    continue;
                }

                $seller = Seller::where('code', $sellerCodeRaw)->first();

                if (!$seller) {
                    $errorMessage = "Seller with code '{$sellerCodeRaw}' not found.";
                    Log::channel('ingestion')->error("Seller not found.", ['seller_code_raw' => $sellerCodeRaw, 'staged_perfume_id' => $stagedPerfume->id, 'batch_id' => $stagedPerfume->import_batch_id, 'error_message' => $errorMessage]);
                    $stagedPerfume->processing_status = 'failed';
                    $stagedPerfume->validation_status = 'failed';
                    $stagedPerfume->error_details = ['error' => $errorMessage];
                    $stagedPerfume->processed_at = $now;
                    $stagedPerfume->save();
                    // Mark associated prices as failed too
                    foreach ($stagedPerfume->stagingPrices as $sp) {
                        $sp->processing_status = 'failed';
                        $sp->validation_status = 'failed';
                        $sp->error_details = ['error' => $errorMessage];
                        $sp->processed_at = $now;
                        $sp->save();
                    }
                    $failedCount++;
                    DB::commit();
                    continue;
                }
                $sellerId = $seller->id;

                // 1. Basic Validation (can be expanded)
                if (empty($stagedPerfume->perfume_name_raw) || empty($stagedPerfume->brand_name_raw)) {
                    $stagedPerfume->processing_status = 'failed';
                    $stagedPerfume->validation_status = 'failed';
                    $stagedPerfume->error_details = ['error' => 'Missing perfume name or brand.'];
                    $stagedPerfume->processed_at = $now;
                    $stagedPerfume->save();
                    Log::channel('ingestion')->warning('Failed to process staged perfume due to missing name or brand.', ['staged_perfume_id' => $stagedPerfume->id, 'batch_id' => $stagedPerfume->import_batch_id, 'seller_code_raw' => $sellerCodeRaw, 'details' => $stagedPerfume->error_details]);
                    // Mark associated prices as failed too
                    foreach ($stagedPerfume->stagingPrices as $sp) {
                        $sp->processing_status = 'failed';
                        $sp->validation_status = 'failed';
                        $sp->error_details = ['error' => 'Parent perfume failed due to missing name/brand.'];
                        $sp->processed_at = $now;
                        $sp->save();
                    }
                    $failedCount++;
                    DB::commit(); // Commit status update for this record
                    continue;
                }

                // 2. De-duplication & Transformation for Perfume
                // Find existing perfume by name and brand (case-insensitive matching)
                // Using a query that can utilize indexes better than LOWER()
                $normalizedName = trim($stagedPerfume->perfume_name_raw);
                $normalizedBrand = trim($stagedPerfume->brand_name_raw);
                
                $perfume = Perfume::where(function ($query) use ($normalizedName, $normalizedBrand) {
                    $query->whereRaw('LOWER(TRIM(name)) = LOWER(?)', [$normalizedName])
                          ->whereRaw('LOWER(TRIM(brand)) = LOWER(?)', [$normalizedBrand]);
                })->first();

                $perfumeData = [
                    'name' => $stagedPerfume->perfume_name_raw,
                    'brand' => $stagedPerfume->brand_name_raw,
                    'description' => $stagedPerfume->description_raw,
                    'notes' => $stagedPerfume->notes_raw, // Pass the array directly
                    'image_url' => $stagedPerfume->image_url_raw,
                    'concentration' => $stagedPerfume->concentration_raw,
                    'gender_affinity' => $stagedPerfume->gender_raw,
                    // 'launch_year' - map if available in staging_perfumes
                ];

                if ($perfume) {
                    // Update existing perfume (be selective about what to update)
                    // For now, let's assume we might update description, notes, image if provided
                    $perfume->update(array_filter($perfumeData, fn($value) => $value !== null));
                    Log::channel('ingestion')->info('Production perfume updated.', ['perfume_id' => $perfume->id, 'name' => $perfume->name, 'staged_perfume_id' => $stagedPerfume->id, 'batch_id' => $stagedPerfume->import_batch_id]);
                    $perfumesUpdated++;
                } else {
                    $perfume = Perfume::create($perfumeData);
                    Log::channel('ingestion')->info('Production perfume created.', ['perfume_id' => $perfume->id, 'name' => $perfume->name, 'staged_perfume_id' => $stagedPerfume->id, 'batch_id' => $stagedPerfume->import_batch_id]);
                    $perfumesCreated++;
                }
                $stagedPerfume->matched_production_perfume_id = $perfume->id;

                // 3. Process Staging Prices for this Perfume
                foreach ($stagedPerfume->stagingPrices as $stagedPrice) {
                    if (empty($stagedPrice->price_raw) || empty($stagedPrice->currency_raw) || empty($stagedPerfume->size_raw) /* size from perfume for now */) {
                        $stagedPrice->processing_status = 'failed';
                        $stagedPrice->validation_status = 'failed';
                        $stagedPrice->error_details = ['error' => 'Missing price, currency, or size for price entry.'];
                        $stagedPrice->processed_at = $now;
                        $stagedPrice->save();
                        Log::channel('ingestion')->warning('Failed to process staged price due to missing data.', ['staged_perfume_id' => $stagedPerfume->id, 'staged_price_id' => $stagedPrice->id, 'batch_id' => $stagedPerfume->import_batch_id, 'seller_code_raw' => $sellerCodeRaw, 'details' => $stagedPrice->error_details]);
                        // Note: This doesn't roll back the perfume creation/update, but marks the price as failed.
                        // Consider if a price failure should also mark the perfume staging as partially failed.
                        continue; 
                    }
                    
                    // De-duplication for Price (e.g., by perfume_id, seller_id (once available), and size)
                    // This is simplified; real de-duplication needs seller context.
                    // For now, we'll assume a new price entry if not perfectly matched.
                    // A proper Price model would have a seller_id. We'll need to add that.
                    // Let's assume for now we create new prices, or update if a similar one exists (simplistic match)

                    $priceData = [
                        'perfume_id' => $perfume->id,
                        'seller_id' => $sellerId, // Crucial: Set the seller_id
                        'price' => $stagedPrice->price_raw,
                        'currency' => $stagedPrice->currency_raw,
                        'stock_status' => $stagedPrice->availability_raw ?? 'In Stock',
                        'product_url' => $stagedPerfume->seller_product_url_raw, // Assuming URL is on perfume level for now
                        'size_ml' => (int) (preg_match('/(\d+(?:\.\d+)?)/', $stagedPerfume->size_raw, $m) ? $m[1] : 0), // Extract number from "100ml"
                        'item_type' => $stagedPrice->raw_data_payload['item_type'] ?? 'Full Bottle', // Assuming item_type is in raw_data_payload of price
                        // 'offer_details' - map if available
                        // 'last_updated' will be set by updateOrCreate or during creation
                    ];
                    
                    // De-duplicate and update/create Price
                    $existingPrice = Price::where('perfume_id', $perfume->id)
                                        ->where('seller_id', $sellerId)
                                        ->where('size_ml', $priceData['size_ml'])
                                        ->where('item_type', $priceData['item_type'])
                                        ->first();

                    if ($existingPrice) {
                        $existingPrice->update([
                            'price' => $priceData['price'],
                            'currency' => $priceData['currency'],
                            'stock_status' => $priceData['stock_status'],
                            'product_url' => $priceData['product_url'],
                            'last_updated' => $now,
                        ]);
                        $newOrUpdatedPrice = $existingPrice;
                        Log::channel('ingestion')->info('Production price updated.', ['price_id' => $newOrUpdatedPrice->id, 'perfume_id' => $perfume->id, 'seller_id' => $sellerId, 'size_ml' => $priceData['size_ml'], 'item_type' => $priceData['item_type'], 'staged_perfume_id' => $stagedPerfume->id, 'staged_price_id' => $stagedPrice->id, 'batch_id' => $stagedPerfume->import_batch_id]);
                        $pricesUpdated++;
                    } else {
                        $priceData['last_updated'] = $now; // Set last_updated for new records
                        $newOrUpdatedPrice = Price::create($priceData);
                        Log::channel('ingestion')->info('Production price created.', ['price_id' => $newOrUpdatedPrice->id, 'perfume_id' => $perfume->id, 'seller_id' => $sellerId, 'size_ml' => $priceData['size_ml'], 'item_type' => $priceData['item_type'], 'staged_perfume_id' => $stagedPerfume->id, 'staged_price_id' => $stagedPrice->id, 'batch_id' => $stagedPerfume->import_batch_id]);
                        $pricesCreated++;
                    }
                    $stagedPrice->matched_production_perfume_id = $perfume->id;
                    $stagedPrice->matched_production_price_id = $newOrUpdatedPrice->id;
                    $stagedPrice->processing_status = 'processed';
                    $stagedPrice->validation_status = 'success'; // Assuming basic validation passed
                    $stagedPrice->processed_at = $now;
                    $stagedPrice->save();
                }
                
                                // Deactivation logic moved to after processing all StagingPerfume entries for the batch
                
                                $stagedPerfume->processing_status = 'processed';
                                $stagedPerfume->validation_status = 'success'; // If all prices processed successfully
                $stagedPerfume->processed_at = $now;
                $stagedPerfume->save();
                $processedCount++;
                DB::commit();

            } catch (\Exception $e) {
                DB::rollBack();
                Log::channel('ingestion')->error("Failed to process staged perfume: " . $e->getMessage(), [
                    'staged_perfume_id' => $stagedPerfume->id,
                    'batch_id' => $stagedPerfume->import_batch_id,
                    'seller_code_raw' => $stagedPerfume->seller_code_raw ?? 'N/A',
                    'exception_message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                
                // Mark as failed to prevent reprocessing indefinitely
                $stagedPerfume->processing_status = 'failed';
                $stagedPerfume->error_details = ['error' => 'Exception during processing: ' . $e->getMessage()];
                $stagedPerfume->processed_at = $now;
                $stagedPerfume->save(); // Save outside transaction if rollback occurred, or use separate DB call
                
                $failedCount++;
            }
        }
        
                // Deactivation logic is now handled by performBatchDeactivation method
                // and called from the command after the batch is fully processed.
        
                $resultArray = [
                    'message' => "Processing complete. Processed: {$processedCount}, Perfumes Created: {$perfumesCreated}, Perfumes Updated: {$perfumesUpdated}, Prices Created: {$pricesCreated}, Prices Updated: {$pricesUpdated}, Failed: {$failedCount}",
                    'processed_count' => $processedCount,
                    'perfumes_created' => $perfumesCreated,
                    'perfumes_updated' => $perfumesUpdated,
                    'prices_created' => $pricesCreated,
                    'prices_updated' => $pricesUpdated,
                    'failed_count' => $failedCount,
                ];
                Log::channel('ingestion')->info('Staging processing summary.', array_merge(['batch_id' => $importBatchId], $resultArray));
                return $resultArray;
    }

    /**
     * Process a single StagingPerfume record by its specific model instance.
     */
    public function processSingleRecord(StagingPerfume $record): array
    {
        $record->loadMissing('stagingPrices');

        // Ensure it's eligible
        if ($record->processing_status !== 'new' || $record->validation_status !== 'pending') {
            return [
                'message' => 'Record is not in a processable state.',
                'processed_count' => 0,
                'perfumes_created' => 0,
                'perfumes_updated' => 0,
                'prices_created' => 0,
                'prices_updated' => 0,
                'failed_count' => 0,
            ];
        }

        // Use processStagedData but scope to just this record's batch + limit 1
        // We need to ensure only THIS record is picked, so we temporarily mark others
        // Instead, just inline the processing for a single record.

        $now = Carbon::now();
        $perfumesCreated = 0;
        $perfumesUpdated = 0;
        $pricesCreated = 0;
        $pricesUpdated = 0;
        $failedCount = 0;

        DB::beginTransaction();
        try {
            $sellerCodeRaw = $record->seller_code_raw;
            if (empty($sellerCodeRaw)) {
                $record->processing_status = 'failed';
                $record->validation_status = 'failed';
                $record->error_details = ['error' => 'Missing seller_code_raw.'];
                $record->processed_at = $now;
                $record->save();
                foreach ($record->stagingPrices as $sp) {
                    $sp->update(['processing_status' => 'failed', 'validation_status' => 'failed', 'error_details' => ['error' => 'Parent perfume failed due to missing seller_code_raw.'], 'processed_at' => $now]);
                }
                DB::commit();
                throw new \Exception('Missing seller_code_raw.');
            }

            $seller = Seller::where('code', $sellerCodeRaw)->first();
            if (!$seller) {
                $errorMessage = "Seller with code '{$sellerCodeRaw}' not found.";
                $record->processing_status = 'failed';
                $record->validation_status = 'failed';
                $record->error_details = ['error' => $errorMessage];
                $record->processed_at = $now;
                $record->save();
                foreach ($record->stagingPrices as $sp) {
                    $sp->update(['processing_status' => 'failed', 'validation_status' => 'failed', 'error_details' => ['error' => $errorMessage], 'processed_at' => $now]);
                }
                DB::commit();
                throw new \Exception($errorMessage);
            }
            $sellerId = $seller->id;

            if (empty($record->perfume_name_raw) || empty($record->brand_name_raw)) {
                $record->processing_status = 'failed';
                $record->validation_status = 'failed';
                $record->error_details = ['error' => 'Missing perfume name or brand.'];
                $record->processed_at = $now;
                $record->save();
                foreach ($record->stagingPrices as $sp) {
                    $sp->update(['processing_status' => 'failed', 'validation_status' => 'failed', 'error_details' => ['error' => 'Parent perfume failed due to missing name/brand.'], 'processed_at' => $now]);
                }
                DB::commit();
                throw new \Exception('Missing perfume name or brand.');
            }

            $normalizedName = trim($record->perfume_name_raw);
            $normalizedBrand = trim($record->brand_name_raw);

            $perfume = Perfume::where(function ($query) use ($normalizedName, $normalizedBrand) {
                $query->whereRaw('LOWER(TRIM(name)) = LOWER(?)', [$normalizedName])
                      ->whereRaw('LOWER(TRIM(brand)) = LOWER(?)', [$normalizedBrand]);
            })->first();

            $perfumeData = [
                'name' => $record->perfume_name_raw,
                'brand' => $record->brand_name_raw,
                'description' => $record->description_raw,
                'notes' => $record->notes_raw,
                'image_url' => $record->image_url_raw,
                'concentration' => $record->concentration_raw,
                'gender_affinity' => $record->gender_raw,
            ];

            if ($perfume) {
                $perfume->update(array_filter($perfumeData, fn($value) => $value !== null));
                $perfumesUpdated++;
            } else {
                $perfume = Perfume::create($perfumeData);
                $perfumesCreated++;
            }
            $record->matched_production_perfume_id = $perfume->id;

            foreach ($record->stagingPrices as $stagedPrice) {
                if (empty($stagedPrice->price_raw) || empty($stagedPrice->currency_raw) || empty($record->size_raw)) {
                    $stagedPrice->update(['processing_status' => 'failed', 'validation_status' => 'failed', 'error_details' => ['error' => 'Missing price, currency, or size.'], 'processed_at' => $now]);
                    continue;
                }

                $priceData = [
                    'perfume_id' => $perfume->id,
                    'seller_id' => $sellerId,
                    'price' => $stagedPrice->price_raw,
                    'currency' => $stagedPrice->currency_raw,
                    'stock_status' => $stagedPrice->availability_raw ?? 'In Stock',
                    'product_url' => $record->seller_product_url_raw,
                    'size_ml' => (int) (preg_match('/(\d+(?:\.\d+)?)/', $record->size_raw, $m) ? $m[1] : 0),
                    'item_type' => $stagedPrice->raw_data_payload['item_type'] ?? 'Full Bottle',
                ];

                $existingPrice = Price::where('perfume_id', $perfume->id)
                    ->where('seller_id', $sellerId)
                    ->where('size_ml', $priceData['size_ml'])
                    ->where('item_type', $priceData['item_type'])
                    ->first();

                if ($existingPrice) {
                    $existingPrice->update([
                        'price' => $priceData['price'],
                        'currency' => $priceData['currency'],
                        'stock_status' => $priceData['stock_status'],
                        'product_url' => $priceData['product_url'],
                        'last_updated' => $now,
                    ]);
                    $pricesUpdated++;
                } else {
                    $priceData['last_updated'] = $now;
                    Price::create($priceData);
                    $pricesCreated++;
                }

                $stagedPrice->update([
                    'matched_production_perfume_id' => $perfume->id,
                    'processing_status' => 'processed',
                    'validation_status' => 'success',
                    'processed_at' => $now,
                ]);
            }

            $record->processing_status = 'processed';
            $record->validation_status = 'success';
            $record->processed_at = $now;
            $record->save();
            DB::commit();

            Log::channel('ingestion')->info('Single record processed.', ['staged_perfume_id' => $record->id, 'perfume' => $record->perfume_name_raw]);

        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            Log::channel('ingestion')->error("Failed to process single record: " . $e->getMessage(), ['staged_perfume_id' => $record->id]);
            throw $e;
        }

        return [
            'message' => 'Processing complete.',
            'processed_count' => 1,
            'perfumes_created' => $perfumesCreated,
            'perfumes_updated' => $perfumesUpdated,
            'prices_created' => $pricesCreated,
            'prices_updated' => $pricesUpdated,
            'failed_count' => $failedCount,
        ];
    }

    /**
     * Check if a batch is fully processed and perform deactivation if so.
     *
     * @return array|null Deactivation result if batch was complete, null otherwise.
     */
    public function checkBatchCompletionAndDeactivate(string $importBatchId): ?array
    {
        $remaining = StagingPerfume::where('import_batch_id', $importBatchId)
            ->where('processing_status', 'new')
            ->count();

        if ($remaining === 0) {
            return $this->performBatchDeactivation($importBatchId);
        }

        return null;
    }

    /**
     * Performs deactivation of prices for a fully processed batch.
     * It identifies production prices that were part of a previous import from this seller for a perfume,
     * but are not present in the current (fully processed) batch, and marks them as 'Out of Stock'.
     *
     * @param string $importBatchId The import batch ID that has been fully processed.
     * @param Carbon|null $deactivationTimestamp The timestamp to use for deactivation. Defaults to now().
     * @return array Counts of deactivated prices and any errors.
     */
    public function performBatchDeactivation(string $importBatchId, ?Carbon $deactivationTimestamp = null): array
    {
        $deactivationTimestamp = $deactivationTimestamp ?? Carbon::now();
        $deactivatedPricesCount = 0;
        $deactivationErrors = [];

        Log::channel('ingestion')->info("Starting batch deactivation process.", ['batch_id' => $importBatchId, 'deactivation_timestamp' => $deactivationTimestamp->toIso8601String()]);

        // Get all successfully processed staging prices for this batch
        // along with their matched production perfume and seller details.
        $processedStagingPrices = StagingPrice::where('import_batch_id', $importBatchId)
            ->where('processing_status', 'processed')
            ->whereNotNull('matched_production_price_id')
            ->with('stagingPerfume:id,seller_code_raw,matched_production_perfume_id') // Eager load necessary fields
            ->get();

        if ($processedStagingPrices->isEmpty()) {
            Log::channel('ingestion')->info("No successfully processed staging prices found for batch to perform deactivation.", ['batch_id' => $importBatchId]);
            // This might mean the batch had no prices, or all failed.
            // We still need to check if this seller had *any* prices before for perfumes in this batch.
            // For simplicity now, if no processed prices in this batch, we assume no "live" prices from this batch.
            // A more robust approach might involve checking StagingPerfume entries for the batch to get seller/perfume combos.
        }

        // Pre-load all sellers referenced in this batch to avoid N+1 queries
        $sellerCodes = $processedStagingPrices
            ->pluck('stagingPerfume.seller_code_raw')
            ->filter()
            ->unique()
            ->values();
        $sellersByCode = Seller::whereIn('code', $sellerCodes)->pluck('id', 'code');

        // Group by seller, then by production perfume ID
        $liveProductionPriceIdsBySellerPerfume = [];
        $sellerPerfumeCombinationsInBatch = [];

        foreach ($processedStagingPrices as $sp) {
            if ($sp->stagingPerfume && $sp->stagingPerfume->seller_code_raw && $sp->stagingPerfume->matched_production_perfume_id) {
                $sellerCode = $sp->stagingPerfume->seller_code_raw;
                $prodPerfumeId = $sp->stagingPerfume->matched_production_perfume_id;

                $sellerId = $sellersByCode[$sellerCode] ?? null;
                if (!$sellerId) {
                    Log::channel('ingestion')->warning("Seller not found during deactivation for seller_code_raw.", ['seller_code_raw' => $sellerCode, 'batch_id' => $importBatchId]);
                    continue;
                }

                if (!isset($liveProductionPriceIdsBySellerPerfume[$sellerId])) {
                    $liveProductionPriceIdsBySellerPerfume[$sellerId] = [];
                }
                if (!isset($liveProductionPriceIdsBySellerPerfume[$sellerId][$prodPerfumeId])) {
                    $liveProductionPriceIdsBySellerPerfume[$sellerId][$prodPerfumeId] = [];
                }
                $liveProductionPriceIdsBySellerPerfume[$sellerId][$prodPerfumeId][] = $sp->matched_production_price_id;
                $sellerPerfumeCombinationsInBatch[$sellerId][$prodPerfumeId] = true; // Mark this combo as present in batch
            }
        }
        
        // Iterate over all unique seller/perfume combinations that were present in this batch.
        // This ensures we only deactivate for sellers/perfumes touched by this batch.
        foreach (array_keys($sellerPerfumeCombinationsInBatch) as $sellerId) {
            foreach (array_keys($sellerPerfumeCombinationsInBatch[$sellerId]) as $perfumeId) {
                DB::beginTransaction();
                try {
                    $liveProductionPriceIdsForGroup = $liveProductionPriceIdsBySellerPerfume[$sellerId][$perfumeId] ?? [];
                    $uniqueLiveProductionPriceIds = array_unique($liveProductionPriceIdsForGroup);

                    // Get all existing production prices for this seller and perfume
                    $existingProductionPricesQuery = Price::where('seller_id', $sellerId)
                        ->where('perfume_id', $perfumeId);
                        // Optionally, only consider deactivating 'In Stock' items:
                        // ->where('stock_status', 'In Stock');

                    $pricesToDeactivate = $existingProductionPricesQuery->whereNotIn('id', $uniqueLiveProductionPriceIds)->get();

                    if ($pricesToDeactivate->isNotEmpty()) {
                        $deactivatedIds = $pricesToDeactivate->pluck('id')->all();
                        Price::whereIn('id', $deactivatedIds)
                            ->update([
                                'stock_status' => 'Out of Stock',
                                'last_updated' => $deactivationTimestamp,
                            ]);
                        $deactivatedPricesCount += count($deactivatedIds);
                        Log::channel('ingestion')->info('Production prices deactivated for seller/perfume.', [
                            'batch_id' => $importBatchId,
                            'seller_id' => $sellerId,
                            'perfume_id' => $perfumeId,
                            'deactivated_price_ids' => $deactivatedIds,
                            'count' => count($deactivatedIds)
                        ]);
                    }
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    $errorMessage = "Error during deactivation for seller ID {$sellerId}, perfume ID {$perfumeId}: " . $e->getMessage();
                    Log::channel('ingestion')->error($errorMessage, [
                        'batch_id' => $importBatchId,
                        'seller_id' => $sellerId,
                        'perfume_id' => $perfumeId,
                        'exception_message' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                    $deactivationErrors[] = $errorMessage;
                }
            }
        }

        Log::channel('ingestion')->info("Batch deactivation process finished.", [
            'batch_id' => $importBatchId,
            'deactivated_prices_count' => $deactivatedPricesCount,
            'deactivation_errors_count' => count($deactivationErrors)
        ]);

        return [
            'deactivated_prices_count' => $deactivatedPricesCount,
            'errors' => $deactivationErrors,
        ];
    }
}