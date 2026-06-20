<?php

namespace App\Http\Controllers\Livreur;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use App\Models\Livraison;
use Illuminate\Http\Request;

class LivraisonController extends Controller
{
    public function index()
    {
        $livreur = auth()->user();

        // Commandes payées sans livreur assigné = disponibles à prendre
        $disponibles = Commande::with('client')
            ->whereIn('statut', ['payee', 'confirmee'])
            ->whereDoesntHave('livraison')
            ->latest()
            ->take(10)
            ->get();

        $mes_livraisons = $livreur->livraisons()
            ->with('commande.client')
            ->latest()
            ->paginate(15);

        return view('livreur.livraisons.index', compact('disponibles', 'mes_livraisons'));
    }

    public function accepter(Commande $commande)
    {
        abort_if($commande->livraison()->exists(), 409, 'Cette commande a déjà un livreur.');

        Livraison::create([
            'commande_id' => $commande->id,
            'livreur_id'  => auth()->id(),
            'statut'      => 'assignee',
            'commission'  => round($commande->total * 0.10),
        ]);

        $commande->update(['statut' => 'en_livraison']);

        return back()->with('success', 'Livraison acceptée ! Référence : ' . $commande->reference);
    }

    public function livrer(Livraison $livraison)
    {
        abort_unless($livraison->livreur_id === auth()->id(), 403);

        $livraison->update([
            'statut'   => 'livree',
            'livre_le' => now(),
            'bonus_satisfaction' => round($livraison->commission * 0.05),
        ]);

        $livraison->commande->update(['statut' => 'livree']);

        return back()->with('success', 'Livraison marquée comme effectuée !');
    }
}
