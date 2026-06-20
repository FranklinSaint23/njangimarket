<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Produit;
use App\Models\Commande;
use App\Models\Tontine;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'utilisateurs' => User::count(),
            'vendeurs'     => User::where('role', 'vendeur')->count(),
            'clients'      => User::where('role', 'client')->count(),
            'livreurs'     => User::where('role', 'livreur')->count(),
            'produits'     => Produit::count(),
            'commandes'    => Commande::count(),
            'ca_total'     => Commande::where('statut_paiement', 'paye')->sum('total'),
            'tontines'     => Tontine::count(),
        ];

        $dernieres_commandes = Commande::with('client')->latest()->take(10)->get();
        $derniers_users = User::latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'dernieres_commandes', 'derniers_users'));
    }
}
