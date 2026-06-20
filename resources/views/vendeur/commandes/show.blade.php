<x-dashboard-layout>
<x-slot name="title">Commande {{ $commande->reference }}</x-slot>

<x-slot name="sidebar">
  <a class="nav-link" href="{{ route('vendeur.dashboard') }}"><i class="bi bi-speedometer2"></i>Dashboard</a>
  <div class="sidebar-section-label">Commerce</div>
  <a class="nav-link active" href="{{ route('vendeur.commandes.index') }}"><i class="bi bi-receipt-cutoff"></i>Commandes reçues</a>
  <a class="nav-link" href="{{ route('vendeur.produits.index') }}"><i class="bi bi-box-seam"></i>Mes Produits</a>
</x-slot>

<div class="page-header d-flex justify-content-between align-items-start">
  <div>
    <h1 class="page-title">{{ $commande->reference }}</h1>
    <p class="page-subtitle">{{ $commande->created_at->format('d M Y à H:i') }}</p>
  </div>
  <span class="status-badge status-{{ $commande->statut }}" style="padding:.5rem 1rem;font-size:.85rem;">
    {{ str_replace('_',' ',ucfirst($commande->statut)) }}
  </span>
</div>

@if(session('success'))
  <div class="alert alert-success mb-4 d-flex align-items-center gap-2">
    <i class="bi bi-check-circle-fill"></i>{{ session('success') }}
  </div>
@endif

<div class="row g-4">
  <div class="col-lg-8">
    {{-- Vos articles dans cette commande --}}
    <div class="card mb-4">
      <div class="card-header"><i class="bi bi-box-seam me-2 text-primary"></i>Vos articles commandés</div>
      <div class="card-body p-0">
        @foreach($mes_items as $item)
        <div class="d-flex align-items-center gap-3 p-4 {{ !$loop->last ? 'border-bottom' : '' }}">
          @if($item->produit?->image_principale)
            <img src="{{ Storage::url($item->produit->image_principale) }}" width="56" height="56"
                 style="border-radius:.75rem;object-fit:cover;flex-shrink:0;">
          @else
            <div style="width:56px;height:56px;border-radius:.75rem;background:#f1f5f9;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
              <i class="bi bi-image text-secondary"></i>
            </div>
          @endif
          <div class="flex-grow-1">
            <div class="fw-600">{{ $item->produit?->nom ?? 'Produit supprimé' }}</div>
            <div class="fs-xs text-secondary">{{ number_format($item->prix_unitaire) }} FCFA × {{ $item->quantite }}</div>
          </div>
          <div class="fw-700 text-success">{{ number_format($item->sous_total) }} FCFA</div>
        </div>
        @endforeach
      </div>
    </div>

    {{-- Changer statut --}}
    @if(!in_array($commande->statut, ['livree','annulee']))
    <div class="card">
      <div class="card-header"><i class="bi bi-arrow-repeat me-2 text-primary"></i>Changer le statut</div>
      <div class="card-body">
        <form method="POST" action="{{ route('vendeur.commandes.statut', $commande) }}" class="d-flex gap-2 flex-wrap">
          @csrf @method('PATCH')
          <select name="statut" class="form-select" style="width:auto;">
            @foreach(['confirmee','en_preparation','en_livraison','annulee'] as $s)
              <option value="{{ $s }}" {{ $commande->statut===$s ? 'selected' : '' }}>
                {{ str_replace('_',' ',ucfirst($s)) }}
              </option>
            @endforeach
          </select>
          <button type="submit" class="btn btn-primary fw-600">Mettre à jour</button>
        </form>
      </div>
    </div>
    @endif
  </div>

  <div class="col-lg-4 d-flex flex-column gap-3">
    {{-- Infos client --}}
    <div class="card">
      <div class="card-header"><i class="bi bi-person me-2 text-primary"></i>Client</div>
      <div class="card-body">
        <div class="d-flex align-items-center gap-3 mb-3">
          <div class="avatar" style="background:#16a34a;">{{ strtoupper(substr($commande->client->name,0,1)) }}</div>
          <div>
            <div class="fw-600">{{ $commande->client->name }}</div>
            <div class="fs-xs text-secondary">{{ $commande->client->phone ?? $commande->client->email }}</div>
          </div>
        </div>
        <div class="fs-sm">
          <div class="text-secondary mb-1">Adresse de livraison</div>
          <div class="fw-600">{{ $commande->adresse_livraison }}</div>
        </div>
      </div>
    </div>

    {{-- Résumé --}}
    <div class="card">
      <div class="card-header"><i class="bi bi-receipt me-2 text-primary"></i>Résumé</div>
      <div class="card-body">
        @php $pm=['orange_money'=>['#ea580c','Orange Money'],'mtn_money'=>['#ca8a04','MTN MoMo'],'tontine'=>['#7c3aed','Tontine']]; [$pc,$pl]=$pm[$commande->methode_paiement]??['#64748b','—']; @endphp
        <div class="d-flex justify-content-between mb-2 fs-sm">
          <span class="text-secondary">Paiement</span>
          <span class="fw-600" style="color:{{ $pc }}">{{ $pl }}</span>
        </div>
        <div class="d-flex justify-content-between mb-2 fs-sm">
          <span class="text-secondary">Vos articles</span>
          <span class="fw-600">{{ number_format($mes_items->sum('sous_total')) }} FCFA</span>
        </div>
        <hr>
        <div class="d-flex justify-content-between">
          <span class="fw-700">Total commande</span>
          <span class="fw-700 text-success">{{ number_format($commande->total) }} FCFA</span>
        </div>
      </div>
    </div>

    <a href="{{ route('vendeur.commandes.index') }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left me-1"></i>Retour aux commandes
    </a>
  </div>
</div>
</x-dashboard-layout>
