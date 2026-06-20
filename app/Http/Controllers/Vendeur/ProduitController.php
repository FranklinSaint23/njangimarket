<?php

namespace App\Http\Controllers\Vendeur;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProduitController extends Controller
{
    public function index()
    {
        $produits = auth()->user()->produits()->with('categorie')->latest()->paginate(10);
        return view('vendeur.produits.index', compact('produits'));
    }

    public function create()
    {
        $categories = Categorie::where('actif', true)->get();
        return view('vendeur.produits.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom'          => 'required|string|max:255',
            'categorie_id' => 'nullable|exists:categories,id',
            'description'  => 'nullable|string',
            'prix'         => 'required|numeric|min:0',
            'prix_tontine' => 'nullable|numeric|min:0',
            'stock'        => 'required|integer|min:0',
            'localisation' => 'nullable|string|max:255',
            'latitude'     => 'nullable|numeric',
            'longitude'    => 'nullable|numeric',
            'statut'       => 'required|in:actif,inactif',
            'image'        => 'nullable|image|max:2048',
        ]);

        $slug = Str::slug($data['nom']) . '-' . Str::random(6);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('produits', 'public');
        }

        auth()->user()->produits()->create([
            ...$data,
            'slug'             => $slug,
            'image_principale' => $imagePath,
        ]);

        return redirect()->route('vendeur.produits.index')
            ->with('success', 'Produit ajouté avec succès !');
    }

    public function show(Produit $produit)
    {
        $this->authorize_owner($produit);
        return view('vendeur.produits.show', compact('produit'));
    }

    public function edit(Produit $produit)
    {
        $this->authorize_owner($produit);
        $categories = Categorie::where('actif', true)->get();
        return view('vendeur.produits.edit', compact('produit', 'categories'));
    }

    public function update(Request $request, Produit $produit)
    {
        $this->authorize_owner($produit);

        $data = $request->validate([
            'nom'          => 'required|string|max:255',
            'categorie_id' => 'nullable|exists:categories,id',
            'description'  => 'nullable|string',
            'prix'         => 'required|numeric|min:0',
            'prix_tontine' => 'nullable|numeric|min:0',
            'stock'        => 'required|integer|min:0',
            'localisation' => 'nullable|string|max:255',
            'statut'       => 'required|in:actif,inactif,rupture',
            'image'        => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($produit->image_principale) {
                Storage::disk('public')->delete($produit->image_principale);
            }
            $data['image_principale'] = $request->file('image')->store('produits', 'public');
        }

        $produit->update($data);

        return redirect()->route('vendeur.produits.index')
            ->with('success', 'Produit mis à jour.');
    }

    public function destroy(Produit $produit)
    {
        $this->authorize_owner($produit);

        if ($produit->image_principale) {
            Storage::disk('public')->delete($produit->image_principale);
        }

        $produit->delete();

        return redirect()->route('vendeur.produits.index')
            ->with('success', 'Produit supprimé.');
    }

    private function authorize_owner(Produit $produit): void
    {
        if ($produit->vendeur_id !== auth()->id()) {
            abort(403);
        }
    }
}
