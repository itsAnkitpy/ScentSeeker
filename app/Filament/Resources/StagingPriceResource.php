<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StagingPriceResource\Pages;
use App\Filament\Resources\StagingPriceResource\RelationManagers;
use App\Models\StagingPrice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StagingPriceResource extends Resource
{
    protected static ?string $model = StagingPrice::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-arrow-down';

    protected static ?string $navigationGroup = 'Data Import';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('import_batch_id')
                    ->maxLength(36),
                Forms\Components\TextInput::make('source_identifier')
                    ->maxLength(255),
                Forms\Components\TextInput::make('raw_data_payload'),
                Forms\Components\TextInput::make('validation_status')
                    ->maxLength(255)
                    ->default('pending'),
                Forms\Components\TextInput::make('processing_status')
                    ->maxLength(255)
                    ->default('new'),
                Forms\Components\TextInput::make('error_details'),
                Forms\Components\TextInput::make('is_duplicate_of_staged_id')
                    ->numeric(),
                Forms\Components\TextInput::make('matched_production_perfume_id')
                    ->numeric(),
                Forms\Components\TextInput::make('matched_production_price_id')
                    ->numeric(),
                Forms\Components\TextInput::make('confidence_score')
                    ->numeric(),
                Forms\Components\DateTimePicker::make('imported_at'),
                Forms\Components\DateTimePicker::make('processed_at'),
                Forms\Components\TextInput::make('staged_perfume_identifier')
                    ->maxLength(255),
                Forms\Components\TextInput::make('price_raw')
                    ->numeric(),
                Forms\Components\TextInput::make('currency_raw')
                    ->maxLength(255),
                Forms\Components\TextInput::make('discount_price_raw')
                    ->numeric(),
                Forms\Components\TextInput::make('availability_raw')
                    ->maxLength(255),
                Forms\Components\TextInput::make('seller_specific_price_id')
                    ->maxLength(255),
                Forms\Components\TextInput::make('seller_code_raw')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('import_batch_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('source_identifier')
                    ->searchable(),
                Tables\Columns\TextColumn::make('validation_status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('processing_status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('is_duplicate_of_staged_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('matched_production_perfume_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('matched_production_price_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('confidence_score')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('imported_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('processed_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('staged_perfume_identifier')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price_raw')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('currency_raw')
                    ->searchable(),
                Tables\Columns\TextColumn::make('discount_price_raw')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('availability_raw')
                    ->searchable(),
                Tables\Columns\TextColumn::make('seller_specific_price_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('seller_code_raw')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
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
            'index' => Pages\ListStagingPrices::route('/'),
            'create' => Pages\CreateStagingPrice::route('/create'),
            'edit' => Pages\EditStagingPrice::route('/{record}/edit'),
        ];
    }
}
