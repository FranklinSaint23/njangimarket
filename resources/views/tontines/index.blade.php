<x-dashboard-layout>
<x-slot name="title">Tontines Njangi</x-slot>

<x-slot name="sidebar">
  <a class="nav-link" href="{{ auth()->user()->isVendeur() ? route('vendeur.dashboard') : route('client.dashboard') }}">
    <i class="bi bi-house"></i>Accueil
  </a>
  <div class="sidebar-section-label">Tontines</div>
  <a class="nav-link active" href="{{ route('tontines.index') }}"><i class="bi bi-people-fill"></i>Mes Tontines</a>
  <a class="nav-link" href="{{ route('tontines.create') }}"><i class="bi bi-plus-circle"></i>Créer une tontine</a>
  @if(auth()->user()->isClient())
  <div class="sidebar-section-label">Achats</div>
  <a class="nav-link" href="{{ route('client.panier.index') }}"><i class="bi bi-cart3"></i>Mon Panier</a>
  <a class="nav-link" href="{{ route('client.commandes.index') }}"><i class="bi bi-receipt-cutoff"></i>Mes Commandes</a>
  @endif
  <div class="sidebar-section-label">Compte</div>
  <a class="nav-link" href="{{ route('profile.edit') }}"><i class="bi bi-person-circle"></i>Mon Profil</a>
</x-slot>

<div class="page-header d-flex justify-content-between align-items-start">
  <div>
    <h1 class="page-title">Tontines Njangi</h1>
    <p class="page-subtitle">Achats groupés, économies collectives</p>
  </div>
  <a href="{{ route('tontines.create') }}" class="btn btn-primary">
    <i class="bi bi-plus-circle me-1"></i>Créer une tontine
  </a>
</div>

@if(session('success'))
  <div class="alert alert-success mb-4 d-flex align-items-center gap-2">
    <i class="bi bi-check-circle-fill"></i>{{ session('success') }}
  </div>
@endif

{{-- Mes tontines --}}
@if($mes_tontines->count())
<h2 style="font-size:1rem;font-weight:700;margin-bottom:1rem;">Mes tontines ({{ $mes_tontines->count() }})</h2>
<div class="row g-3 mb-5">
  @foreach($mes_tontines as $t)
  @php
    $pct = $t->nb_membres_max > 0 ? round($t->nb_membres / $t->nb_membres_max * 100) : 0;
    $sc = ['ouvert'=>['#f0fdf4','#16a34a'],'en_cours'=>['#fffbeb','#d97706'],'clos'=>['#f1f5f9','#64748b']];
    [$sbg,$sc2] = $sc[$t->statut] ?? ['#f1f5f9','#64748b'];
    $est_createur = $t->createur_id === auth()->id();
  @endphp
  <div class="col-md-6 col-xl-4">
    <div class="card h-100">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-start mb-3">
          <div>
            <h6 class="fw-700 mb-0">{{ $t->nom }}</h6>
            @if($est_createur)
              <span class="fs-xs" style="color:#16a34a;font-weight:600;"><i class="bi bi-star-fill me-1"></i>Créateur</span>
            @else
              <span class="fs-xs text-secondary">par {{ $t->createur->name }}</span>
            @endif
          </div>
          <span class="status-badge" style="background:{{ $sbg }};color:{{ $sc2 }};">{{ ucfirst($t->statut) }}</span>
        </div>

        @if($t->description)
          <p class="fs-sm text-secondary mb-3" style="line-height:1.5;">{{ Str::limit($t->description, 80) }}</p>
        @endif

        <div class="row g-2 mb-3">
          <div class="col-6">
            <div class="p-2 rounded-xl" style="background:#f8fafc;">
              <div class="fs-xs text-secondary">Cotisation</div>
              <div class="fw-700 fs-sm">{{ number_format($t->montant_cotisation) }} F</div>
            </div>
          </div>
          <div class="col-6">
            <div class="p-2 rounded-xl" style="background:#f8fafc;">
              <div class="fs-xs text-secondary">Fonds</div>
              <div class="fw-700 fs-sm text-success">{{ number_format($t->fond_total) }} F</div>
            </div>
          </div>
        </div>

        {{-- Barre membres --}}
        <div class="mb-3">
          <div class="d-flex justify-content-between fs-xs mb-1">
            <span class="text-secondary">Membres</span>
            <span class="fw-600">{{ $t->nb_membres }}/{{ $t->nb_membres_max }}</span>
          </div>
          <div style="height:6px;background:#f1f5f9;border-radius:99px;overflow:hidden;">
            <div style="height:100%;width:{{ $pct }}%;background:#16a34a;border-radius:99px;transition:width .3s;"></div>
          </div>
        </div>

        <div class="d-flex gap-2">
          <a href="{{ route('tontines.show', $t) }}" class="btn btn-primary btn-sm flex-grow-1">
            <i class="bi bi-eye me-1"></i>Voir
          </a>
          @if($est_createur)
          <form method="POST" action="{{ route('tontines.destroy', $t) }}">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-icon btn-outline-danger"
                    onclick="return confirm('Supprimer cette tontine ?')">
              <i class="bi bi-trash fs-xs"></i>
            </button>
          </form>
          @endif
        </div>
      </div>
    </div>
  </div>
  @endforeach
</div>
@endif

{{-- Tontines disponibles --}}
@if($tontines_disponibles->count())
<div class="d-flex align-items-center gap-3 mb-3">
  <h2 style="font-size:1rem;font-weight:700;margin:0;">Tontines disponibles</h2>
  <span class="badge" style="background:#eef2ff;color:#4338ca;">{{ $tontines_disponibles->count() }} disponible(s)</span>
</div>
<div class="row g-3">
  @foreach($tontines_disponibles as $t)
  @php $pct = $t->nb_membres_max > 0 ? round($t->nb_membres / $t->nb_membres_max * 100) : 0; @endphp
  <div class="col-md-6 col-xl-4">
    <div class="card h-100" style="border:2px dashed #e2e8f0;">
      <div class="card-body p-4">
        <h6 class="fw-700 mb-1">{{ $t->nom }}</h6>
        <div class="fs-xs text-secondary mb-3">par {{ $t->createur->name }}</div>

        @if($t->description)
          <p class="fs-sm text-secondary mb-3">{{ Str::limit($t->description, 80) }}</p>
        @endif

        <div class="row g-2 mb-3">
          <div class="col-6">
            <div class="p-2 rounded-xl" style="background:#f8fafc;">
              <div class="fs-xs text-secondary">Cotisation/membre</div>
              <div class="fw-700 fs-sm">{{ number_format($t->montant_cotisation) }} F</div>
            </div>
          </div>
          <div class="col-6">
            <div class="p-2 rounded-xl" style="background:#f0fdf4;">
              <div class="fs-xs text-secondary">Économie</div>
              <div class="fw-700 fs-sm text-success">−15%</div>
            </div>
          </div>
        </div>

        <div class="mb-3">
          <div class="d-flex justify-content-between fs-xs mb-1">
            <span class="text-secondary">Places</span>
            <span class="fw-600">{{ $t->nb_membres }}/{{ $t->nb_membres_max }}</span>
          </div>
          <div style="height:6px;background:#f1f5f9;border-radius:99px;overflow:hidden;">
            <div style="height:100%;width:{{ $pct }}%;background:#6366f1;border-radius:99px;"></div>
          </div>
        </div>

        <form method="POST" action="{{ route('tontines.rejoindre', $t) }}">
          @csrf
          <button type="submit" class="btn btn-primary w-100 btn-sm fw-600">
            <i class="bi bi-people-fill me-1"></i>Rejoindre cette tontine
          </button>
        </form>
      </div>
    </div>
  </div>
  @endforeach
</div>

@elseif($mes_tontines->isEmpty())
<div class="empty-state" style="padding:4rem 1rem;">
  <i class="bi bi-people"></i>
  <h5>Aucune tontine</h5>
  <p>Créez votre première tontine ou attendez qu'un membre en partage une.</p>
  <a href="{{ route('tontines.create') }}" class="btn btn-primary">
    <i class="bi bi-plus-circle me-1"></i>Créer une tontine
  </a>
</div>
@endif
</x-dashboard-layout>
