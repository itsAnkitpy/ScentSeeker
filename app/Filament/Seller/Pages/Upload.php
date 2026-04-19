<?php

namespace App\Filament\Seller\Pages;

use App\Jobs\ProcessExcelIngestionJob;
use App\Models\StagingPerfume;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Upload extends Page implements HasActions, HasForms, HasTable
{
    use InteractsWithActions, InteractsWithForms, InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-tray';

    protected static ?string $navigationLabel = 'Upload Prices';

    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.seller.pages.upload';

    public function uploadAction(): Action
    {
        return Action::make('upload')
            ->label('Upload Price List')
            ->icon('heroicon-o-arrow-up-tray')
            ->modalHeading('Upload Price List')
            ->modalDescription('Upload an .xlsx or .xls file (max 20MB). Use the template for the correct format.')
            ->modalSubmitActionLabel('Upload')
            ->form([
                FileUpload::make('file')
                    ->label('Excel File')
                    ->disk('local')
                    ->directory('seller-uploads')
                    ->acceptedFileTypes([
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/vnd.ms-excel',
                    ])
                    ->maxSize(20480)
                    ->required(),
            ])
            ->action(function (array $data): void {
                $user = Auth::user();
                $sellerCode = $user?->seller?->code;

                if (! $sellerCode) {
                    Log::channel('ingestion')->error('Seller upload aborted: no seller linked to user', [
                        'user_id' => $user?->id,
                    ]);

                    Notification::make()
                        ->title('Upload failed')
                        ->body('Your account is not linked to a seller. Please contact support.')
                        ->danger()
                        ->send();

                    return;
                }

                try {
                    $filePath = storage_path('app/private/'.$data['file']);

                    if (! file_exists($filePath)) {
                        throw new \RuntimeException("Uploaded file not found at {$filePath}");
                    }

                    ProcessExcelIngestionJob::dispatch($filePath, $sellerCode);

                    Log::channel('ingestion')->info('Seller upload queued', [
                        'user_id' => $user->id,
                        'seller_code' => $sellerCode,
                        'file_path' => $filePath,
                    ]);

                    Notification::make()
                        ->title('Upload queued')
                        ->body('Your file is being processed. Staged rows will appear in Upload History shortly.')
                        ->success()
                        ->send();
                } catch (\Throwable $e) {
                    Log::channel('ingestion')->error('Seller upload dispatch failed: '.$e->getMessage(), [
                        'user_id' => $user->id,
                        'seller_code' => $sellerCode,
                        'exception' => $e,
                    ]);

                    Notification::make()
                        ->title('Upload failed')
                        ->body('We could not queue your upload. Please try again or contact support.')
                        ->danger()
                        ->send();
                }
            });
    }

    public function downloadTemplateAction(): Action
    {
        return Action::make('downloadTemplate')
            ->label('Download Template')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('gray')
            ->action(fn () => $this->downloadTemplate());
    }

    public function downloadTemplate(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $path = storage_path('app/public/templates/perfume_import_template.xlsx');

        if (! file_exists($path)) {
            \Artisan::call('template:generate');
        }

        return response()->download($path, 'scentcents_import_template.xlsx');
    }

    public function getTableRecordKey($record): string
    {
        return $record->import_batch_id;
    }

    public function table(Table $table): Table
    {
        $sellerCode = Auth::user()->seller?->code;

        return $table
            ->query(
                StagingPerfume::query()
                    ->where('seller_code_raw', $sellerCode)
                    ->selectRaw('import_batch_id, MIN(created_at) as upload_date, COUNT(*) as row_count')
                    ->groupBy('import_batch_id')
            )
            ->columns([
                TextColumn::make('import_batch_id')
                    ->label('Batch ID')
                    ->limit(8)
                    ->copyable()
                    ->tooltip(fn ($record) => $record->import_batch_id),
                TextColumn::make('row_count')
                    ->label('Rows')
                    ->badge()
                    ->color('info'),
                TextColumn::make('upload_date')
                    ->label('Uploaded')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('upload_date', 'desc')
            ->heading('Upload History')
            ->paginated([5]);
    }
}
