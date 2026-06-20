<x-dashboard-layout>
<x-slot name="title">Paiement</x-slot>

<x-slot name="sidebar">
  <a class="nav-link" href="{{ route('client.dashboard') }}"><i class="bi bi-house"></i>Accueil</a>
  <a class="nav-link" href="{{ route('client.panier.index') }}"><i class="bi bi-cart3"></i>Mon Panier</a>
  <a class="nav-link active" href="#"><i class="bi bi-credit-card"></i>Paiement</a>
  <a class="nav-link" href="{{ route('client.commandes.index') }}"><i class="bi bi-receipt-cutoff"></i>Mes Commandes</a>
</x-slot>

<div class="page-header">
  <h1 class="page-title">Paiement Mobile Money</h1>
  <p class="page-subtitle">Commande {{ $commande->reference }}</p>
</div>

@if(session('error'))
  <div class="alert alert-danger mb-4 d-flex align-items-center gap-2">
    <i class="bi bi-exclamation-circle-fill"></i>{{ session('error') }}
  </div>
@endif

<div class="row justify-content-center">
  <div class="col-md-6 col-lg-5">
    <div class="card">
      <div class="card-body p-4">
        {{-- Montant --}}
        <div class="text-center mb-4">
          @php
            $pm = ['orange_money'=>['#ea580c','Orange Money','bi-phone-fill'],
                   'mtn_money'=>['#ca8a04','MTN MoMo','bi-phone-fill'],
                   'tontine'=>['#7c3aed','Tontine','bi-people-fill']];
            [$color,$label,$icon] = $pm[$commande->methode_paiement] ?? ['#64748b','Paiement','bi-credit-card'];
          @endphp
          <div class="stat-icon mx-auto mb-3" style="background:{{ $color }}18;color:{{ $color }};width:64px;height:64px;">
            <i class="bi {{ $icon }} fs-4"></i>
          </div>
          <div class="fw-700 fs-sm text-secondary mb-1">{{ $label }}</div>
          <div style="font-size:2rem;font-weight:800;color:#0f172a;">{{ number_format($commande->total) }} FCFA</div>
          <div class="fs-xs text-secondary">{{ $commande->reference }}</div>
        </div>

        <form method="POST" action="{{ route('paiement.payer', $commande) }}">
          @csrf
          <div class="mb-4">
            <label for="phone" class="form-label fw-600">Numéro Mobile Money</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-phone"></i></span>
              <input type="tel" id="phone" name="phone"
                     class="form-control @error('phone') is-invalid @enderror"
                     value="{{ old('phone', $phone ?? auth()->user()->phone) }}"
                     placeholder="+237 6XX XXX XXX" required>
              @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="p-3 rounded-xl mb-4" style="background:#f0fdf4;border:1px solid #bbf7d0;">
            <div class="fs-sm text-secondary">
              <i class="bi bi-info-circle-fill text-success me-1"></i>
              <strong>Comment ça marche :</strong><br>
              1. Cliquez "Payer maintenant"<br>
              2. Vous recevrez une notification sur votre téléphone<br>
              3. Entrez votre PIN Mobile Money pour confirmer
            </div>
          </div>

          <button type="submit" class="btn btn-primary w-100 py-2 fw-600">
            <i class="bi bi-phone me-2"></i>Payer {{ number_format($commande->total) }} FCFA
          </button>
        </form>

        <a href="{{ route('client.commandes.show', $commande) }}" class="btn btn-outline-secondary w-100 mt-2">
          Payer plus tard
        </a>
      </div>
    </div>
  </div>
</div>
</x-dashboard-layout>
