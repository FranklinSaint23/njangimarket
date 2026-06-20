<x-dashboard-layout>
<x-slot name="title">Passer la commande</x-slot>

<x-slot name="sidebar">
  <a class="nav-link" href="{{ route('client.dashboard') }}"><i class="bi bi-house"></i>Accueil</a>
  <a class="nav-link" href="{{ route('client.panier.index') }}"><i class="bi bi-cart3"></i>Mon Panier</a>
  <a class="nav-link active" href="{{ route('client.commandes.create') }}"><i class="bi bi-bag-check"></i>Commander</a>
  <a class="nav-link" href="{{ route('client.commandes.index') }}"><i class="bi bi-receipt-cutoff"></i>Mes Commandes</a>
  <a class="nav-link" href="{{ route('tontines.index') }}"><i class="bi bi-people-fill"></i>Tontines</a>
</x-slot>

<div class="page-header">
  <h1 class="page-title">Finaliser la commande</h1>
  <p class="page-subtitle">Vérifiez vos articles et choisissez votre mode de paiement</p>
</div>

@if(session('error'))
  <div class="alert alert-danger mb-4 d-flex align-items-center gap-2">
    <i class="bi bi-exclamation-circle-fill"></i>{{ session('error') }}
  </div>
@endif

<form method="POST" action="{{ route('client.commandes.store') }}">
@csrf
<div class="row g-4">
  {{-- Articles --}}
  <div class="col-lg-7">
    <div class="card mb-4">
      <div class="card-header"><i class="bi bi-cart3 me-2 text-primary"></i>Récapitulatif ({{ count($items) }} articles)</div>
      <div class="card-body p-0">
        @foreach($items as $item)
        <div class="d-flex align-items-center gap-3 p-4 {{ !$loop->last ? 'border-bottom' : '' }}">
          @if($item['produit']->image_principale)
            <img src="{{ Storage::url($item['produit']->image_principale) }}" width="56" height="56"
                 style="border-radius:.75rem;object-fit:cover;flex-shrink:0;">
          @else
            <div style="width:56px;height:56px;border-radius:.75rem;background:#f1f5f9;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
              <i class="bi bi-image text-secondary"></i>
            </div>
          @endif
          <div class="flex-grow-1">
            <div class="fw-600 fs-sm">{{ $item['produit']->nom }}</div>
            <div class="fs-xs text-secondary"><i class="bi bi-shop"></i> {{ $item['produit']->vendeur->name }}</div>
          </div>
          <div class="text-end">
            <div class="fw-700" style="color:#16a34a;">{{ number_format($item['sous_total']) }} F</div>
            <div class="fs-xs text-secondary">× {{ $item['quantite'] }}</div>
          </div>
        </div>
        @endforeach
      </div>
    </div>

    {{-- Adresse de livraison --}}
    <div class="card mb-4">
      <div class="card-header"><i class="bi bi-geo-alt me-2 text-primary"></i>Adresse de livraison</div>
      <div class="card-body">
        <div class="mb-3">
          <label for="adresse_livraison" class="form-label">Adresse complète <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
            <input type="text" id="adresse_livraison" name="adresse_livraison"
                   class="form-control @error('adresse_livraison') is-invalid @enderror"
                   value="{{ old('adresse_livraison', auth()->user()->adresse) }}"
                   placeholder="Ex: Quartier Bastos, Rue 1.234, Yaoundé"
                   required>
            @error('adresse_livraison')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
        <div>
          <label for="note" class="form-label">Note pour le livreur <span class="text-secondary fw-400">(optionnel)</span></label>
          <textarea id="note" name="note" class="form-control" rows="2"
                    placeholder="Ex: Bâtiment rouge, 2e étage, code portail 1234">{{ old('note') }}</textarea>
        </div>

        {{-- Mini carte pour localisation --}}
        <div class="mt-3">
          <div id="checkoutMap" class="map-container" style="height:180px;"></div>
          <p class="fs-xs text-secondary mt-1"><i class="bi bi-info-circle"></i> Cliquez sur la carte pour préciser votre position</p>
        </div>
      </div>
    </div>

    {{-- Mode de paiement --}}
    <div class="card">
      <div class="card-header"><i class="bi bi-credit-card me-2 text-primary"></i>Mode de paiement</div>
      <div class="card-body">
        <div class="row g-3 mb-3">
          @foreach([
            ['orange_money', 'Orange Money', 'bi-phone-fill', '#ea580c', '#fff7ed'],
            ['mtn_money',    'MTN MoMo',    'bi-phone-fill', '#ca8a04', '#fefce8'],
            ['tontine',      'Tontine Njangi','bi-people-fill','#7c3aed','#f5f3ff'],
          ] as [$val,$label,$icon,$color,$bg])
          <div class="col-4">
            <input type="radio" class="payment-option" name="methode_paiement" id="pay_{{ $val }}"
                   value="{{ $val }}" {{ old('methode_paiement','orange_money') === $val ? 'checked' : '' }} required>
            <label class="payment-label w-100" for="pay_{{ $val }}" style="--pay-color:{{ $color }};background:{{ $bg }};">
              <i class="bi {{ $icon }}" style="color:{{ $color }};font-size:1.25rem;display:block;margin-bottom:.35rem;"></i>
              <span style="font-size:.78rem;font-weight:600;color:#374151;">{{ $label }}</span>
            </label>
          </div>
          @endforeach
        </div>

        <div id="phoneField">
          <label for="phone_paiement" class="form-label">Numéro Mobile Money</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-phone"></i></span>
            <input type="tel" id="phone_paiement" name="phone_paiement"
                   class="form-control @error('phone_paiement') is-invalid @enderror"
                   value="{{ old('phone_paiement', auth()->user()->phone) }}"
                   placeholder="+237 6XX XXX XXX">
            @error('phone_paiement')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <p class="fs-xs text-secondary mt-1">
            <i class="bi bi-shield-check text-success"></i>
            Vous recevrez une notification pour confirmer le paiement.
          </p>
        </div>

        <div id="tontineInfo" class="mt-2 p-3 rounded-xl" style="background:#f5f3ff;border:1px solid #ddd6fe;display:none;">
          <div class="fw-600 fs-sm" style="color:#5b21b6;"><i class="bi bi-people-fill me-1"></i>Paiement via tontine</div>
          <p class="fs-xs text-secondary mt-1 mb-2">Votre commande sera groupée avec votre tontine active. Vous bénéficiez de 15% de réduction.</p>
          <a href="{{ route('tontines.index') }}" class="fs-xs fw-600 text-decoration-none" style="color:#7c3aed;">
            Voir mes tontines <i class="bi bi-arrow-right"></i>
          </a>
        </div>
      </div>
    </div>
  </div>

  {{-- Résumé prix --}}
  <div class="col-lg-5">
    <div class="card" style="position:sticky;top:88px;">
      <div class="card-header"><i class="bi bi-receipt-cutoff me-2 text-primary"></i>Total à payer</div>
      <div class="card-body">
        @foreach($items as $item)
        <div class="d-flex justify-content-between fs-sm mb-1">
          <span class="text-secondary">{{ $item['produit']->nom }} ×{{ $item['quantite'] }}</span>
          <span>{{ number_format($item['sous_total']) }} F</span>
        </div>
        @endforeach
        <hr>
        <div class="d-flex justify-content-between mb-1 fs-sm">
          <span class="text-secondary">Sous-total</span>
          <span class="fw-600">{{ number_format($total) }} FCFA</span>
        </div>
        <div class="d-flex justify-content-between mb-3 fs-sm">
          <span class="text-secondary">Livraison</span>
          <span class="text-secondary">Calculé par le livreur</span>
        </div>
        <div class="d-flex justify-content-between mb-4">
          <span class="fw-700 fs-6">Total</span>
          <span class="fw-700 fs-5" style="color:#16a34a;">{{ number_format($total) }} FCFA</span>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2 fw-600">
          <i class="bi bi-bag-check me-2"></i>Confirmer et payer
        </button>
        <a href="{{ route('client.panier.index') }}" class="btn btn-outline-secondary w-100 mt-2">
          <i class="bi bi-arrow-left me-1"></i>Retour au panier
        </a>

        <div class="mt-3 d-flex align-items-center gap-2 justify-content-center">
          <i class="bi bi-shield-lock-fill text-success fs-sm"></i>
          <span class="fs-xs text-secondary">Paiement sécurisé via Campay</span>
        </div>
      </div>
    </div>
  </div>
</div>
</form>

@push('scripts')
<script>
// Afficher/cacher champ téléphone selon mode
document.querySelectorAll('input[name=methode_paiement]').forEach(radio => {
  radio.addEventListener('change', function() {
    const isTontine = this.value === 'tontine';
    document.getElementById('phoneField').style.display  = isTontine ? 'none' : '';
    document.getElementById('tontineInfo').style.display = isTontine ? '' : 'none';
    document.getElementById('phone_paiement').required   = !isTontine;
  });
});

// Carte checkout - Yaoundé par défaut
const map = L.map('checkoutMap').setView([3.848, 11.502], 12);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: '© OpenStreetMap contributors', maxZoom: 19
}).addTo(map);

const icon = L.divIcon({
  html: '<div style="width:22px;height:22px;border-radius:50%;background:#ef4444;border:3px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,.3);"></div>',
  iconSize:[22,22], iconAnchor:[11,11], className:''
});
let pinMarker = null;

map.on('click', e => {
  if (pinMarker) map.removeLayer(pinMarker);
  pinMarker = L.marker(e.latlng, {icon}).addTo(map);
});

// Essayer de centrer sur la position utilisateur
if (navigator.geolocation) {
  navigator.geolocation.getCurrentPosition(pos => {
    map.setView([pos.coords.latitude, pos.coords.longitude], 14);
    if (!pinMarker) {
      pinMarker = L.marker([pos.coords.latitude, pos.coords.longitude], {icon}).addTo(map);
    }
  });
}
</script>
@endpush
</x-dashboard-layout>
