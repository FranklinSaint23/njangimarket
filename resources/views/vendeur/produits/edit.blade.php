<x-dashboard-layout>
    <x-slot name="title">Modifier {{ $produit->nom }}</x-slot>

    <x-slot name="sidebar">
        <nav class="nav flex-column">
            <a class="nav-link" href="{{ route('vendeur.dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a class="nav-link active" href="{{ route('vendeur.produits.index') }}"><i class="bi bi-box-seam"></i> Mes Produits</a>
            <a class="nav-link" href="{{ route('vendeur.produits.create') }}"><i class="bi bi-plus-circle"></i> Ajouter Produit</a>
        </nav>
    </x-slot>

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('vendeur.produits.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h2 class="fw-bold mb-0">Modifier le produit</h2>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('vendeur.produits.update', $produit) }}" enctype="multipart/form-data">
                        @csrf @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nom du produit *</label>
                            <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror"
                                   value="{{ old('nom', $produit->nom) }}" required>
                            @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Prix normal (FCFA) *</label>
                                <input type="number" name="prix" class="form-control @error('prix') is-invalid @enderror"
                                       value="{{ old('prix', $produit->prix) }}" required min="0">
                                @error('prix')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Prix Tontine (FCFA)</label>
                                <input type="number" name="prix_tontine" class="form-control"
                                       value="{{ old('prix_tontine', $produit->prix_tontine) }}" min="0">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Catégorie</label>
                                <select name="categorie_id" class="form-select">
                                    <option value="">— Aucune catégorie —</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}"
                                            {{ old('categorie_id', $produit->categorie_id) == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Stock</label>
                                <input type="number" name="stock" class="form-control"
                                       value="{{ old('stock', $produit->stock) }}" required min="0">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description', $produit->description) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Localisation</label>
                            <input type="text" name="localisation" class="form-control"
                                   value="{{ old('localisation', $produit->localisation) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nouvelle photo (optionnel)</label>
                            @if($produit->image_principale)
                                <div class="mb-2">
                                    <img src="{{ Storage::url($produit->image_principale) }}" class="rounded"
                                         style="max-height:120px; object-fit:cover;">
                                    <small class="text-muted ms-2">Photo actuelle</small>
                                </div>
                            @endif
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Statut</label>
                            <select name="statut" class="form-select">
                                @foreach(['actif' => '✅ Actif', 'inactif' => '⏸ Inactif', 'rupture' => '❌ Rupture de stock'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('statut', $produit->statut) == $val ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-check-circle"></i> Enregistrer
                            </button>
                            <a href="{{ route('vendeur.produits.index') }}" class="btn btn-outline-secondary">Annuler</a>
                            <form method="POST" action="{{ route('vendeur.produits.destroy', $produit) }}" class="ms-auto">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger"
                                        onclick="return confirm('Supprimer ce produit ?')">
                                    <i class="bi bi-trash"></i> Supprimer
                                </button>
                            </form>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-dashboard-layout>
