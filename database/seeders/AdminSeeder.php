<?php

namespace Database\Seeders;

use App\Models\Categorie;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Admin principal — mot de passe via variable d'environnement
        User::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@njangimarket.cm')],
            [
                'name'     => env('ADMIN_NAME', 'Admin NjangiMarket'),
                'phone'    => env('ADMIN_PHONE', ''),
                'role'     => 'admin',
                'password' => Hash::make(env('ADMIN_PASSWORD', 'changeme_en_prod')),
            ]
        );

        // Catégories de base (nécessaires pour que les vendeurs puissent publier)
        $categories = [
            ['nom' => 'Alimentation',        'slug' => 'alimentation',       'description' => 'Produits alimentaires locaux'],
            ['nom' => 'Légumes & Fruits',     'slug' => 'legumes-fruits',     'description' => 'Produits frais du marché'],
            ['nom' => 'Viandes & Poissons',   'slug' => 'viandes-poissons',   'description' => 'Protéines fraîches'],
            ['nom' => 'Épices & Condiments',  'slug' => 'epices-condiments',  'description' => 'Épices camerounaises'],
            ['nom' => 'Artisanat',            'slug' => 'artisanat',          'description' => 'Produits artisanaux locaux'],
            ['nom' => 'Électronique',         'slug' => 'electronique',       'description' => 'Appareils électroniques'],
        ];

        foreach ($categories as $cat) {
            Categorie::firstOrCreate(
                ['slug' => $cat['slug']],
                array_merge($cat, ['actif' => true])
            );
        }
    }
}
