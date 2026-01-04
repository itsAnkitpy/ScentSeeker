<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StagingPerfumeResource\Pages;
use App\Models\StagingPerfume;
use App\Services\DataIngestion\StagingProcessorService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class StagingPerfumeResource extends Resource
{
    protected static ?string $model = StagingPerfume::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-arrow-down';

    protected static ?string $navigationLabel = 'Staging Data';

    protected static ?string $navigationGroup = 'Data Import';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        $count = StagingPerfume::where('processing_status', 'new')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Import Information')
                    ->schema([
                        Forms\Components\TextInput::make('import_batch_id')
                            ->label('Batch ID')
                            ->disabled(),
                        Forms\Components\TextInput::make('seller_code_raw')
                            ->label('Seller Code')
                            ->disabled(),
                        Forms\Components\TextInput::make('processing_status')
                            ->disabled(),
                        Forms\Components\TextInput::make('validation_status')
                            ->disabled(),
                    ])->columns(2),

                Forms\Components\Section::make('Perfume Details')
                    ->schema([
                        Forms\Components\TextInput::make('perfume_name_raw')
                            ->label('Name'),
                        Forms\Components\TextInput::make('brand_name_raw')
                            ->label('Brand'),
                        Forms\Components\TextInput::make('concentration_raw')
                            ->label('Concentration'),
                        Forms\Components\TextInput::make('size_raw')
                            ->label('Size'),
                        Forms\Components\TextInput::make('gender_raw')
                            ->label('Gender'),
                        Forms\Components\Textarea::make('description_raw')
                            ->label('Description')
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Additional Info')
                    ->schema([
                        Forms\Components\TextInput::make('image_url_raw')
                            ->label('Image URL'),
                        Forms\Components\TextInput::make('seller_product_url_raw')
                            ->label('Product URL'),
                        Forms\Components\Textarea::make('error_details')
                            ->label('Errors')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('processing_status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'new' => 'warning',
                        'processed' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('perfume_name_raw')
                    ->label('Name')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('brand_name_raw')
                    ->label('Brand')
                    ->searchable(),
                Tables\Columns\TextColumn::make('concentration_raw')
                    ->label('Conc.')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('size_raw')
                    ->label('Size'),
                Tables\Columns\TextColumn::make('seller_code_raw')
                    ->label('Seller')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('import_batch_id')
                    ->label('Batch')
                    ->limit(8)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('imported_at')
                    ->label('Imported')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stagingPrices_count')
                    ->label('Prices')
                    ->counts('stagingPrices')
                    ->badge()
                    ->color('success'),
            ])
            ->defaultSort('imported_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('processing_status')
                    ->options([
                        'new' => 'Pending',
                        'processed' => 'Processed',
                        'failed' => 'Failed',
                    ]),
                Tables\Filters\SelectFilter::make('seller_code_raw')
                    ->label('Seller')
                    ->options(fn() => StagingPerfume::distinct()->pluck('seller_code_raw', 'seller_code_raw')->toArray()),
                Tables\Filters\SelectFilter::make('import_batch_id')
                    ->label('Batch')
                    ->options(fn() => StagingPerfume::distinct()->pluck('import_batch_id', 'import_batch_id')->toArray()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('process_single')
                    ->label('Process')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn(StagingPerfume $record) => $record->processing_status === 'new')
                    ->action(function (StagingPerfume $record): void {
                        try {
                            $processorService = app(StagingProcessorService::class);
                            $result = $processorService->processStagedData($record->import_batch_id, 1);

                            Notification::make()
                                ->title('Processed Successfully')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Processing Failed')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('process_selected')
                        ->label('Process to Production')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records): void {
                            $processed = 0;
                            $failed = 0;

                            foreach ($records as $record) {
                                if ($record->processing_status !== 'new') {
                                    continue;
                                }

                                try {
                                    $processorService = app(StagingProcessorService::class);
                                    $processorService->processStagedData($record->import_batch_id, 1);
                                    $processed++;
                                } catch (\Exception $e) {
                                    $failed++;
                                }
                            }

                            Notification::make()
                                ->title('Bulk Processing Complete')
                                ->body("Processed: {$processed}, Failed: {$failed}")
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\BulkAction::make('mark_failed')
                        ->label('Mark as Failed')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records): void {
                            $records->each(function (StagingPerfume $record) {
                                $record->update(['processing_status' => 'failed']);
                            });

                            Notification::make()
                                ->title('Records Marked as Failed')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStagingPerfumes::route('/'),
            'create' => Pages\CreateStagingPerfume::route('/create'),
            'edit' => Pages\EditStagingPerfume::route('/{record}/edit'),
        ];
    }
}
