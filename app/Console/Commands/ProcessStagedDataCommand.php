<?php

namespace App\Console\Commands;

use App\Services\DataIngestion\StagingProcessorService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessStagedDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:staged-data
                            {--batchId= : Optionally specify an import_batch_id to process.}
                            {--limit=100 : The maximum number of staging records to process in one run.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Processes validated data from staging tables and imports it into production tables.';

    protected StagingProcessorService $stagingProcessorService;

    /**
     * Create a new command instance.
     *
     * @param StagingProcessorService $stagingProcessorService
     */
    public function __construct(StagingProcessorService $stagingProcessorService)
    {
        parent::__construct();
        $this->stagingProcessorService = $stagingProcessorService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $batchId = $this->option('batchId');
        $limit = (int) $this->option('limit');

        if ($limit <= 0) {
            $this->error('The --limit option must be a positive integer.');
            return Command::FAILURE;
        }

        $this->info("Starting to process staged data...");
        if ($batchId) {
            $this->info("Processing for Batch ID: {$batchId}");
        }
        $this->info("Record limit per run: {$limit}");

        try {
            $result = $this->stagingProcessorService->processStagedData($batchId, $limit);
            
            $this->info($result['message']);
            $this->line("Perfumes Created: " . $result['perfumes_created']);
            $this->line("Perfumes Updated: " . $result['perfumes_updated']);
            $this->line("Prices Created: " . $result['prices_created']);
            // $this->line("Prices Updated: " . $result['prices_updated']); // If implemented
            $this->line("Failed Records: " . $result['failed_count']);

            if ($result['failed_count'] > 0) {
                $this->warn("Some records failed to process. Check logs for details.");
            }
            
            if ($result['processed_count'] > 0 && $result['processed_count'] === $limit) {
                $this->info("There might be more records to process. Run the command again if needed.");
            }


        } catch (\Exception $e) {
            $this->error("An unexpected error occurred during staged data processing: {$e->getMessage()}");
            Log::channel('ingestion')->error("Error in ProcessStagedDataCommand: {$e->getMessage()}", [
                'batch_id' => $batchId, // Ensure batch_id is always present, even if null
                'exception_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
