<?php

namespace App\Jobs;

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

    public $tries = 3;

    public const MAX_DISPATCHES = 50;

    public function __construct(
        public ?string $batchId = null,
        public int $limit = 100,
        public int $dispatchCount = 1
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
                $deactivationResult = $stagingProcessorService->checkBatchCompletionAndDeactivate($this->batchId);

                if ($deactivationResult === null && $result['processed_count'] === $this->limit) {
                    $this->dispatchNext();
                }
            } elseif (!$this->batchId && $result['processed_count'] === $this->limit) {
                $this->dispatchNext();
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

    private function dispatchNext(): void
    {
        if ($this->dispatchCount >= self::MAX_DISPATCHES) {
            Log::channel('ingestion')->warning("Circuit breaker: reached max dispatch limit", [
                'batch_id' => $this->batchId,
                'dispatch_count' => $this->dispatchCount,
            ]);

            return;
        }

        Log::channel('ingestion')->info("More records to process, dispatching another job", [
            'batch_id' => $this->batchId,
            'dispatch_count' => $this->dispatchCount + 1,
        ]);

        self::dispatch($this->batchId, $this->limit, $this->dispatchCount + 1)
            ->delay(now()->addSeconds(5));
    }
}
