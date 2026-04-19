<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SellerResource\Pages;
use App\Models\Seller;
use App\Models\User;
use App\Notifications\SellerWelcomeNotification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Password;
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
                        Forms\Components\Select::make('onboarding_status')
                            ->options([
                                'new' => 'New',
                                'active' => 'Active',
                                'suspended' => 'Suspended',
                            ])
                            ->default('new'),
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
                Tables\Columns\IconColumn::make('has_portal_account')
                    ->label('Portal')
                    ->state(fn (Seller $record): bool => $record->users()->where('role', 'seller')->exists())
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),
                Tables\Columns\TextColumn::make('onboarding_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'suspended' => 'danger',
                        default => 'warning',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Actions\Action::make('createPortalAccount')
                    ->label('Create Portal Account')
                    ->icon('heroicon-o-user-plus')
                    ->color('success')
                    ->visible(fn (Seller $record): bool => ! $record->users()->where('role', 'seller')->exists())
                    ->form([
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique('users', 'email'),
                        Forms\Components\TextInput::make('username')
                            ->required()
                            ->unique('users', 'username')
                            ->maxLength(255),
                    ])
                    ->action(function (Seller $record, array $data): void {
                        $user = User::create([
                            'username' => $data['username'],
                            'email' => $data['email'],
                            'password' => bcrypt(Str::random(32)),
                            'email_verified_at' => now(),
                            'role' => 'seller',
                            'seller_id' => $record->id,
                        ]);

                        $token = Password::createToken($user);
                        $resetUrl = url("/reset-password/{$token}?" . http_build_query([
                            'email' => $user->email,
                            'redirect' => '/seller/login',
                        ]));

                        $user->notify(new SellerWelcomeNotification(
                            sellerName: $record->name,
                            resetUrl: $resetUrl,
                        ));

                        $record->update(['onboarding_status' => 'active']);

                        Notification::make()
                            ->title('Portal account created')
                            ->body("Welcome email sent to {$data['email']} with a password reset link.")
                            ->success()
                            ->send();
                    }),
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
