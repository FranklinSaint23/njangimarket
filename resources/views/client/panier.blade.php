<x-dashboard-layout>
<x-slot name="title">Mon Panier</x-slot>

<x-slot name="sidebar">
  <a class="nav-link" href="{{ route('client.dashboard') }}"><i class="bi bi-house"></i>Accueil</a>
  <a class="nav-link active" href="{{ route('client.panier.index') }}">
    <i class="bi bi-cart3"></i>Mon Panier
    @if(count(session('panier',[])) > 0)
      <span class="badge ms-auto" style="background:#ef4444;color:#fff;">{{ count(session('panier',[])) }}</span>
    @endif
  </a>
  <a class="nav-link" href="{{ route('client.commandes.index') }}"><i class="bi bi-receipt-cutoff"></i>Mes Commandes</a>
  <a class="nav-link" href="{{ route('tontines.index') }}"><i class="bi bi-people-fill"></i>Tontines</a>
  <div class="sidebar-section-label">Compte</div>
  <a class="nav-link" href="{{ route('profile.edit') }}"><i class="bi bi-person-circle"></i>Mon Profil</a>
</x-slot>

<div class="page-header">
  <h1 class="page-title">Mon Panier</h1>
  <p class="page-subtitle">{{ count($items) }} article(s) · Prêt à commander ?</p>
</div>

@if(session('success'))
  <div class="alert alert-success mb-4 d-flex align-items-center gap-2">
    <i class="bi bi-check-circle-fill"></i>{{ session('success') }}
  </div>
@endif

@if(count($items) > 0)
<div class="row g-4">
  {{-- Articles --}}
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-cart3 me-2 text-primary"></i>Articles ({{ count($items) }})</span>
        <form method="POST" action="{{ route('client.panier.vider') }}">
          @csrf @method('DELETE')
          <button type="submit" class="btn btn-sm btn-outline-danger"
                  onclick="return confirm('Vider le panier ?')">
            <i class="bi bi-trash me-1"></i>Vider
          </button>
        </form>
      </div>
      <div class="card-body p-0">
        @foreach($items as $item)
        <div class="d-flex align-items-start gap-3 p-4 {{ !$loop->last ? 'border-bottom' : '' }}">
          {{-- Image --}}
          @if($item['produit']->image_principale)
            <img src="{{ Storage::url($item['produit']->image_principale) }}"
                 width="72" height="72"
                 style="border-radius:.75rem;object-fit:cover;flex-shrink:0;">
          @else
            <div style="width:72px;height:72px;border-radius:.75rem;background:#f1f5f9;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
              <i class="bi bi-image text-secondary fs-4"></i>
            </div>
          @endif

          {{-- Info --}}
          <div class="flex-grow-1 min-w-0">
            <div class="fw-600 mb-1">{{ $item['produit']->nom }}</div>
            <div class="fs-xs text-secondary mb-3">
              <i class="bi bi-shop"></i> {{ $item['produit']->vendeur->name }}
              @if($item['produit']->localisation)
                · <i class="bi bi-geo-alt"></i> {{ $item['produit']->localisation }}
              @endif
            </div>
            <div class="d-flex align-items-center gap-2">
              <form method="POST" action="{{ route('client.panier.modifier', $item['produit']) }}" class="d-flex align-items-center gap-1">
                @csrf @method('PATCH')
                <button type="submit" name="quantite" value="{{ max(1,$item['quantite']-1) }}"
                        class="btn btn-icon btn-outline-secondary" style="width:30px;height:30px;padding:0;">−</button>
                <span class="fw-600 px-2" style="min-width:2rem;text-align:center;">{{ $item['quantite'] }}</span>
                <button type="submit" name="quantite" value="{{ $item['quantite']+1 }}"
                        class="btn btn-icon btn-outline-secondary" style="width:30px;height:30px;padding:0;">+</button>
              </form>
              <form method="POST" action="{{ route('client.panier.supprimer', $item['produit']) }}">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-icon btn-outline-danger" style="width:30px;height:30px;padding:0;">
                  <i class="bi bi-trash fs-xs"></i>
                </button>
              </form>
            </div>
          </div>

          {{-- Prix --}}
          <div class="text-end flex-shrink-0">
            <div class="fw-700" style="color:#16a34a;">{{ number_format($item['sous_total']) }} F</div>
            <div class="fs-xs text-secondary">{{ number_format($item['produit']->prix) }} × {{ $item['quantite'] }}</div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>

  {{-- Résumé --}}
  <div class="col-lg-4">
    <div class="card" style="position:sticky;top:88px;">
      <div class="card-header"><i class="bi bi-receipt-cutoff me-2 text-primary"></i>Résumé</div>
      <div class="card-body">
        <div class="d-flex justify-content-between mb-2 fs-sm">
          <span class="text-secondary">Sous-total</span>
          <span class="fw-600">{{ number_format($total) }} FCFA</span>
        </div>
        <div class="d-flex justify-content-between mb-2 fs-sm">
          <span class="text-secondary">Livraison</span>
          <span class="text-secondary">Calculé à la commande</span>
        </div>
        <hr>
        <div class="d-flex justify-content-between mb-4">
          <span class="fw-700">Total estimé</span>
          <span class="fw-700 fs-5" style="color:#16a34a;">{{ number_format($total) }} FCFA</span>
        </div>

        <a href="{{ route('client.commandes.create') }}" class="btn btn-primary w-100 py-2 fw-600">
          <i class="bi bi-bag-check me-2"></i>Passer la commande
        </a>
        <a href="{{ route('client.dashboard') }}" class="btn btn-outline-secondary w-100 mt-2">
          <i class="bi bi-arrow-left me-1"></i>Continuer mes achats
        </a>

        {{-- Tontine promo --}}
        <div class="mt-3 p-3 rounded-xl" style="background:#f5f3ff;border:1px solid #ddd6fe;">
          <div class="d-flex align-items-start gap-2">
            <i class="bi bi-people-fill text-purple mt-1" style="color:#7c3aed;flex-shrink:0;"></i>
            <div>
              <div class="fw-600 fs-sm" style="color:#5b21b6;">Payez en tontine</div>
              <div class="fs-xs text-secondary mt-1">Rejoignez un groupe d'achat et économisez jusqu'à 15% sur ce panier.</div>
              <a href="{{ route('tontines.index') }}" class="fs-xs fw-600 text-decoration-none" style="color:#7c3aed;">
                Voir les tontines <i class="bi bi-arrow-right"></i>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@else
<div class="empty-state" style="padding:4rem 1rem;">
  <i class="bi bi-cart-x" style="color:#d1d5db;"></i>
  <h4>Votre panier est vide</h4>
  <p>Découvrez les produits frais du marché local</p>
  <a href="{{ route('client.dashboard') }}" class="btn btn-primary">
    <i class="bi bi-shop me-1"></i>Voir les produits
  </a>
</div>
@endif
</x-dashboard-layout>
