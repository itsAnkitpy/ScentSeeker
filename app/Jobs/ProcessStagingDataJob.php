<?php

namespace App\Jobs;

use App\Models\StagingPerfume;
use App\Services\DataIngestion\StagingProcessorService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessStagingDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public ?string $batchId = null,
        public int $limit = 100
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(StagingProcessorService $stagingProcessorService): void
    {
        Log::channel('ingestion')->info("Processing staging data job", [
            'batch_id' => $this->batchId,
            'limit' => $this->limit,
        ]);

        try {
            $result = $stagingProcessorService->processStagedData($this->batchId, $this->limit);

            Log::channel('ingestion')->info("Staging data processing job completed", [
                'batch_id' => $this->batchId,
                'result' => $result,
            ]);

            // If a specific batch was processed and it's complete, perform deactivation
            if ($this->batchId && $result['processed_count'] > 0) {
                // Check if batch is fully processed
                $remainingInBatch = StagingPerfume::where('import_batch_id', $this->batchId)
                    ->where('processing_status', 'new')
                    ->count();
                
                if ($remainingInBatch === 0) {
                    $stagingProcessorService->performBatchDeactivation($this->batchId);
                } elseif ($result['processed_count'] === $this->limit) {
                    // More records to process, dispatch another job
                    Log::channel('ingestion')->info("More records to process, dispatching another job", [
                        'batch_id' => $this->batchId,
                    ]);
                    self::dispatch($this->batchId, $this->limit)->delay(now()->addSeconds(5));
                }
            } elseif (!$this->batchId && $result['processed_count'] === $this->limit) {
                // Processing all pending records, dispatch another job if more exist
                Log::channel('ingestion')->info("More records to process, dispatching another job");
                self::dispatch(null, $this->limit)->delay(now()->addSeconds(5));
            }

        } catch (\Exception $e) {
            Log::channel('ingestion')->error("Staging data processing job failed", [
                'batch_id' => $this->batchId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
