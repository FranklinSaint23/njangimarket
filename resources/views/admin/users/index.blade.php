<x-dashboard-layout>
<x-slot name="title">Gestion des utilisateurs</x-slot>

<x-slot name="sidebar">
  <a class="nav-link" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i>Dashboard</a>
  <a class="nav-link active" href="{{ route('admin.users.index') }}"><i class="bi bi-people"></i>Utilisateurs</a>
  <div class="sidebar-section-label">Catalogue</div>
  <a class="nav-link" href="#"><i class="bi bi-grid-3x3-gap"></i>Catégories</a>
  <a class="nav-link" href="#"><i class="bi bi-box-seam"></i>Produits</a>
  <div class="sidebar-section-label">Commerce</div>
  <a class="nav-link" href="#"><i class="bi bi-receipt-cutoff"></i>Commandes</a>
  <a class="nav-link" href="{{ route('tontines.index') }}"><i class="bi bi-people-fill"></i>Tontines</a>
  <div class="sidebar-section-label">Paramètres</div>
  <a class="nav-link" href="{{ route('profile.edit') }}"><i class="bi bi-sliders"></i>Profil</a>
</x-slot>

<div class="page-header d-flex justify-content-between align-items-start">
  <div>
    <h1 class="page-title">Utilisateurs</h1>
    <p class="page-subtitle">{{ $stats['total'] }} comptes enregistrés</p>
  </div>
  <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
    <i class="bi bi-person-plus me-1"></i>Nouvel utilisateur
  </a>
</div>

@if(session('success'))
  <div class="alert alert-success mb-4 d-flex align-items-center gap-2">
    <i class="bi bi-check-circle-fill"></i>{{ session('success') }}
  </div>
@endif

{{-- Stats --}}
<div class="row g-3 mb-4">
  @foreach([
    ['Total',    $stats['total'],    'bi-people-fill', '#6366f1','#eef2ff'],
    ['Clients',  $stats['clients'],  'bi-bag-heart',   '#16a34a','#f0fdf4'],
    ['Vendeurs', $stats['vendeurs'], 'bi-shop',        '#2563eb','#eff6ff'],
    ['Livreurs', $stats['livreurs'], 'bi-truck',       '#f59e0b','#fffbeb'],
  ] as [$l,$v,$i,$c,$bg])
  <div class="col-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-icon" style="background:{{ $bg }};color:{{ $c }};"><i class="bi {{ $i }}"></i></div>
      <div><div class="stat-value">{{ $v }}</div><div class="stat-label">{{ $l }}</div></div>
    </div>
  </div>
  @endforeach
</div>

{{-- Filtres --}}
<div class="card mb-0">
  <div class="card-body p-3">
    <form method="GET" class="d-flex gap-2 flex-wrap align-items-center">
      <div class="input-group" style="max-width:240px;">
        <span class="input-group-text"><i class="bi bi-search fs-xs text-secondary"></i></span>
        <input type="text" name="search" class="form-control" placeholder="Nom, email…"
               value="{{ request('search') }}">
      </div>
      <select name="role" class="form-select" style="width:auto;">
        <option value="">Tous les rôles</option>
        @foreach(['admin','vendeur','client','livreur'] as $r)
          <option value="{{ $r }}" {{ request('role')===$r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
        @endforeach
      </select>
      <button type="submit" class="btn btn-primary btn-sm">Filtrer</button>
      @if(request()->hasAny(['search','role']))
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">Réinitialiser</a>
      @endif
    </form>
  </div>
</div>

<div class="card mt-0" style="border-top:0;border-radius:0 0 1rem 1rem;">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table mb-0">
        <thead>
          <tr><th></th><th>Utilisateur</th><th>Rôle</th><th>Téléphone</th><th>Statut</th><th>Inscription</th><th>Actions</th></tr>
        </thead>
        <tbody>
          @forelse($users as $u)
          @php $roleColors=['admin'=>'#6366f1','vendeur'=>'#2563eb','client'=>'#16a34a','livreur'=>'#f59e0b']; @endphp
          <tr>
            <td style="width:44px;">
              <div class="avatar" style="background:{{ $roleColors[$u->role] ?? '#64748b' }};width:34px;height:34px;font-size:.8rem;">
                {{ strtoupper(substr($u->name,0,1)) }}
              </div>
            </td>
            <td>
              <div class="fw-600 fs-sm">{{ $u->name }}</div>
              <div class="fs-xs text-secondary">{{ $u->email }}</div>
            </td>
            <td>
              <span class="badge badge-role-{{ $u->role }}">{{ ucfirst($u->role) }}</span>
            </td>
            <td class="fs-sm text-secondary">{{ $u->phone ?? '—' }}</td>
            <td>
              @if($u->actif)
                <span class="status-badge" style="background:#f0fdf4;color:#16a34a;">Actif</span>
              @else
                <span class="status-badge" style="background:#fef2f2;color:#ef4444;">Inactif</span>
              @endif
            </td>
            <td class="fs-xs text-secondary">{{ $u->created_at->format('d/m/Y') }}</td>
            <td>
              <div class="d-flex gap-1">
                <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-icon btn-outline-primary" title="Modifier">
                  <i class="bi bi-pencil fs-xs"></i>
                </a>
                @if($u->id !== auth()->id())
                <form method="POST" action="{{ route('admin.users.destroy', $u) }}">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-icon btn-outline-danger" title="Supprimer"
                          onclick="return confirm('Supprimer {{ addslashes($u->name) }} ?')">
                    <i class="bi bi-trash fs-xs"></i>
                  </button>
                </form>
                @endif
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7">
              <div class="empty-state py-4">
                <i class="bi bi-people"></i>
                <h5>Aucun utilisateur trouvé</h5>
              </div>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

@if($users->hasPages())
  <div class="mt-4">{{ $users->links() }}</div>
@endif
</x-dashboard-layout>
