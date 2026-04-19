<?php

namespace App\Filament\Seller\Resources;

use App\Filament\Seller\Resources\StagingPerfumeResource\Pages;
use App\Models\StagingPerfume;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class StagingPerfumeResource extends Resource
{
    protected static ?string $model = StagingPerfume::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Staged Data';

    protected static ?string $modelLabel = 'Staged Record';

    protected static ?string $pluralModelLabel = 'Staged Records';

    protected static ?int $navigationSort = 3;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('seller_code_raw', Auth::user()->seller?->code);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('import_batch_id')
                    ->label('Batch')
                    ->limit(8)
                    ->copyable()
                    ->tooltip(fn ($record) => $record->import_batch_id)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('perfume_name_raw')
                    ->label('Perfume')
                    ->searchable()
                    ->sortable()
                    ->limit(35),
                Tables\Columns\TextColumn::make('brand_name_raw')
                    ->label('Brand')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('size_raw')
                    ->label('Size'),
                Tables\Columns\TextColumn::make('concentration_raw')
                    ->label('Concentration')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('processing_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'processing' => 'warning',
                        'error' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('validation_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'validated' => 'success',
                        'failed' => 'danger',
                        default => 'warning',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('error_details')
                    ->label('Errors')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->error_details ? json_encode($record->error_details, JSON_PRETTY_PRINT) : null)
                    ->placeholder('None'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Uploaded')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('processing_status')
                    ->options([
                        'new' => 'New',
                        'processing' => 'Processing',
                        'completed' => 'Completed',
                        'error' => 'Error',
                    ]),
                Tables\Filters\SelectFilter::make('import_batch_id')
                    ->label('Batch')
                    ->options(function () {
                        $sellerCode = Auth::user()->seller?->code;

                        return StagingPerfume::where('seller_code_raw', $sellerCode)
                            ->selectRaw('import_batch_id, MAX(created_at) as latest')
                            ->groupBy('import_batch_id')
                            ->orderByDesc('latest')
                            ->limit(10)
                            ->pluck('import_batch_id', 'import_batch_id')
                            ->mapWithKeys(fn ($id) => [$id => substr($id, 0, 8) . '...']);
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStagingPerfumes::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
