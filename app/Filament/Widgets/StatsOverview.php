<?php

namespace App\Filament\Widgets;

use App\Models\Perfume;
use App\Models\Price;
use App\Models\Seller;
use App\Models\StagingPerfume;
use App\Models\StagingPrice;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Perfumes', Perfume::count())
                ->description('Active perfumes in catalog')
                ->color('success')
                ->icon('heroicon-o-sparkles'),

            Stat::make('Total Sellers', Seller::count())
                ->description('Registered sellers')
                ->color('info')
                ->icon('heroicon-o-building-storefront'),

            Stat::make('Price Entries', Price::count())
                ->description('Tracked price points')
                ->color('warning')
                ->icon('heroicon-o-currency-rupee'),

            Stat::make('Pending Staging', StagingPerfume::where('processing_status', 'pending')->count() + StagingPrice::where('processing_status', 'pending')->count())
                ->description('Items awaiting review')
                ->color('danger')
                ->icon('heroicon-o-clock'),
        ];
    }
}
