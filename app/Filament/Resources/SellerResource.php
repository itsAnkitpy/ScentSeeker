<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SellerResource\Pages;
use App\Models\Seller;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class SellerResource extends Resource
{
    protected static ?string $model = Seller::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationGroup = 'Data Management';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Seller Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state, ?string $old) {
                                // Only auto-generate if code is empty or was auto-generated from old name
                                $currentCode = $get('code');
                                $oldAutoCode = $old ? self::generateCodeFromName($old) : null;

                                if (empty($currentCode) || $currentCode === $oldAutoCode) {
                                    $set('code', self::generateCodeFromName($state));
                                }
                            }),
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->maxLength(20)
                            ->unique(ignoreRecord: true)
                            ->helperText('Auto-generated from name. You can customize it.')
                            ->rules(['alpha_dash'])
                            ->validationMessages([
                                'unique' => 'This code is already in use by another seller.',
                                'alpha_dash' => 'Code can only contain letters, numbers, dashes and underscores.',
                            ]),
                        Forms\Components\TextInput::make('website_url')
                            ->label('Website URL')
                            ->url()
                            ->maxLength(255),
                        Forms\Components\Select::make('type')
                            ->options([
                                'official_retailer' => 'Official Retailer',
                                'marketplace' => 'Marketplace',
                                'discount_store' => 'Discount Store',
                                'reddit_seller' => 'Reddit Seller',
                                'other' => 'Other',
                            ])
                            ->default('official_retailer'),
                    ])->columns(2),

                Forms\Components\Section::make('Additional Details')
                    ->schema([
                        Forms\Components\TextInput::make('logo_url')
                            ->label('Logo URL')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('rating')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(5)
                            ->step(0.1),
                        Forms\Components\Textarea::make('contact_info')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    /**
     * Generate a code from the seller name.
     * Example: "Fragrance Net India" -> "FNI001"
     */
    protected static function generateCodeFromName(?string $name): string
    {
        if (empty($name)) {
            return '';
        }

        // Get initials from the name (first letter of each word)
        $words = preg_split('/\s+/', trim($name));
        $initials = '';
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper(substr($word, 0, 1));
            }
        }

        // Limit to 4 characters
        $initials = substr($initials, 0, 4);

        // If too short, pad with letters from the name
        if (strlen($initials) < 2 && strlen($name) >= 2) {
            $initials = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $name), 0, 3));
        }

        // Find a unique code by appending numbers
        $baseCode = $initials;
        $code = $baseCode;
        $counter = 1;

        while (Seller::where('code', $code)->exists()) {
            $code = $baseCode . str_pad($counter, 3, '0', STR_PAD_LEFT);
            $counter++;
        }

        return $code;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->badge()
                    ->color('info')
                    ->searchable(),
                Tables\Columns\TextColumn::make('website_url')
                    ->label('Website')
                    ->limit(30)
                    ->url(fn($record) => $record->website_url, true)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('rating')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'official_retailer' => 'success',
                        'marketplace' => 'warning',
                        'discount_store' => 'info',
                        'reddit_seller' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('prices_count')
                    ->label('Prices')
                    ->counts('prices')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'official_retailer' => 'Official Retailer',
                        'marketplace' => 'Marketplace',
                        'discount_store' => 'Discount Store',
                        'reddit_seller' => 'Reddit Seller',
                    ]),
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
            'index' => Pages\ListSellers::route('/'),
            'create' => Pages\CreateSeller::route('/create'),
            'edit' => Pages\EditSeller::route('/{record}/edit'),
        ];
    }
}
