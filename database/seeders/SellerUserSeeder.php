<?php

namespace Database\Seeders;

use App\Models\Seller;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SellerUserSeeder extends Seeder
{
    public function run(): void
    {
        $seller = Seller::where('code', 'FRAGNET')->first();

        if (! $seller) {
            return;
        }

        User::updateOrCreate(
            ['email' => 'seller@example.com'],
            [
                'username' => 'FragranceNetSeller',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'seller',
                'seller_id' => $seller->id,
            ]
        );
    }
}
