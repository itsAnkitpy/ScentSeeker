<?php

namespace App\Filament\Seller\Widgets;

use App\Models\Price;
use App\Models\StagingPerfume;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class SellerStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $seller = Auth::user()->seller;

        if (! $seller) {
            return [];
        }

        $activePrices = Price::where('seller_id', $seller->id)
            ->where('stock_status', 'In Stock')
            ->count();

        $totalPrices = Price::where('seller_id', $seller->id)->count();

        $pendingStaged = StagingPerfume::where('seller_code_raw', $seller->code)
            ->where('processing_status', 'new')
            ->count();

        $lastUpload = StagingPerfume::where('seller_code_raw', $seller->code)
            ->latest('created_at')
            ->value('created_at');

        return [
            Stat::make('Active Prices', $activePrices)
                ->description("$totalPrices total")
                ->icon('heroicon-o-currency-rupee'),
            Stat::make('Pending Uploads', $pendingStaged)
                ->description('Awaiting admin processing')
                ->icon('heroicon-o-clock'),
            Stat::make('Last Upload', $lastUpload ? $lastUpload->diffForHumans() : 'Never')
                ->icon('heroicon-o-arrow-up-tray'),
        ];
    }
}
