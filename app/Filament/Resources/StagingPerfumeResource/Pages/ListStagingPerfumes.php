<?php

namespace App\Filament\Resources\StagingPerfumeResource\Pages;

use App\Filament\Resources\StagingPerfumeResource;
use App\Models\Seller;
use App\Models\StagingPerfume;
use App\Services\DataIngestion\Parsers\ExcelParserService;
use App\Services\DataIngestion\StagingDataService;
use App\Services\DataIngestion\StagingProcessorService;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class ListStagingPerfumes extends ListRecords
{
    protected static string $resource = StagingPerfumeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Download Template Action
            Action::make('download_template')
                ->label('Download Template')
                ->icon('heroicon-o-document-arrow-down')
                ->color('gray')
                ->action(function () {
                    $templatePath = storage_path('app/public/templates/perfume_import_template.xlsx');

                    if (!file_exists($templatePath)) {
                        // Generate template if it doesn't exist
                        \Artisan::call('template:generate');
                    }

                    return response()->download(
                        $templatePath,
                        'perfume_import_template.xlsx',
                        ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
                    );
                }),

            // Import Excel Action
            Action::make('import_excel')
                ->label('Import Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('info')
                ->form([
                    Placeholder::make('template_info')
                        ->content(new HtmlString('
                            <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                <p class="text-sm text-blue-700 dark:text-blue-300">
                                    <strong>📋 Need the template?</strong> Use the "Download Template" button to get the correct format.
                                </p>
                                <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                                    Required fields: Perfume Name, Brand, Size (ml), Price, Currency
                                </p>
                            </div>
                        ')),
                    FileUpload::make('file')
                        ->label('Excel File')
                        ->required()
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-excel',
                            '.xlsx',
                            '.xls',
                        ])
                        ->directory('imports')
                        ->preserveFilenames()
                        ->maxSize(10240), // 10MB max
                    Select::make('seller_code')
                        ->label('Seller')
                        ->options(
                            Seller::all()->mapWithKeys(function ($seller) {
                                // Use code if available, otherwise use ID as fallback
                                $key = $seller->code ?: 'id_' . $seller->id;
                                return [$key => $seller->name . ($seller->code ? " ({$seller->code})" : ' [No Code]')];
                            })->toArray()
                        )
                        ->required()
                        ->searchable()
                        ->helperText('Select the seller this data belongs to. Sellers without codes will fail during processing.'),
                ])
                ->action(function (array $data): void {
                    try {
                        $filePath = storage_path('app/public/' . $data['file']);
                        $sellerCode = $data['seller_code'];

                        // Warn if using ID-based fallback
                        if (str_starts_with($sellerCode, 'id_')) {
                            Notification::make()
                                ->title('Warning')
                                ->body('Selected seller has no code. Please add a code to the seller before processing.')
                                ->warning()
                                ->send();
                        }

                        $importBatchId = (string) Str::uuid();

                        // Parse the Excel file
                        $excelParser = app(ExcelParserService::class);
                        $parsedData = $excelParser->parse($filePath);
                        $parserErrors = $excelParser->getErrors();
                        $sourceIdentifier = $excelParser->getSourceIdentifier();

                        if (!empty($parserErrors)) {
                            Notification::make()
                                ->title('Parsing Warnings')
                                ->body(implode("\n", array_slice($parserErrors, 0, 5)))
                                ->warning()
                                ->send();
                        }

                        if (empty($parsedData)) {
                            Notification::make()
                                ->title('Import Failed')
                                ->body('No data found in the Excel file. Check that headers match the template.')
                                ->danger()
                                ->send();
                            return;
                        }

                        // Stage the data
                        $stagingService = app(StagingDataService::class);
                        $result = $stagingService->stageData($parsedData, $sourceIdentifier, $importBatchId, $sellerCode);

                        Notification::make()
                            ->title('Import Successful')
                            ->body("Staged {$result['perfumes_staged']} perfumes and {$result['prices_staged']} prices. Batch ID: " . substr($importBatchId, 0, 8))
                            ->success()
                            ->send();

                        // Clean up uploaded file
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Import Failed')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            // Process All Pending Action
            Action::make('process_all')
                ->label('Process All to Production')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Process All Staging Data')
                ->modalDescription('This will move all pending staging data to production tables. Are you sure?')
                ->modalSubmitActionLabel('Process All')
                ->action(function (): void {
                    try {
                        $processorService = app(StagingProcessorService::class);
                        $result = $processorService->processStagedData(null, 500);

                        Notification::make()
                            ->title('Processing Complete')
                            ->body("Processed: {$result['processed_count']}, Created: {$result['perfumes_created']}, Updated: {$result['perfumes_updated']}, Failed: {$result['failed_count']}")
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Processing Failed')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                })
                ->visible(fn() => StagingPerfume::where('processing_status', 'new')->exists()),

            // Clear All Staging Data
            Action::make('clear_staging')
                ->label('Clear All Staging')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Clear All Staging Data')
                ->modalDescription('This will permanently delete ALL staging data. This cannot be undone.')
                ->modalSubmitActionLabel('Delete All')
                ->action(function (): void {
                    StagingPerfume::query()->delete();

                    Notification::make()
                        ->title('Staging Cleared')
                        ->body('All staging data has been deleted.')
                        ->success()
                        ->send();
                }),

            Actions\CreateAction::make(),
        ];
    }
}
