<x-dashboard-layout>
<x-slot name="title">Espace Vendeur</x-slot>

<x-slot name="sidebar">
  <a class="nav-link active" href="{{ route('vendeur.dashboard') }}"><i class="bi bi-speedometer2"></i>Dashboard</a>
  <div class="sidebar-section-label">Catalogue</div>
  <a class="nav-link" href="{{ route('vendeur.produits.index') }}"><i class="bi bi-box-seam"></i>Mes Produits</a>
  <a class="nav-link" href="{{ route('vendeur.produits.create') }}"><i class="bi bi-plus-circle"></i>Ajouter un produit</a>
  <div class="sidebar-section-label">Commerce</div>
  <a class="nav-link" href="{{ route('vendeur.commandes.index') }}"><i class="bi bi-receipt-cutoff"></i>Commandes reçues</a>
  <a class="nav-link" href="{{ route('tontines.index') }}"><i class="bi bi-people-fill"></i>Mes Tontines</a>
  <div class="sidebar-section-label">Compte</div>
  <a class="nav-link" href="{{ route('profile.edit') }}"><i class="bi bi-person-circle"></i>Mon Profil</a>
</x-slot>

<div class="page-header d-flex justify-content-between align-items-start">
  <div>
    <h1 class="page-title">Espace Vendeur</h1>
    <p class="page-subtitle">{{ auth()->user()->name }} · {{ auth()->user()->adresse ?? 'Localisation non définie' }}</p>
  </div>
  <a href="{{ route('vendeur.produits.create') }}" class="btn btn-primary">
    <i class="bi bi-plus-circle me-1"></i>Nouveau produit
  </a>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
  @foreach([
    ['Produits totaux',  $stats['produits'],        'bi-box-seam',       '#16a34a','#f0fdf4'],
    ['Produits actifs',  $stats['produits_actifs'],  'bi-check-circle',   '#6366f1','#eef2ff'],
    ['Commandes reçues', $stats['commandes'],        'bi-receipt-cutoff', '#f59e0b','#fffbeb'],
    ['CA Total (FCFA)',  number_format($stats['ca_total']), 'bi-cash-stack','#ef4444','#fef2f2'],
  ] as [$l,$v,$i,$c,$bg])
  <div class="col-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-icon" style="background:{{ $bg }};color:{{ $c }};"><i class="bi {{ $i }}"></i></div>
      <div><div class="stat-value">{{ $v }}</div><div class="stat-label">{{ $l }}</div></div>
    </div>
  </div>
  @endforeach
</div>

<div class="row g-3">
  {{-- Produits récents --}}
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-box-seam me-2 text-primary"></i>Mes derniers produits</span>
        <a href="{{ route('vendeur.produits.index') }}" class="btn btn-sm btn-outline-primary">Voir tout</a>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table mb-0">
            <thead><tr><th></th><th>Produit</th><th>Prix</th><th>Stock</th><th>Statut</th><th></th></tr></thead>
            <tbody>
              @forelse($mes_produits as $p)
              <tr>
                <td style="width:48px;">
                  @if($p->image_principale)
                    <img src="{{ Storage::url($p->image_principale) }}" width="36" height="36"
                         style="border-radius:.5rem;object-fit:cover;">
                  @else
                    <div style="width:36px;height:36px;border-radius:.5rem;background:#f1f5f9;display:flex;align-items:center;justify-content:center;">
                      <i class="bi bi-image text-secondary"></i>
                    </div>
                  @endif
                </td>
                <td>
                  <div class="fw-600 fs-sm">{{ $p->nom }}</div>
                  @if($p->localisation)<div class="text-secondary fs-xs"><i class="bi bi-geo-alt"></i> {{ $p->localisation }}</div>@endif
                </td>
                <td>
                  <div class="fw-600">{{ number_format($p->prix) }} F</div>
                  @if($p->prix_tontine)<div class="fs-xs text-primary"><i class="bi bi-people-fill"></i> {{ number_format($p->prix_tontine) }} F</div>@endif
                </td>
                <td>
                  <span class="fw-600 {{ $p->stock < 5 ? 'text-danger' : ($p->stock < 15 ? 'text-warning' : 'text-success') }}">
                    {{ $p->stock }}
                  </span>
                </td>
                <td>
                  @php $sc=['actif'=>['#dcfce7','#14532d'],'inactif'=>['#f1f5f9','#475569'],'rupture'=>['#fee2e2','#991b1b']]; @endphp
                  <span class="status-badge" style="background:{{ ($sc[$p->statut]??['#f1f5f9','#475569'])[0] }};color:{{ ($sc[$p->statut]??['#f1f5f9','#475569'])[1] }}">
                    {{ ucfirst($p->statut) }}
                  </span>
                </td>
                <td>
                  <a href="{{ route('vendeur.produits.edit', $p) }}" class="btn btn-icon btn-outline-secondary">
                    <i class="bi bi-pencil fs-xs"></i>
                  </a>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="6">
                  <div class="empty-state py-4">
                    <i class="bi bi-box-seam"></i>
                    <h5>Aucun produit</h5>
                    <a href="{{ route('vendeur.produits.create') }}" class="btn btn-primary btn-sm">Ajouter un produit</a>
                  </div>
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  {{-- Quick actions + conseils --}}
  <div class="col-lg-4 d-flex flex-column gap-3">
    <div class="card">
      <div class="card-header"><i class="bi bi-lightning me-2 text-primary"></i>Actions rapides</div>
      <div class="card-body d-flex flex-column gap-2">
        <a href="{{ route('vendeur.produits.create') }}" class="btn btn-primary text-start">
          <i class="bi bi-plus-circle me-2"></i>Ajouter un produit
        </a>
        <a href="{{ route('vendeur.produits.index') }}" class="btn btn-outline-secondary text-start">
          <i class="bi bi-pencil-square me-2"></i>Gérer mes produits
        </a>
        <a href="{{ route('tontines.create') }}" class="btn btn-outline-secondary text-start">
          <i class="bi bi-people-fill me-2"></i>Créer une tontine
        </a>
        <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary text-start">
          <i class="bi bi-geo-alt me-2"></i>Mettre à jour ma localisation
        </a>
      </div>
    </div>

    <div class="card border-0" style="background:linear-gradient(135deg,#064e3b,#065f46);">
      <div class="card-body p-4 text-white">
        <div style="font-size:2rem; margin-bottom:.75rem;">💡</div>
        <h6 class="fw-700 mb-2">Conseil du jour</h6>
        <p class="fs-sm opacity-75 mb-3">Activez le <strong>prix tontine</strong> pour vos produits et augmentez vos ventes de 30% grâce aux achats groupés.</p>
        <a href="{{ route('vendeur.produits.index') }}" class="btn btn-sm btn-light fw-600">
          Configurer les prix <i class="bi bi-arrow-right ms-1"></i>
        </a>
      </div>
    </div>
  </div>
</div>
</x-dashboard-layout>
