<?php

namespace App\Http\Controllers\Vendeur;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use App\Models\CommandeItem;
use App\Models\Produit;

class DashboardController extends Controller
{
    public function index()
    {
        $vendeur = auth()->user();

        $produits_ids = $vendeur->produits()->pluck('id');

        $stats = [
            'produits'       => $vendeur->produits()->count(),
            'produits_actifs'=> $vendeur->produits()->where('statut', 'actif')->count(),
            'commandes'      => CommandeItem::whereIn('produit_id', $produits_ids)->distinct('commande_id')->count(),
            'ca_total'       => CommandeItem::whereIn('produit_id', $produits_ids)->sum('sous_total'),
        ];

        $mes_produits = $vendeur->produits()->latest()->take(5)->get();

        return view('vendeur.dashboard', compact('stats', 'mes_produits'));
    }
}
