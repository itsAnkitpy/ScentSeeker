<?php

namespace App\Filament\Seller\Resources;

use App\Filament\Seller\Resources\PriceResource\Pages;
use App\Models\Price;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PriceResource extends Resource
{
    protected static ?string $model = Price::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-rupee';

    protected static ?string $navigationLabel = 'My Prices';

    protected static ?string $modelLabel = 'Price';

    protected static ?string $pluralModelLabel = 'Prices';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('seller_id', Auth::user()->seller_id);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('perfume.name')
                    ->label('Perfume')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('perfume.brand')
                    ->label('Brand')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('size_ml')
                    ->label('Size (ml)')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('INR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('item_type')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'full_bottle' => 'success',
                        'decant' => 'info',
                        'sample' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('stock_status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'In Stock' => 'success',
                        'Out of Stock' => 'danger',
                        'Pre-order' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('offer_details')
                    ->label('Offer')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('stock_status')
                    ->options([
                        'In Stock' => 'In Stock',
                        'Out of Stock' => 'Out of Stock',
                        'Pre-order' => 'Pre-order',
                    ]),
                Tables\Filters\SelectFilter::make('item_type')
                    ->options([
                        'full_bottle' => 'Full Bottle',
                        'decant' => 'Decant',
                        'sample' => 'Sample',
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrices::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
