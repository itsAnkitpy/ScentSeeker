<?php

namespace Database\Seeders;

use App\Models\Perfume;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PerfumeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Perfume::create([
            'name' => 'Sauvage Elixir',
            'brand' => 'Dior',
            'description' => 'An extraordinarily concentrated fragrance steeped in the emblematic freshness of Sauvage with an intoxicating heart of Spices, a "tailor-made" Lavender essence and a blend of rich Woods forming the signature of its powerful, lavish and captivating trail.',
            'notes' => json_encode(['top' => ['Cinnamon', 'Nutmeg', 'Cardamom', 'Grapefruit'], 'middle' => ['Lavender'], 'base' => ['Licorice', 'Sandalwood', 'Amber', 'Patchouli', 'Haitian Vetiver']]),
            'image_url' => 'sauvage_elixir.jpg',
            'concentration' => 'Elixir',
            'gender_affinity' => 'Male',
            'launch_year' => 2021,
        ]);

        Perfume::create([
            'name' => 'Bleu de Chanel',
            'brand' => 'Chanel',
            'description' => 'A woody, aromatic fragrance for the man who defies convention. A provocative blend of citrus and woods that liberates the senses. Fresh, clean and profoundly sensual.',
            'notes' => json_encode(['top' => ['Lemon', 'Mint', 'Pink Pepper', 'Grapefruit'], 'middle' => ['Ginger', 'Iso E Super', 'Nutmeg', 'Jasmine'], 'base' => ['Labdanum', 'Sandalwood', 'Patchouli', 'Vetiver', 'Incense', 'Cedar', 'White Musk']]),
            'image_url' => 'bleu_de_chanel.jpg',
            'concentration' => 'Eau de Parfum',
            'gender_affinity' => 'Male',
            'launch_year' => 2014,
        ]);

        Perfume::create([
            'name' => 'Aventus',
            'brand' => 'Creed',
            'description' => 'A sophisticated fruity and rich fragrance with notes of blackcurrant, bergamot, apple and pineapple. The heart is a woody and heady centre complemented by notes of roses, jasmine blossom and patchouli, while a rich base of oak moss, ambergris and a touch of vanilla provides a final flourish to this sophisticated scent.',
            'notes' => json_encode(['top' => ['Pineapple', 'Bergamot', 'Black Currant', 'Apple'], 'middle' => ['Birch', 'Patchouli', 'Moroccan Jasmine', 'Rose'], 'base' => ['Musk', 'Oak moss', 'Ambergris', 'Vanille']]),
            'image_url' => 'aventus.jpg',
            'concentration' => 'Eau de Parfum',
            'gender_affinity' => 'Male',
            'launch_year' => 2010,
        ]);
    }
}
