<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Categorie;
use App\Models\Produit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin + catégories (commun dev & prod)
        $this->call(AdminSeeder::class);

        // ── Données de test uniquement (dev local) ──────────────
        $vendeur = User::create([
            'name'     => 'Marie Vendeur',
            'email'    => 'vendeur@njangimarket.cm',
            'phone'    => '+237 699 000 002',
            'role'     => 'vendeur',
            'adresse'  => 'Marché Central, Yaoundé',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name'     => 'Paul Client',
            'email'    => 'client@njangimarket.cm',
            'phone'    => '+237 699 000 003',
            'role'     => 'client',
            'adresse'  => 'Bastos, Yaoundé',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name'     => 'Jean Livreur',
            'email'    => 'livreur@njangimarket.cm',
            'phone'    => '+237 699 000 004',
            'role'     => 'livreur',
            'password' => Hash::make('password'),
        ]);

        // Produits exemples
        $cat_alim = Categorie::where('slug', 'alimentation')->first();
        $cat_legumes = Categorie::where('slug', 'legumes-fruits')->first();

        $produits = [
            ['nom' => 'Huile de palme rouge 5L', 'prix' => 4500, 'stock' => 20, 'categorie_id' => $cat_alim->id],
            ['nom' => 'Macabo frais (1kg)', 'prix' => 800, 'stock' => 50, 'categorie_id' => $cat_legumes->id],
            ['nom' => 'Plantains mûrs (régime)', 'prix' => 1500, 'stock' => 30, 'categorie_id' => $cat_legumes->id],
            ['nom' => 'Piment vert frais (500g)', 'prix' => 500, 'stock' => 100, 'categorie_id' => $cat_legumes->id],
            ['nom' => 'Farine de manioc (2kg)', 'prix' => 1200, 'stock' => 40, 'categorie_id' => $cat_alim->id],
        ];

        foreach ($produits as $p) {
            Produit::create(array_merge($p, [
                'vendeur_id'  => $vendeur->id,
                'slug'        => Str::slug($p['nom']) . '-' . Str::random(4),
                'description' => 'Produit frais du marché local camerounais.',
                'statut'      => 'actif',
                'prix_tontine'=> $p['prix'] * 0.85,
            ]));
        }
    }
}
