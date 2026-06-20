<?php

namespace App\Http\Controllers\Livreur;

use App\Http\Controllers\Controller;
use App\Models\Livraison;

class DashboardController extends Controller
{
    public function index()
    {
        $livreur = auth()->user();

        $stats = [
            'livraisons_total'     => $livreur->livraisons()->count(),
            'livraisons_en_cours'  => $livreur->livraisons()->where('statut', 'en_route')->count(),
            'livraisons_terminees' => $livreur->livraisons()->where('statut', 'livree')->count(),
            'commission_totale'    => $livreur->livraisons()->sum('commission'),
            'bonus_total'          => $livreur->livraisons()->sum('bonus_satisfaction'),
        ];

        $livraisons_en_cours = $livreur->livraisons()
            ->with('commande.client')
            ->whereIn('statut', ['assignee', 'en_route'])
            ->latest()
            ->get();

        $historique = $livreur->livraisons()
            ->with('commande.client')
            ->where('statut', 'livree')
            ->latest()
            ->take(10)
            ->get();

        return view('livreur.dashboard', compact('stats', 'livraisons_en_cours', 'historique'));
    }
}
