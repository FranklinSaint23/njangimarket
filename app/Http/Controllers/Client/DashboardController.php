<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Produit;
use App\Models\Categorie;

class DashboardController extends Controller
{
    public function index()
    {
        $client = auth()->user();

        $stats = [
            'commandes'        => $client->commandes()->count(),
            'commandes_actives'=> $client->commandes()->whereNotIn('statut', ['livree', 'annulee'])->count(),
            'tontines'         => $client->tontines()->count() + $client->cotisations()->distinct('tontine_id')->count(),
        ];

        $produits_recents = Produit::with('vendeur', 'categorie')
            ->actif()
            ->latest()
            ->take(8)
            ->get();

        $categories = Categorie::where('actif', true)->take(6)->get();
        $mes_commandes = $client->commandes()->with('items.produit')->latest()->take(5)->get();

        return view('client.dashboard', compact('stats', 'produits_recents', 'categories', 'mes_commandes'));
    }
}
