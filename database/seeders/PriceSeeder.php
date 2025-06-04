<?php

namespace Database\Seeders;

use App\Models\Price;
use App\Models\Perfume;
use App\Models\Seller;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sauvageElixir = Perfume::where('name', 'Sauvage Elixir')->first();
        $bleuDeChanel = Perfume::where('name', 'Bleu de Chanel')->first();
        $aventus = Perfume::where('name', 'Aventus')->first();

        $fragranceNet = Seller::where('name', 'FragranceNet India')->first();
        $perfumeParadise = Seller::where('name', 'PerfumeParadise IN')->first();
        $redditSeller1 = Seller::where('name', 'ScentSeller1 (Reddit)')->first();

        if ($sauvageElixir && $fragranceNet) {
            Price::create([
                'perfume_id' => $sauvageElixir->id,
                'seller_id' => $fragranceNet->id,
                'price' => 12500.00,
                'currency' => 'INR',
                'stock_status' => 'in_stock',
                'product_url' => 'https://www.fragrancenet.com/india/sauvage-elixir',
                'last_updated' => now(),
                'size_ml' => 60,
                'item_type' => 'full_bottle',
            ]);
        }

        if ($sauvageElixir && $redditSeller1) {
            Price::create([
                'perfume_id' => $sauvageElixir->id,
                'seller_id' => $redditSeller1->id,
                'price' => 11800.00,
                'currency' => 'INR',
                'stock_status' => 'in_stock',
                'product_url' => 'https://www.reddit.com/user/ScentSeller1/posts/sauvage_elixir_60ml',
                'last_updated' => now(),
                'size_ml' => 60,
                'item_type' => 'full_bottle',
            ]);
        }

        if ($bleuDeChanel && $perfumeParadise) {
            Price::create([
                'perfume_id' => $bleuDeChanel->id,
                'seller_id' => $perfumeParadise->id,
                'price' => 9800.00,
                'currency' => 'INR',
                'stock_status' => 'in_stock',
                'product_url' => 'https://www.perfumeparadise.in/bleu-de-chanel-edp',
                'last_updated' => now(),
                'size_ml' => 100,
                'item_type' => 'full_bottle',
            ]);
        }
        
        if ($aventus && $fragranceNet) {
            Price::create([
                'perfume_id' => $aventus->id,
                'seller_id' => $fragranceNet->id,
                'price' => 22000.00,
                'currency' => 'INR',
                'stock_status' => 'out_of_stock',
                'product_url' => 'https://www.fragrancenet.com/india/creed-aventus',
                'last_updated' => now()->subDays(2),
                'size_ml' => 100,
                'item_type' => 'full_bottle',
            ]);
        }

        if ($aventus && $redditSeller1) {
             Price::create([
                'perfume_id' => $aventus->id,
                'seller_id' => $redditSeller1->id,
                'price' => 2500.00, // Assuming decant price
                'currency' => 'INR',
                'stock_status' => 'in_stock',
                'product_url' => 'https://www.reddit.com/user/ScentSeller1/posts/aventus_10ml_decant',
                'last_updated' => now(),
                'size_ml' => 10,
                'item_type' => 'decant',
            ]);
        }
    }
}
