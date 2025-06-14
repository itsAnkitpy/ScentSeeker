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
    
        // To store all processed production price IDs for each seller-perfume combination in the batch
        $batchProcessedPriceIds = [];
    
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
                // Try to find an existing perfume by name and brand (case-insensitive for robustness)
                $perfume = Perfume::whereRaw('LOWER(name) = ?', [strtolower($stagedPerfume->perfume_name_raw)])
                                ->whereRaw('LOWER(brand) = ?', [strtolower($stagedPerfume->brand_name_raw)])
                                ->first();

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

                // Initialize list to track processed production price IDs for this perfume/seller combination
                $processedProductionPriceIds = [];

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
                        'size_ml' => (int) filter_var($stagedPerfume->size_raw, FILTER_SANITIZE_NUMBER_INT), // Extract number from "100ml"
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
                    $processedProductionPriceIds[] = $newOrUpdatedPrice->id; // Track processed price ID for current StagingPerfume
                
                    // Accumulate into batch-wide tracker
                    if (!isset($batchProcessedPriceIds[$sellerId])) {
                        $batchProcessedPriceIds[$sellerId] = [];
                    }
                    if (!isset($batchProcessedPriceIds[$sellerId][$perfume->id])) {
                        $batchProcessedPriceIds[$sellerId][$perfume->id] = [];
                    }
                    $batchProcessedPriceIds[$sellerId][$perfume->id][] = $newOrUpdatedPrice->id;
                    
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
        
                // After processing all StagingPerfume entries, perform deactivation
                $deactivationTimestamp = Carbon::now(); // Use a consistent timestamp for this deactivation run
        
                // Get unique seller IDs for whom items were processed in this batch
                $uniqueSellerIdsProcessedInThisBatch = array_keys($batchProcessedPriceIds);
        
                foreach ($uniqueSellerIdsProcessedInThisBatch as $currentSellerId) {
                    // Get all perfume IDs for which this seller has existing prices in the production table.
                    $perfumeIdsWithPricesForThisSeller = Price::where('seller_id', $currentSellerId)
                                                              ->distinct()
                                                              ->pluck('perfume_id')
                                                              ->all();
        
                    foreach ($perfumeIdsWithPricesForThisSeller as $currentPerfumeId) {
                        DB::beginTransaction();
                        try {
                            // Price IDs from the current sheet/batch for this specific seller/perfume.
                            // If the perfume was not in the sheet for this seller in this batch, this will be an empty array.
                            $processedPriceIdsForCurrentGroup = $batchProcessedPriceIds[$currentSellerId][$currentPerfumeId] ?? [];
                            $uniqueProcessedPriceIdsForCurrentGroup = array_unique($processedPriceIdsForCurrentGroup);
        
                            // All existing price IDs in the DB for this seller/perfume.
                            $existingProductionPriceIdsForGroup = Price::where('perfume_id', $currentPerfumeId)
                                ->where('seller_id', $currentSellerId)
                                // ->where('stock_status', 'In Stock') // Optional: only consider deactivating 'In Stock' items
                                ->pluck('id')->all();
        
                            $outdatedPriceIds = array_diff($existingProductionPriceIdsForGroup, $uniqueProcessedPriceIdsForCurrentGroup);
        
                            if (!empty($outdatedPriceIds)) {
                                Price::whereIn('id', $outdatedPriceIds) // Price IDs are unique, no need for extra seller_id/perfume_id here
                                      ->update([
                                          'stock_status' => 'Out of Stock',
                                          'last_updated' => $deactivationTimestamp,
                                      ]);
                                Log::channel('ingestion')->info('Production prices deactivated (Out of Stock).', ['perfume_id' => $currentPerfumeId, 'seller_id' => $currentSellerId, 'deactivated_price_ids' => $outdatedPriceIds, 'count' => count($outdatedPriceIds), 'batch_id' => $importBatchId ?? 'N/A_batch']);
                                // Optionally, count these updates if needed for the summary (e.g., $pricesDeactivated)
                            }
                            DB::commit();
                        } catch (\Exception $e) {
                            DB::rollBack();
                            Log::channel('ingestion')->error("Error during deactivation for perfume ID {$currentPerfumeId}, seller ID {$currentSellerId}: " . $e->getMessage(), [
                                'exception' => $e,
                                'perfume_id' => $currentPerfumeId,
                                'seller_id' => $currentSellerId,
                                'trace' => $e->getTraceAsString(),
                            ]);
                            // This failure doesn't increment $failedCount for StagingPerfume records,
                            // but it's a failure in a post-processing step.
                        }
                    }
                }
        
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

    // TODO: Implement resolveSellerId if seller information is part of the source or raw data
    // private function resolveSellerId(string $sourceIdentifier, array $rawDataPayload): ?int
    // {
    //     // Logic to find or create a seller based on the source or data
    //     return null;
    // }
}