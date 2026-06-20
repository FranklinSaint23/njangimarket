<?php

namespace App\Http\Controllers\Vendeur;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use App\Models\CommandeItem;
use Illuminate\Http\Request;

class CommandeController extends Controller
{
    public function index(Request $request)
    {
        $vendeur = auth()->user();
        $produitIds = $vendeur->produits()->pluck('id');

        // Commandes contenant au moins un produit du vendeur
        $query = Commande::with(['client', 'items.produit'])
            ->whereHas('items', fn($q) => $q->whereIn('produit_id', $produitIds));

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $commandes = $query->latest()->paginate(15)->withQueryString();

        return view('vendeur.commandes.index', compact('commandes'));
    }

    public function show(Commande $commande)
    {
        $vendeur = auth()->user();
        $produitIds = $vendeur->produits()->pluck('id');

        abort_unless(
            $commande->items()->whereIn('produit_id', $produitIds)->exists(),
            403
        );

        $commande->load(['client', 'items.produit', 'livraison.livreur']);
        $mes_items = $commande->items->whereIn('produit_id', $produitIds->toArray());

        return view('vendeur.commandes.show', compact('commande', 'mes_items'));
    }

    public function updateStatut(Request $request, Commande $commande)
    {
        $request->validate([
            'statut' => 'required|in:confirmee,en_preparation,en_livraison,annulee',
        ]);

        $vendeur = auth()->user();
        $produitIds = $vendeur->produits()->pluck('id');
        abort_unless($commande->items()->whereIn('produit_id', $produitIds)->exists(), 403);

        $commande->update(['statut' => $request->statut]);

        return back()->with('success', 'Statut de la commande mis à jour.');
    }
}
