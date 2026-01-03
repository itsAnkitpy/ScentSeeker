<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PerfumeResource\Pages;
use App\Filament\Resources\PerfumeResource\RelationManagers;
use App\Models\Perfume;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PerfumeResource extends Resource
{
    protected static ?string $model = Perfume::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('brand')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('notes'),
                Forms\Components\FileUpload::make('image_url')
                    ->image(),
                Forms\Components\TextInput::make('concentration')
                    ->maxLength(255),
                Forms\Components\TextInput::make('gender_affinity')
                    ->maxLength(255),
                Forms\Components\TextInput::make('launch_year'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('brand')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image_url'),
                Tables\Columns\TextColumn::make('concentration')
                    ->searchable(),
                Tables\Columns\TextColumn::make('gender_affinity')
                    ->searchable(),
                Tables\Columns\TextColumn::make('launch_year'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListPerfumes::route('/'),
            'create' => Pages\CreatePerfume::route('/create'),
            'edit' => Pages\EditPerfume::route('/{record}/edit'),
        ];
    }
}
