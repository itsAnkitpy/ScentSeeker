<?php

namespace Database\Seeders;

use App\Models\Seller;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SellerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Seller::create([
            'name' => 'FragranceNet India',
            'logo_url' => 'fragrancenet_logo.png',
            'website_url' => 'https://www.fragrancenet.com/india',
            'rating' => 4.5,
            'contact_info' => 'support@fragrancenet.com',
            'type' => 'official_retailer',
        ]);

        Seller::create([
            'name' => 'PerfumeParadise IN',
            'logo_url' => 'perfumeparadise_logo.png',
            'website_url' => 'https://www.perfumeparadise.in',
            'rating' => 4.2,
            'contact_info' => 'contact@perfumeparadise.in',
            'type' => 'official_retailer',
        ]);

        Seller::create([
            'name' => 'ScentSeller1 (Reddit)',
            'website_url' => 'https://www.reddit.com/user/ScentSeller1',
            'rating' => 4.8,
            'contact_info' => 'PM on Reddit',
            'type' => 'reddit_seller',
        ]);
    }
}
