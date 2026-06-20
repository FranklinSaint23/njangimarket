<x-dashboard-layout>
<x-slot name="title">Marketplace</x-slot>

<x-slot name="sidebar">
  <a class="nav-link active" href="{{ route('client.dashboard') }}"><i class="bi bi-house"></i>Accueil</a>
  <a class="nav-link" href="{{ route('client.panier.index') }}">
    <i class="bi bi-cart3"></i>Mon Panier
    @php $nb=count(session('panier',[])); @endphp
    @if($nb)<span class="badge ms-auto" style="background:#ef4444;color:#fff;">{{ $nb }}</span>@endif
  </a>
  <a class="nav-link" href="{{ route('client.commandes.index') }}"><i class="bi bi-receipt-cutoff"></i>Mes Commandes</a>
  <a class="nav-link" href="{{ route('tontines.index') }}"><i class="bi bi-people-fill"></i>Tontines Njangi</a>
  <div class="sidebar-section-label">Compte</div>
  <a class="nav-link" href="{{ route('profile.edit') }}"><i class="bi bi-person-circle"></i>Mon Profil</a>
</x-slot>

{{-- Header --}}
<div class="page-header d-flex justify-content-between align-items-start">
  <div>
    <h1 class="page-title">Bonjour, {{ auth()->user()->name }} 👋</h1>
    <p class="page-subtitle">Découvrez les produits du marché local</p>
  </div>
  <a href="{{ route('client.panier.index') }}" class="btn btn-primary">
    <i class="bi bi-cart3 me-1"></i>Mon Panier
    @if($nb)<span class="badge bg-white text-primary ms-1">{{ $nb }}</span>@endif
  </a>
</div>

{{-- Stats rapides --}}
<div class="row g-3 mb-4">
  @foreach([
    ['Commandes totales', $stats['commandes'],        'bi-receipt-cutoff', '#6366f1','#eef2ff'],
    ['En cours',          $stats['commandes_actives'],'bi-truck',          '#f59e0b','#fffbeb'],
    ['Mes tontines',      $stats['tontines'],         'bi-people-fill',    '#16a34a','#f0fdf4'],
  ] as [$l,$v,$i,$c,$bg])
  <div class="col-4">
    <div class="stat-card">
      <div class="stat-icon" style="background:{{ $bg }};color:{{ $c }};"><i class="bi {{ $i }}"></i></div>
      <div><div class="stat-value">{{ $v }}</div><div class="stat-label">{{ $l }}</div></div>
    </div>
  </div>
  @endforeach
</div>

{{-- Catégories rapides --}}
@if($categories->count())
<div class="mb-4">
  <div class="d-flex gap-2 flex-wrap">
    <button class="btn btn-primary btn-sm rounded-pill">Tout</button>
    @foreach($categories as $cat)
    <button class="btn btn-outline-secondary btn-sm rounded-pill">{{ $cat->nom }}</button>
    @endforeach
  </div>
</div>
@endif

{{-- Produits --}}
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2 style="font-size:1.1rem;font-weight:700;">Produits disponibles</h2>
  <div class="input-group" style="max-width:260px;">
    <span class="input-group-text bg-white"><i class="bi bi-search fs-sm text-secondary"></i></span>
    <input type="text" class="form-control" placeholder="Rechercher..." id="searchInput">
  </div>
</div>

@if($produits_recents->count())
<div class="row g-3 mb-4" id="productsGrid">
  @foreach($produits_recents as $produit)
  <div class="col-6 col-md-4 col-xl-3 product-item">
    <div class="product-card h-100">
      @if($produit->image_principale)
        <img src="{{ Storage::url($produit->image_principale) }}" class="product-card-img" alt="{{ $produit->nom }}">
      @else
        <div class="product-card-img-placeholder"><i class="bi bi-image"></i></div>
      @endif

      {{-- Badges --}}
      <div style="position:absolute;top:.625rem;left:.625rem;display:flex;gap:.35rem;">
        @if($produit->prix_tontine)
          <span class="badge" style="background:#7c3aed;color:#fff;font-size:.65rem;">
            <i class="bi bi-people-fill"></i> Tontine
          </span>
        @endif
        @if($produit->localisation)
          <span class="badge" style="background:rgba(0,0,0,.5);color:#fff;font-size:.65rem;">
            <i class="bi bi-geo-alt"></i>
          </span>
        @endif
      </div>

      <div class="product-card-body">
        <div class="product-card-name">{{ $produit->nom }}</div>
        <div class="product-card-price">{{ number_format($produit->prix) }} FCFA</div>
        @if($produit->prix_tontine)
          <div class="product-card-tontine">
            <i class="bi bi-people-fill"></i> {{ number_format($produit->prix_tontine) }} FCFA (tontine)
          </div>
        @endif
        <div class="product-card-seller mt-1">
          <i class="bi bi-shop"></i> {{ $produit->vendeur->name }}
          @if($produit->localisation) · <i class="bi bi-geo-alt"></i> {{ Str::limit($produit->localisation,25) }} @endif
        </div>

        <form method="POST" action="{{ route('client.panier.ajouter', $produit) }}" class="mt-2">
          @csrf
          <button type="submit" class="btn btn-primary w-100 btn-sm">
            <i class="bi bi-cart-plus me-1"></i>Ajouter au panier
          </button>
        </form>
      </div>
    </div>
  </div>
  @endforeach
</div>
@else
<div class="empty-state">
  <i class="bi bi-shop"></i>
  <h5>Aucun produit disponible</h5>
  <p>Les vendeurs n'ont pas encore publié de produits.</p>
</div>
@endif

{{-- Tontine promo --}}
<div class="card border-0 mb-4" style="background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;">
  <div class="card-body p-4 d-flex align-items-center gap-4">
    <div style="font-size:3rem;flex-shrink:0;">🤝</div>
    <div>
      <h5 class="fw-700 mb-1">Rejoignez une Tontine Njangi</h5>
      <p class="mb-3 opacity-75 fs-sm">Achetez en groupe, économisez jusqu'à 15% et bénéficiez de paiements progressifs.</p>
      <a href="{{ route('tontines.index') }}" class="btn btn-sm btn-light fw-600">
        Voir les tontines <i class="bi bi-arrow-right ms-1"></i>
      </a>
    </div>
  </div>
</div>

{{-- Mes commandes récentes --}}
@if($mes_commandes->count())
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <span><i class="bi bi-receipt-cutoff me-2 text-primary"></i>Mes dernières commandes</span>
    <a href="{{ route('client.commandes.index') }}" class="btn btn-sm btn-outline-primary">Voir tout</a>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table mb-0">
        <thead><tr><th>Référence</th><th>Articles</th><th>Total</th><th>Statut</th><th>Date</th></tr></thead>
        <tbody>
          @foreach($mes_commandes as $cmd)
          <tr>
            <td class="fw-600 text-primary">{{ $cmd->reference }}</td>
            <td class="text-secondary">{{ $cmd->items->count() }} article(s)</td>
            <td class="fw-600">{{ number_format($cmd->total) }} FCFA</td>
            <td><span class="status-badge status-{{ $cmd->statut }}">{{ str_replace('_',' ',ucfirst($cmd->statut)) }}</span></td>
            <td class="text-secondary fs-xs">{{ $cmd->created_at->format('d/m/Y') }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endif

@push('scripts')
<script>
document.getElementById('searchInput')?.addEventListener('input', function() {
  const q = this.value.toLowerCase();
  document.querySelectorAll('.product-item').forEach(el => {
    el.style.display = el.textContent.toLowerCase().includes(q) ? '' : 'none';
  });
});
</script>
@endpush
</x-dashboard-layout>
