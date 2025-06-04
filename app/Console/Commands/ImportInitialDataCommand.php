<?php

namespace App\Console\Commands;

use App\Models\Perfume;
use App\Models\Seller;
use App\Models\Price;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportInitialDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:initial-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports initial set of perfumes, sellers, and prices into the database.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting initial data import...');

        DB::transaction(function () {
            // Perfumes
            $this->info('Importing perfumes...');
            $laNuit = Perfume::updateOrCreate(
                ['name' => 'La Nuit de L\'Homme', 'brand' => 'Yves Saint Laurent'],
                [
                    'description' => 'A story of intensity, bold sensuality, and seduction that lies half-way between restraint and abandon.',
                    'notes' => json_encode(['top' => ['Cardamom'], 'middle' => ['Lavender', 'Virginia Cedar', 'Bergamot'], 'base' => ['Vetiver', 'Caraway']]),
                    'image_url' => 'la_nuit.jpg',
                    'concentration' => 'Eau de Toilette',
                    'gender_affinity' => 'Male',
                    'launch_year' => 2009,
                ]
            );

            $blackOrchid = Perfume::updateOrCreate(
                ['name' => 'Black Orchid', 'brand' => 'Tom Ford'],
                [
                    'description' => 'A luxurious and sensual fragrance of rich, dark accords and an alluring potion of black orchids and spice.',
                    'notes' => json_encode(['top' => ['Truffle', 'Gardenia', 'Black Currant', 'Ylang-Ylang', 'Jasmine', 'Bergamot', 'Mandarin Orange', 'Amalfi Lemon'], 'middle' => ['Orchid', 'Spices', 'Gardenia', 'Fruity Notes', 'Ylang-Ylang', 'Jasmine', 'Lotus'], 'base' => ['Mexican chocolate', 'Patchouli', 'Vanille', 'Incense', 'Amber', 'Sandalwood', 'Vetiver', 'White Musk']]),
                    'image_url' => 'black_orchid.jpg',
                    'concentration' => 'Eau de Parfum',
                    'gender_affinity' => 'Unisex',
                    'launch_year' => 2006,
                ]
            );
            $this->info('Perfumes imported.');

            // Sellers
            $this->info('Importing sellers...');
            $sellerNykaa = Seller::updateOrCreate(
                ['name' => 'Nykaa Man'],
                [
                    'logo_url' => 'nykaa_man_logo.png',
                    'website_url' => 'https://www.nykaaman.com',
                    'rating' => 4.6,
                    'contact_info' => 'support@nykaaman.com',
                    'type' => 'official_retailer',
                ]
            );

            $sellerTataCliq = Seller::updateOrCreate(
                ['name' => 'Tata CLiQ Luxury'],
                [
                    'logo_url' => 'tatacliq_luxury_logo.png',
                    'website_url' => 'https://luxury.tatacliq.com',
                    'rating' => 4.4,
                    'contact_info' => 'luxury@tatacliq.com',
                    'type' => 'official_retailer',
                ]
            );
            $this->info('Sellers imported.');

            // Prices
            $this->info('Importing prices...');
            if ($laNuit && $sellerNykaa) {
                Price::updateOrCreate(
                    ['perfume_id' => $laNuit->id, 'seller_id' => $sellerNykaa->id, 'size_ml' => 100, 'item_type' => 'full_bottle'],
                    [
                        'price' => 7500.00,
                        'currency' => 'INR',
                        'stock_status' => 'in_stock',
                        'product_url' => 'https://www.nykaaman.com/yves-saint-laurent-la-nuit-de-l-homme-edt',
                        'last_updated' => now(),
                    ]
                );
            }

            if ($blackOrchid && $sellerTataCliq) {
                Price::updateOrCreate(
                    ['perfume_id' => $blackOrchid->id, 'seller_id' => $sellerTataCliq->id, 'size_ml' => 50, 'item_type' => 'full_bottle'],
                    [
                        'price' => 9900.00,
                        'currency' => 'INR',
                        'stock_status' => 'in_stock',
                        'product_url' => 'https://luxury.tatacliq.com/tom-ford-black-orchid-edp-50ml',
                        'last_updated' => now(),
                    ]
                );
            }
            $this->info('Prices imported.');
        });

        $this->info('Initial data import completed successfully!');
        return Command::SUCCESS;
    }
}
