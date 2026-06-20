<x-dashboard-layout>
    <x-slot name="title">Ajouter un produit</x-slot>

    <x-slot name="sidebar">
        <nav class="nav flex-column">
            <a class="nav-link" href="{{ route('vendeur.dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a class="nav-link" href="{{ route('vendeur.produits.index') }}"><i class="bi bi-box-seam"></i> Mes Produits</a>
            <a class="nav-link active" href="{{ route('vendeur.produits.create') }}"><i class="bi bi-plus-circle"></i> Ajouter Produit</a>
        </nav>
    </x-slot>

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('vendeur.produits.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h2 class="fw-bold mb-0">Ajouter un produit</h2>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('vendeur.produits.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nom du produit *</label>
                            <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror"
                                   value="{{ old('nom') }}" required placeholder="Ex: Huile de palme rouge 5L">
                            @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Prix normal (FCFA) *</label>
                                <input type="number" name="prix" class="form-control @error('prix') is-invalid @enderror"
                                       value="{{ old('prix') }}" required min="0" placeholder="0">
                                @error('prix')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Prix Tontine (FCFA)
                                    <span class="badge bg-success ms-1">-15%</span>
                                </label>
                                <input type="number" name="prix_tontine" class="form-control"
                                       value="{{ old('prix_tontine') }}" min="0"
                                       placeholder="Prix réduit pour achats groupés">
                                <div class="form-text text-success">
                                    <i class="bi bi-people-fill"></i> Prix pour les membres de tontine
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Catégorie</label>
                                <select name="categorie_id" class="form-select">
                                    <option value="">— Choisir une catégorie —</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('categorie_id') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Stock disponible *</label>
                                <input type="number" name="stock" class="form-control @error('stock') is-invalid @enderror"
                                       value="{{ old('stock', 0) }}" required min="0">
                                @error('stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control" rows="3"
                                      placeholder="Décrivez votre produit...">{{ old('description') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-geo-alt"></i> Localisation (marché/quartier)
                            </label>
                            <input type="text" name="localisation" class="form-control"
                                   value="{{ old('localisation') }}" placeholder="Ex: Marché Central Yaoundé, Stand B12">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Photo du produit</label>
                            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror"
                                   accept="image/*" id="imageInput">
                            @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div id="imagePreview" class="mt-2" style="display:none;">
                                <img id="previewImg" src="" class="rounded" style="max-height:200px; object-fit:cover;">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Statut</label>
                            <select name="statut" class="form-select">
                                <option value="actif" {{ old('statut','actif') == 'actif' ? 'selected' : '' }}>✅ Actif — visible sur le marché</option>
                                <option value="inactif" {{ old('statut') == 'inactif' ? 'selected' : '' }}>⏸ Inactif — caché</option>
                            </select>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-check-circle"></i> Enregistrer le produit
                            </button>
                            <a href="{{ route('vendeur.produits.index') }}" class="btn btn-outline-secondary">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-success" style="border-left: 4px solid #16a34a !important;">
                <div class="card-body">
                    <h6 class="fw-bold text-success mb-3">
                        <i class="bi bi-people-fill"></i> Tontines Digitales
                    </h6>
                    <p class="small text-muted mb-2">
                        En proposant un <strong>prix tontine</strong>, vous permettez aux groupes d'acheteurs de commander ensemble à prix réduit.
                    </p>
                    <ul class="small text-muted ps-3 mb-0">
                        <li>Augmente vos ventes en volume</li>
                        <li>Fidélise les communautés</li>
                        <li>Réduit les invendus</li>
                    </ul>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-geo-alt"></i> Géolocalisation</h6>
                    <p class="small text-muted mb-0">
                        Indiquez votre localisation pour que les acheteurs proches vous trouvent facilement.
                    </p>
                </div>
            </div>
        </div>
    </div>

</x-dashboard-layout>

@push('scripts')
<script>
document.getElementById('imageInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (ev) => {
            document.getElementById('previewImg').src = ev.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endpush
