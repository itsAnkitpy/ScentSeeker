<?php

namespace App\Console\Commands;

use App\Services\DataIngestion\Parsers\ExcelParserService;
use App\Services\DataIngestion\Parsers\Exceptions\ParserException;
use App\Services\DataIngestion\StagingDataService; // Added
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str; // Added for UUID generation

class IngestExcelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ingest:excel {--file= : The path to the Excel file to ingest.} {--seller-code= : The unique code of the seller providing the sheet.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ingests perfume data from a specified Excel file into the staging tables.';

    protected ExcelParserService $excelParser;
    protected StagingDataService $stagingDataService; // Added

    /**
     * Create a new command instance.
     *
     * @param ExcelParserService $excelParser
     * @param StagingDataService $stagingDataService // Added
     */
    public function __construct(ExcelParserService $excelParser, StagingDataService $stagingDataService) // Modified
    {
        parent::__construct();
        $this->excelParser = $excelParser;
        $this->stagingDataService = $stagingDataService; // Added
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $filePath = $this->option('file');
        $sellerCode = $this->option('seller-code');

        if (empty($filePath)) {
            $this->error('The --file option is required.');
            return Command::FAILURE;
        }

        if (empty($sellerCode)) {
            $this->error('The --seller-code option is required.');
            return Command::FAILURE;
        }

        if (!file_exists($filePath) || !is_readable($filePath)) {
            $this->error("The file '{$filePath}' does not exist or is not readable.");
            return Command::FAILURE;
        }

        $this->info("Attempting to ingest data from: {$filePath} for seller: {$sellerCode}");
        $importBatchId = (string) Str::uuid();
        $this->info("Generated Import Batch ID: {$importBatchId}");

        try {
            $parsedData = $this->excelParser->parse($filePath);
            $parserErrors = $this->excelParser->getErrors();
            $sourceIdentifier = $this->excelParser->getSourceIdentifier();

            if (!empty($parserErrors)) {
                $this->warn('Parsing completed with some issues:');
                foreach ($parserErrors as $error) {
                    $this->warn("- {$error}");
                    Log::warning("Excel Ingestion Warning ({$filePath}, Batch: {$importBatchId}, Seller: {$sellerCode}): {$error}");
                }
            }

            if (empty($parsedData) && !empty($parserErrors)) {
                $this->error('No data was successfully parsed due to errors. Please check warnings and logs.');
                return Command::FAILURE;
            } elseif (empty($parsedData)) {
                $this->info('No data was found or parsed from the Excel file.');
                return Command::SUCCESS;
            }

            $this->info(count($parsedData) . ' records parsed successfully.');
            
            $this->info("Staging parsed data with Source: {$sourceIdentifier}, Batch ID: {$importBatchId}, Seller Code: {$sellerCode}...");
            
            $stagingResult = $this->stagingDataService->stageData($parsedData, $sourceIdentifier, $importBatchId, $sellerCode);

            $this->info("Staging complete.");
            $this->info("Perfumes Staged: " . $stagingResult['perfumes_staged']);
            $this->info("Prices Staged: " . $stagingResult['prices_staged']);

            if (($stagingResult['perfumes_staged'] + $stagingResult['prices_staged']) < count($parsedData)) {
                $this->warn("Some parsed records might not have been staged. Check logs for details (Batch ID: {$importBatchId}, Seller: {$sellerCode}).");
            }

        } catch (ParserException $e) {
            $this->error("Parser Error: {$e->getMessage()}");
            Log::error("Excel Ingestion Parser Error ({$filePath}, Batch: {$importBatchId}, Seller: {$sellerCode}): {$e->getMessage()}");
            return Command::FAILURE;
        } catch (\Exception $e) {
            $this->error("An unexpected error occurred during staging (Batch ID: {$importBatchId}, Seller: {$sellerCode}): {$e->getMessage()}");
            Log::error("Excel Ingestion Staging Error ({$filePath}, Batch: {$importBatchId}, Seller: {$sellerCode}): {$e->getMessage()}");
            Log::error($e->getTraceAsString());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
