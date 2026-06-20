<x-dashboard-layout>
<x-slot name="title">Espace Livreur</x-slot>

<x-slot name="sidebar">
  <a class="nav-link active" href="{{ route('livreur.dashboard') }}"><i class="bi bi-speedometer2"></i>Dashboard</a>
  <div class="sidebar-section-label">Livraisons</div>
  <a class="nav-link" href="{{ route('livreur.livraisons.index') }}"><i class="bi bi-truck"></i>Mes livraisons</a>
  <a class="nav-link" href="{{ route('livreur.livraisons.index') }}"><i class="bi bi-clock-history"></i>Historique</a>
  <a class="nav-link" href="{{ route('livreur.livraisons.index') }}"><i class="bi bi-cash-stack"></i>Mes commissions</a>
  <div class="sidebar-section-label">Compte</div>
  <a class="nav-link" href="{{ route('profile.edit') }}"><i class="bi bi-person-circle"></i>Mon Profil</a>
</x-slot>

<div class="page-header d-flex justify-content-between align-items-start">
  <div>
    <h1 class="page-title">Espace Livreur</h1>
    <p class="page-subtitle">{{ auth()->user()->name }} · Tableau de bord</p>
  </div>
  <span class="badge" style="background:#f0fdf4;color:#16a34a;padding:.5rem 1rem;border-radius:.75rem;font-weight:600;font-size:.8rem;">
    <i class="bi bi-circle-fill me-1" style="font-size:.45rem;vertical-align:middle;"></i>En ligne
  </span>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
  @foreach([
    ['Total livraisons', $stats['livraisons_total'],   'bi-truck',        '#16a34a','#f0fdf4'],
    ['En cours',         $stats['livraisons_en_cours'], 'bi-clock',        '#f59e0b','#fffbeb'],
    ['Terminées',        $stats['livraisons_terminees'],'bi-check-circle', '#6366f1','#eef2ff'],
    ['Commissions (F)',  number_format($stats['commission_totale']),'bi-cash','#ef4444','#fef2f2'],
  ] as [$l,$v,$i,$c,$bg])
  <div class="col-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-icon" style="background:{{ $bg }};color:{{ $c }};"><i class="bi {{ $i }}"></i></div>
      <div><div class="stat-value">{{ $v }}</div><div class="stat-label">{{ $l }}</div></div>
    </div>
  </div>
  @endforeach
</div>

{{-- Bonus banner --}}
<div class="card border-0 mb-4" style="background:linear-gradient(135deg,#1e3a5f,#1e40af);">
  <div class="card-body p-4 d-flex align-items-center gap-4 text-white">
    <div style="font-size:2.5rem;flex-shrink:0;">⭐</div>
    <div>
      <h6 class="fw-700 mb-1">Bonus satisfaction</h6>
      <p class="mb-0 opacity-75 fs-sm">{{ number_format($stats['bonus_total']) }} FCFA gagnés · +10% ponctualité, +5% si client satisfait</p>
    </div>
  </div>
</div>

<div class="row g-3">
  {{-- Livraisons en cours --}}
  <div class="col-lg-7">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-truck me-2 text-primary"></i>Livraisons en cours</span>
        @if($stats['livraisons_en_cours'] > 0)
          <span class="badge" style="background:#fff7ed;color:#ea580c;">{{ $stats['livraisons_en_cours'] }} en attente</span>
        @endif
      </div>
      <div class="card-body p-0">
        @forelse($livraisons_en_cours as $livraison)
        <div class="p-4 {{ !$loop->last ? 'border-bottom' : '' }}">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <div>
              <div class="fw-600">{{ $livraison->commande->reference }}</div>
              <div class="fs-sm text-secondary"><i class="bi bi-person"></i> {{ $livraison->commande->client->name }}</div>
            </div>
            <span class="status-badge" style="background:#fff7ed;color:#ea580c;">{{ ucfirst($livraison->statut) }}</span>
          </div>
          <div class="fs-sm text-secondary mb-1">
            <i class="bi bi-geo-alt text-primary"></i>
            {{ $livraison->commande->adresse_livraison ?? 'Adresse non spécifiée' }}
          </div>
          <div class="d-flex justify-content-between align-items-center mt-2">
            <span class="fw-600 text-success">{{ number_format($livraison->commission) }} FCFA</span>
            <form method="POST" action="#">
              @csrf @method('PATCH')
              <button type="submit" class="btn btn-primary btn-sm">
                <i class="bi bi-check2 me-1"></i>Marquer livrée
              </button>
            </form>
          </div>
        </div>
        @empty
        <div class="empty-state py-5">
          <i class="bi bi-truck"></i>
          <h5>Aucune livraison en cours</h5>
          <p class="fs-sm text-secondary">Vous serez notifié dès qu'une livraison vous est assignée.</p>
        </div>
        @endforelse
      </div>
    </div>
  </div>

  {{-- Carte + Historique --}}
  <div class="col-lg-5 d-flex flex-column gap-3">
    <div class="card">
      <div class="card-header"><i class="bi bi-geo-alt-fill me-2 text-primary"></i>Ma position</div>
      <div class="card-body">
        <div id="livreurMap" class="map-container mb-3" style="height:200px;"></div>
        <button class="btn btn-primary w-100 btn-sm" id="updatePosition">
          <i class="bi bi-crosshair me-1"></i>Mettre à jour ma position
        </button>
      </div>
    </div>

    @if($historique->count())
    <div class="card">
      <div class="card-header"><i class="bi bi-check2-circle me-2 text-primary"></i>Historique récent</div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table mb-0 fs-sm">
            <thead><tr><th>Réf.</th><th>Commission</th><th>Date</th></tr></thead>
            <tbody>
              @foreach($historique as $lv)
              <tr>
                <td class="fw-600">{{ $lv->commande->reference }}</td>
                <td class="text-success fw-600">{{ number_format($lv->commission + $lv->bonus_satisfaction) }} F</td>
                <td class="text-secondary">{{ $lv->livre_le?->format('d/m/Y') ?? '—' }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
    @endif
  </div>
</div>

@push('scripts')
<script>
const lat = {{ auth()->user()->latitude ?? 3.848 }};
const lng = {{ auth()->user()->longitude ?? 11.502 }};

const map = L.map('livreurMap').setView([lat, lng], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: '© OpenStreetMap contributors', maxZoom: 19
}).addTo(map);

const icon = L.divIcon({
  html: '<div style="width:26px;height:26px;border-radius:50%;background:#16a34a;border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,.3);"></div>',
  iconSize:[26,26], iconAnchor:[13,13], className:''
});
let marker = L.marker([lat, lng], {icon}).addTo(map).bindPopup('Ma position').openPopup();

document.getElementById('updatePosition')?.addEventListener('click', function() {
  if (!navigator.geolocation) { alert('Géolocalisation non supportée'); return; }
  const btn = this;
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Localisation...';

  navigator.geolocation.getCurrentPosition(pos => {
    const {latitude, longitude} = pos.coords;
    marker.setLatLng([latitude, longitude]);
    map.setView([latitude, longitude], 15);
    fetch('/livreur/position', {
      method: 'POST',
      headers: {'Content-Type':'application/json','X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content ?? ''},
      body: JSON.stringify({latitude, longitude})
    });
    btn.innerHTML = '<i class="bi bi-check2 me-1"></i>Position mise à jour !';
    btn.disabled = false;
    setTimeout(() => { btn.innerHTML = '<i class="bi bi-crosshair me-1"></i>Mettre à jour ma position'; }, 2500);
  }, () => {
    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-crosshair me-1"></i>Mettre à jour ma position';
    alert('Impossible d\'obtenir votre position.');
  });
});
</script>
@endpush
</x-dashboard-layout>
