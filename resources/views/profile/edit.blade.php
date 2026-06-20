<x-dashboard-layout>
<x-slot name="title">Mon Profil</x-slot>

<x-slot name="sidebar">
  @php $role = auth()->user()->role; @endphp
  @if($role === 'admin')
    <a class="nav-link" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i>Dashboard</a>
  @elseif($role === 'vendeur')
    <a class="nav-link" href="{{ route('vendeur.dashboard') }}"><i class="bi bi-speedometer2"></i>Dashboard</a>
  @elseif($role === 'livreur')
    <a class="nav-link" href="{{ route('livreur.dashboard') }}"><i class="bi bi-speedometer2"></i>Dashboard</a>
  @else
    <a class="nav-link" href="{{ route('client.dashboard') }}"><i class="bi bi-house"></i>Accueil</a>
  @endif
  <div class="sidebar-section-label">Compte</div>
  <a class="nav-link active" href="{{ route('profile.edit') }}"><i class="bi bi-person-circle"></i>Mon Profil</a>
</x-slot>

<div class="page-header">
  <h1 class="page-title">Mon Profil</h1>
  <p class="page-subtitle">Gérez vos informations personnelles et votre localisation</p>
</div>

@if(session('status') === 'profile-updated')
  <div class="alert alert-success mb-4 d-flex align-items-center gap-2">
    <i class="bi bi-check-circle-fill"></i>Profil mis à jour avec succès !
  </div>
@endif

<div class="row g-4">
  {{-- Infos principales --}}
  <div class="col-lg-7">
    <div class="card mb-4">
      <div class="card-header"><i class="bi bi-person me-2 text-primary"></i>Informations personnelles</div>
      <div class="card-body p-4">
        <form method="POST" action="{{ route('profile.update') }}">
          @csrf @method('PATCH')

          <div class="d-flex align-items-center gap-3 mb-4">
            <div class="avatar" style="width:64px;height:64px;font-size:1.5rem;background:{{ ['admin'=>'#6366f1','vendeur'=>'#2563eb','client'=>'#16a34a','livreur'=>'#f59e0b'][$user->role] ?? '#64748b' }};">
              {{ strtoupper(substr($user->name,0,1)) }}
            </div>
            <div>
              <div class="fw-700 fs-5">{{ $user->name }}</div>
              <span class="badge badge-role-{{ $user->role }}">{{ ucfirst($user->role) }}</span>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Nom complet <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-person"></i></span>
              <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                     value="{{ old('name', $user->name) }}" required>
              @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Email <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-envelope"></i></span>
              <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                     value="{{ old('email', $user->email) }}" required>
              @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Téléphone Mobile Money</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-phone"></i></span>
              <input type="tel" name="phone" class="form-control"
                     value="{{ old('phone', $user->phone) }}" placeholder="+237 6XX XXX XXX">
            </div>
            <p class="fs-xs text-secondary mt-1">Utilisé pour les paiements Orange Money / MTN MoMo</p>
          </div>

          <div class="mb-4">
            <label class="form-label">Adresse</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
              <input type="text" name="adresse" class="form-control"
                     value="{{ old('adresse', $user->adresse) }}" placeholder="Quartier, rue, ville…">
            </div>
          </div>

          {{-- Carte localisation --}}
          <div class="mb-4">
            <label class="form-label d-flex justify-content-between align-items-center">
              <span>Ma localisation sur la carte</span>
              <button type="button" class="btn btn-sm btn-outline-primary fs-xs" id="detectLocation">
                <i class="bi bi-crosshair me-1"></i>Me localiser
              </button>
            </label>
            <div id="profileMap" class="map-container mb-2" style="height:220px;"></div>
            <p class="fs-xs text-secondary">Cliquez sur la carte pour définir votre position précise.</p>
            <input type="hidden" name="latitude"  id="latInput"  value="{{ old('latitude',  $user->latitude) }}">
            <input type="hidden" name="longitude" id="lngInput"  value="{{ old('longitude', $user->longitude) }}">
            @if($user->latitude && $user->longitude)
              <div class="fs-xs" style="color:#16a34a;">
                <i class="bi bi-pin-map-fill me-1"></i>{{ round($user->latitude,5) }}, {{ round($user->longitude,5) }}
              </div>
            @endif
          </div>

          <button type="submit" class="btn btn-primary w-100 py-2 fw-600">
            <i class="bi bi-check2-circle me-1"></i>Enregistrer les modifications
          </button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-5 d-flex flex-column gap-4">
    {{-- Mot de passe --}}
    <div class="card">
      <div class="card-header"><i class="bi bi-lock me-2 text-primary"></i>Changer le mot de passe</div>
      <div class="card-body p-4">
        <form method="POST" action="{{ route('password.update') }}">
          @csrf @method('PUT')

          <div class="mb-3">
            <label class="form-label">Mot de passe actuel</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-lock"></i></span>
              <input type="password" name="current_password"
                     class="form-control @error('current_password', 'updatePassword') is-invalid @enderror">
              @error('current_password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Nouveau mot de passe</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
              <input type="password" name="password"
                     class="form-control @error('password', 'updatePassword') is-invalid @enderror">
              @error('password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="mb-4">
            <label class="form-label">Confirmer</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
              <input type="password" name="password_confirmation" class="form-control">
            </div>
          </div>

          <button type="submit" class="btn btn-primary w-100 fw-600">
            <i class="bi bi-shield-lock me-1"></i>Mettre à jour
          </button>
          @if(session('status') === 'password-updated')
            <div class="mt-2 text-center fs-sm text-success">
              <i class="bi bi-check-circle-fill me-1"></i>Mot de passe mis à jour !
            </div>
          @endif
        </form>
      </div>
    </div>

    {{-- Supprimer compte --}}
    <div class="card" style="border-color:#fecaca;">
      <div class="card-header" style="color:#dc2626;">
        <i class="bi bi-exclamation-triangle me-2"></i>Zone dangereuse
      </div>
      <div class="card-body p-4">
        <p class="fs-sm text-secondary mb-3">La suppression est irréversible. Toutes vos données seront effacées.</p>
        <form method="POST" action="{{ route('profile.destroy') }}"
              onsubmit="return confirm('Supprimer définitivement votre compte ? Cette action est irréversible.')">
          @csrf @method('DELETE')
          <div class="mb-3">
            <label class="form-label fs-sm">Confirmez avec votre mot de passe</label>
            <input type="password" name="password" class="form-control" required>
            @error('password', 'userDeletion')
              <div class="text-danger fs-xs mt-1">{{ $message }}</div>
            @enderror
          </div>
          <button type="submit" class="btn btn-danger w-100 fw-600">
            <i class="bi bi-trash3 me-1"></i>Supprimer mon compte
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
const initLat = {{ $user->latitude ?? 3.848 }};
const initLng = {{ $user->longitude ?? 11.502 }};
const hasPos  = {{ $user->latitude ? 'true' : 'false' }};

const map = L.map('profileMap').setView([initLat, initLng], hasPos ? 14 : 12);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: '© OpenStreetMap contributors', maxZoom: 19
}).addTo(map);

const pinIcon = L.divIcon({
  html: '<div style="width:24px;height:24px;border-radius:50%;background:#16a34a;border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,.3);"></div>',
  iconSize:[24,24], iconAnchor:[12,12], className:''
});

let pin = hasPos ? L.marker([initLat, initLng], {icon: pinIcon}).addTo(map).bindPopup('Ma position') : null;

function setPin(lat, lng) {
  if (pin) map.removeLayer(pin);
  pin = L.marker([lat, lng], {icon: pinIcon}).addTo(map).bindPopup('Ma position').openPopup();
  document.getElementById('latInput').value = lat.toFixed(7);
  document.getElementById('lngInput').value = lng.toFixed(7);
}

map.on('click', e => setPin(e.latlng.lat, e.latlng.lng));

document.getElementById('detectLocation')?.addEventListener('click', function() {
  if (!navigator.geolocation) { alert('Géolocalisation non supportée'); return; }
  const btn = this;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>…';
  navigator.geolocation.getCurrentPosition(pos => {
    setPin(pos.coords.latitude, pos.coords.longitude);
    map.setView([pos.coords.latitude, pos.coords.longitude], 15);
    btn.innerHTML = '<i class="bi bi-crosshair me-1"></i>Me localiser';
  }, () => {
    btn.innerHTML = '<i class="bi bi-crosshair me-1"></i>Me localiser';
    alert('Impossible d\'obtenir votre position.');
  });
});
</script>
@endpush
</x-dashboard-layout>
