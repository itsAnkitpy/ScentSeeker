<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StagingPerfumeResource\Pages;
use App\Filament\Resources\StagingPerfumeResource\RelationManagers;
use App\Models\StagingPerfume;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StagingPerfumeResource extends Resource
{
    protected static ?string $model = StagingPerfume::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                Forms\Components\TextInput::make('confidence_score')
                    ->numeric(),
                Forms\Components\DateTimePicker::make('imported_at'),
                Forms\Components\DateTimePicker::make('processed_at'),
                Forms\Components\TextInput::make('seller_provided_perfume_id')
                    ->maxLength(255),
                Forms\Components\TextInput::make('perfume_name_raw')
                    ->maxLength(255),
                Forms\Components\TextInput::make('brand_name_raw')
                    ->maxLength(255),
                Forms\Components\TextInput::make('concentration_raw')
                    ->maxLength(255),
                Forms\Components\TextInput::make('size_raw')
                    ->maxLength(255),
                Forms\Components\TextInput::make('gender_raw')
                    ->maxLength(255),
                Forms\Components\Textarea::make('description_raw')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('notes_raw'),
                Forms\Components\FileUpload::make('image_url_raw')
                    ->image(),
                Forms\Components\TextInput::make('seller_product_url_raw')
                    ->maxLength(255),
                Forms\Components\TextInput::make('category_raw')
                    ->maxLength(255),
                Forms\Components\TextInput::make('sku_raw')
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
                Tables\Columns\TextColumn::make('confidence_score')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('imported_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('processed_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('seller_provided_perfume_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('perfume_name_raw')
                    ->searchable(),
                Tables\Columns\TextColumn::make('brand_name_raw')
                    ->searchable(),
                Tables\Columns\TextColumn::make('concentration_raw')
                    ->searchable(),
                Tables\Columns\TextColumn::make('size_raw')
                    ->searchable(),
                Tables\Columns\TextColumn::make('gender_raw')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image_url_raw'),
                Tables\Columns\TextColumn::make('seller_product_url_raw')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category_raw')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sku_raw')
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
            'index' => Pages\ListStagingPerfumes::route('/'),
            'create' => Pages\CreateStagingPerfume::route('/create'),
            'edit' => Pages\EditStagingPerfume::route('/{record}/edit'),
        ];
    }
}
