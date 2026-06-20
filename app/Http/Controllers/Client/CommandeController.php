<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use App\Models\CommandeItem;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommandeController extends Controller
{
    public function index()
    {
        $commandes = auth()->user()->commandes()->with('items.produit')->latest()->paginate(10);
        return view('client.commandes.index', compact('commandes'));
    }

    public function create()
    {
        $panier = session('panier', []);
        if (empty($panier)) {
            return redirect()->route('client.panier.index')
                ->with('error', 'Votre panier est vide.');
        }

        $items = [];
        $total = 0;
        foreach ($panier as $produitId => $item) {
            $quantite = is_array($item) ? ($item['quantite'] ?? 1) : (int) $item;
            $produit = Produit::with('vendeur')->find($produitId);
            if ($produit && $produit->stock >= $quantite) {
                $sous_total = $produit->prix * $quantite;
                $items[] = compact('produit', 'quantite', 'sous_total');
                $total += $sous_total;
            }
        }

        return view('client.commandes.create', compact('items', 'total'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'adresse_livraison' => 'required|string|max:255',
            'methode_paiement'  => 'required|in:orange_money,mtn_money,tontine',
            'phone_paiement'    => 'required_unless:methode_paiement,tontine|nullable|string|max:20',
        ]);

        $panier = session('panier', []);
        if (empty($panier)) {
            return redirect()->route('client.panier.index');
        }

        DB::beginTransaction();
        try {
            $total = 0;
            $itemsData = [];

            foreach ($panier as $produitId => $item) {
                $quantite = is_array($item) ? ($item['quantite'] ?? 1) : (int) $item;
                $produit = Produit::lockForUpdate()->find($produitId);
                if (!$produit || $produit->stock < $quantite) {
                    DB::rollBack();
                    return back()->with('error', "Stock insuffisant pour « {$produit?->nom} ».");
                }
                $sous_total = $produit->prix * $quantite;
                $total += $sous_total;
                $itemsData[] = [
                    'produit_id'    => $produit->id,
                    'quantite'      => $quantite,
                    'prix_unitaire' => $produit->prix,
                    'sous_total'    => $sous_total,
                ];
            }

            $commande = Commande::create([
                'client_id'        => auth()->id(),
                'sous_total'       => $total,
                'total'            => $total,
                'statut'           => 'en_attente',
                'methode_paiement' => $request->methode_paiement,
                'adresse_livraison' => $request->adresse_livraison,
                'note'             => $request->note,
                'notes'            => $request->note,
            ]);

            foreach ($itemsData as $item) {
                $commande->items()->create($item);
                Produit::find($item['produit_id'])->decrement('stock', $item['quantite']);
            }

            DB::commit();

            // Vider le panier
            session()->forget('panier');

            // Rediriger vers paiement Campay
            if ($request->methode_paiement !== 'tontine') {
                return redirect()->route('paiement.initier', $commande)
                    ->with('phone_paiement', $request->phone_paiement);
            }

            return redirect()->route('client.commandes.show', $commande)
                ->with('success', "Commande {$commande->reference} créée. Paiement via tontine en attente.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la commande. Veuillez réessayer.');
        }
    }

    public function show(Commande $commande)
    {
        abort_unless($commande->client_id === auth()->id(), 403);
        $commande->load('items.produit.vendeur', 'livraison');
        return view('client.commandes.show', compact('commande'));
    }

    public function destroy(Commande $commande)
    {
        abort_unless($commande->client_id === auth()->id(), 403);

        if ($commande->statut !== 'en_attente') {
            return back()->with('error', 'Seules les commandes en attente peuvent être supprimées.');
        }

        DB::transaction(function () use ($commande) {
            foreach ($commande->items as $item) {
                Produit::find($item->produit_id)?->increment('stock', $item->quantite);
            }
            $commande->delete();
        });

        return redirect()->route('client.commandes.index')
            ->with('success', "Commande {$commande->reference} annulée et stock restauré.");
    }
}
