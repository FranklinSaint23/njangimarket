<x-dashboard-layout>
<x-slot name="title">Commande {{ $commande->reference }}</x-slot>

<x-slot name="sidebar">
  <a class="nav-link" href="{{ route('client.dashboard') }}"><i class="bi bi-house"></i>Accueil</a>
  <a class="nav-link" href="{{ route('client.panier.index') }}"><i class="bi bi-cart3"></i>Mon Panier</a>
  <a class="nav-link active" href="{{ route('client.commandes.index') }}"><i class="bi bi-receipt-cutoff"></i>Mes Commandes</a>
  <a class="nav-link" href="{{ route('tontines.index') }}"><i class="bi bi-people-fill"></i>Tontines</a>
</x-slot>

<div class="page-header d-flex justify-content-between align-items-start">
  <div>
    <h1 class="page-title">{{ $commande->reference }}</h1>
    <p class="page-subtitle">{{ $commande->created_at->format('d M Y à H:i') }}</p>
  </div>
  @if($commande->statut === 'en_attente' || $commande->statut === 'paiement_en_cours')
    <a href="{{ route('paiement.initier', $commande) }}" class="btn btn-primary">
      <i class="bi bi-credit-card me-1"></i>Payer maintenant
    </a>
  @endif
</div>

@if(session('success'))
  <div class="alert alert-success mb-4 d-flex align-items-center gap-2">
    <i class="bi bi-check-circle-fill"></i>{{ session('success') }}
  </div>
@endif

<div class="row g-4">
  <div class="col-lg-8">
    {{-- Articles --}}
    <div class="card mb-4">
      <div class="card-header"><i class="bi bi-box-seam me-2 text-primary"></i>Articles commandés</div>
      <div class="card-body p-0">
        @foreach($commande->items as $item)
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
            <div class="fs-xs text-secondary">× {{ $item->quantite }} · {{ number_format($item->prix_unitaire) }} F/unité</div>
          </div>
          <div class="fw-700" style="color:#16a34a;">{{ number_format($item->sous_total) }} F</div>
        </div>
        @endforeach
      </div>
    </div>

    {{-- Suivi livraison --}}
    @if($commande->livraison)
    <div class="card mb-4">
      <div class="card-header"><i class="bi bi-truck me-2 text-primary"></i>Suivi de livraison</div>
      <div class="card-body">
        <div id="trackingMap" class="map-container mb-3" style="height:220px;"></div>
        <div class="d-flex align-items-center gap-3">
          <div class="avatar" style="background:#f59e0b;">
            {{ strtoupper(substr($commande->livraison->livreur->name ?? 'L',0,1)) }}
          </div>
          <div>
            <div class="fw-600">{{ $commande->livraison->livreur->name ?? 'En attente d\'un livreur' }}</div>
            <div class="fs-xs text-secondary">Livreur</div>
          </div>
          <span class="ms-auto status-badge status-{{ $commande->livraison->statut }}">
            {{ str_replace('_',' ',ucfirst($commande->livraison->statut)) }}
          </span>
        </div>
      </div>
    </div>
    @endif
  </div>

  {{-- Résumé commande --}}
  <div class="col-lg-4">
    <div class="card mb-3">
      <div class="card-header"><i class="bi bi-info-circle me-2 text-primary"></i>Détails</div>
      <div class="card-body">
        <div class="d-flex justify-content-between mb-2 fs-sm">
          <span class="text-secondary">Statut</span>
          <span class="status-badge status-{{ $commande->statut }}">{{ str_replace('_',' ',ucfirst($commande->statut)) }}</span>
        </div>
        <div class="d-flex justify-content-between mb-2 fs-sm">
          <span class="text-secondary">Paiement</span>
          @php $pm=['orange_money'=>['#ea580c','Orange Money'],'mtn_money'=>['#ca8a04','MTN MoMo'],'tontine'=>['#7c3aed','Tontine']]; [$pc,$pl]=$pm[$commande->methode_paiement]??['#64748b','—']; @endphp
          <span class="fw-600" style="color:{{ $pc }}">{{ $pl }}</span>
        </div>
        <div class="d-flex justify-content-between mb-2 fs-sm">
          <span class="text-secondary">Total</span>
          <span class="fw-700 text-success">{{ number_format($commande->total) }} FCFA</span>
        </div>
        <hr>
        <div class="fs-sm">
          <div class="text-secondary mb-1">Adresse de livraison</div>
          <div class="fw-600">{{ $commande->adresse_livraison }}</div>
        </div>
        @if($commande->note)
        <div class="fs-sm mt-2">
          <div class="text-secondary mb-1">Note</div>
          <div>{{ $commande->note }}</div>
        </div>
        @endif
      </div>
    </div>

    <a href="{{ route('client.commandes.index') }}" class="btn btn-outline-secondary w-100">
      <i class="bi bi-arrow-left me-1"></i>Toutes mes commandes
    </a>
  </div>
</div>

@if($commande->livraison && $commande->livraison->livreur?->latitude)
@push('scripts')
<script>
const clientLat = {{ auth()->user()->latitude ?? 3.848 }};
const clientLng = {{ auth()->user()->longitude ?? 11.502 }};
const livreurLat = {{ $commande->livraison->livreur->latitude }};
const livreurLng = {{ $commande->livraison->livreur->longitude }};

const map = L.map('trackingMap').setView([clientLat, clientLng], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: '© OpenStreetMap contributors', maxZoom: 19
}).addTo(map);

const clientIcon = L.divIcon({
  html: '<div style="width:22px;height:22px;border-radius:50%;background:#ef4444;border:3px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,.3);"></div>',
  iconSize:[22,22], iconAnchor:[11,11], className:''
});
const livreurIcon = L.divIcon({
  html: '<div style="width:26px;height:26px;border-radius:50%;background:#f59e0b;border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,.3);display:flex;align-items:center;justify-content:center;font-size:.7rem;">🏍</div>',
  iconSize:[26,26], iconAnchor:[13,13], className:''
});

L.marker([clientLat, clientLng], {icon: clientIcon}).addTo(map).bindPopup('Votre adresse');
L.marker([livreurLat, livreurLng], {icon: livreurIcon}).addTo(map).bindPopup('Votre livreur');

map.fitBounds([[clientLat, clientLng], [livreurLat, livreurLng]], {padding: [20, 20]});
</script>
@endpush
@endif
</x-dashboard-layout>
