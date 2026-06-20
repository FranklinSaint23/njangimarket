<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Produit;
use Illuminate\Http\Request;

class PanierController extends Controller
{
    public function index()
    {
        $panier = session('panier', []);
        $total = 0;
        $items = [];

        foreach ($panier as $id => $item) {
            $produit = Produit::find($id);
            if ($produit) {
                $sous_total = $produit->prix * $item['quantite'];
                $total += $sous_total;
                $items[] = ['produit' => $produit, 'quantite' => $item['quantite'], 'sous_total' => $sous_total];
            }
        }

        return view('client.panier', compact('items', 'total'));
    }

    public function ajouter(Request $request, Produit $produit)
    {
        if ($produit->statut !== 'actif' || $produit->stock < 1) {
            return back()->with('error', 'Ce produit n\'est pas disponible.');
        }

        $panier = session('panier', []);
        $id = $produit->id;
        $qte = $request->input('quantite', 1);

        $panier[$id] = [
            'quantite' => ($panier[$id]['quantite'] ?? 0) + $qte,
        ];

        if ($panier[$id]['quantite'] > $produit->stock) {
            $panier[$id]['quantite'] = $produit->stock;
        }

        session(['panier' => $panier]);

        return back()->with('success', '"' . $produit->nom . '" ajouté au panier.');
    }

    public function modifier(Request $request, Produit $produit)
    {
        $panier = session('panier', []);
        $qte = (int) $request->input('quantite', 1);

        if ($qte < 1) {
            unset($panier[$produit->id]);
        } else {
            $panier[$produit->id] = ['quantite' => min($qte, $produit->stock)];
        }

        session(['panier' => $panier]);
        return back()->with('success', 'Panier mis à jour.');
    }

    public function supprimer(Produit $produit)
    {
        $panier = session('panier', []);
        unset($panier[$produit->id]);
        session(['panier' => $panier]);

        return back()->with('success', 'Produit retiré du panier.');
    }

    public function vider()
    {
        session()->forget('panier');
        return back()->with('success', 'Panier vidé.');
    }
}
