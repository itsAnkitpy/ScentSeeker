<?php

namespace App\Jobs;

use App\Services\DataIngestion\Parsers\ExcelParserService;
use App\Services\DataIngestion\Parsers\Exceptions\ParserException;
use App\Services\DataIngestion\StagingDataService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProcessExcelIngestionJob implements ShouldQueue
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
        public string $filePath,
        public string $sellerCode
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(ExcelParserService $excelParser, StagingDataService $stagingDataService): void
    {
        $importBatchId = (string) Str::uuid();

        Log::channel('ingestion')->info("Processing Excel ingestion job", [
            'file_path' => $this->filePath,
            'seller_code' => $this->sellerCode,
            'batch_id' => $importBatchId,
        ]);

        try {
            $parsedData = $excelParser->parse($this->filePath);
            $parserErrors = $excelParser->getErrors();
            $sourceIdentifier = $excelParser->getSourceIdentifier();

            if (!empty($parserErrors)) {
                foreach ($parserErrors as $error) {
                    Log::channel('ingestion')->warning("Excel Ingestion Warning: {$error}", [
                        'file_path' => $this->filePath,
                        'seller_code' => $this->sellerCode,
                        'batch_id' => $importBatchId,
                    ]);
                }
            }

            if (empty($parsedData) && !empty($parserErrors)) {
                throw new ParserException('No data was successfully parsed due to errors.');
            }

            if (empty($parsedData)) {
                Log::channel('ingestion')->info("No data found in Excel file", [
                    'file_path' => $this->filePath,
                    'seller_code' => $this->sellerCode,
                    'batch_id' => $importBatchId,
                ]);
                return;
            }

            $stagingResult = $stagingDataService->stageData(
                $parsedData,
                $sourceIdentifier,
                $importBatchId,
                $this->sellerCode
            );

            Log::channel('ingestion')->info("Excel ingestion job completed successfully", [
                'file_path' => $this->filePath,
                'seller_code' => $this->sellerCode,
                'batch_id' => $importBatchId,
                'perfumes_staged' => $stagingResult['perfumes_staged'],
                'prices_staged' => $stagingResult['prices_staged'],
            ]);

            // Dispatch processing job for the staged data
            ProcessStagingDataJob::dispatch($importBatchId)->delay(now()->addSeconds(10));

        } catch (ParserException $e) {
            Log::channel('ingestion')->error("Excel Ingestion Parser Error: {$e->getMessage()}", [
                'file_path' => $this->filePath,
                'seller_code' => $this->sellerCode,
                'batch_id' => $importBatchId,
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::channel('ingestion')->error("Excel Ingestion Error: {$e->getMessage()}", [
                'file_path' => $this->filePath,
                'seller_code' => $this->sellerCode,
                'batch_id' => $importBatchId,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
