@props(['title' => 'Dashboard'])

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $title }} — NjangiMarket</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @stack('styles')
</head>
<body>

{{-- ─── TOPBAR ─────────────────────────────────────────── --}}
<header class="topbar">
  <button class="btn btn-icon me-2 d-lg-none border-0 text-secondary" id="sidebarToggle">
    <i class="bi bi-list fs-5"></i>
  </button>

  <a href="{{ route('home') }}" class="topbar-brand">
    <span>🛒</span> NjangiMarket
  </a>

  <div class="ms-auto d-flex align-items-center gap-3">
    {{-- Panier rapide pour clients --}}
    @if(auth()->user()->isClient())
    @php $nb = count(session('panier',[])); @endphp
    <a href="{{ route('client.panier.index') }}" class="btn btn-icon btn-outline-secondary border-0 position-relative text-secondary">
      <i class="bi bi-cart3 fs-5"></i>
      @if($nb) <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:.6rem;">{{ $nb }}</span> @endif
    </a>
    @endif

    {{-- Rôle badge --}}
    <span class="badge badge-role-{{ auth()->user()->role }}">
      {{ ucfirst(auth()->user()->role) }}
    </span>

    {{-- User dropdown --}}
    <div class="dropdown">
      <button class="btn border-0 p-0 d-flex align-items-center gap-2 text-dark" style="height:auto;" data-bs-toggle="dropdown">
        <div class="avatar">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</div>
        <span class="d-none d-md-inline fw-600 fs-sm">{{ auth()->user()->name }}</span>
        <i class="bi bi-chevron-down fs-xs text-secondary d-none d-md-inline"></i>
      </button>
      <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="min-width:200px; border-radius:.75rem; padding:.5rem;">
        <li class="px-3 py-2 border-bottom mb-1">
          <div class="fw-600 fs-sm">{{ auth()->user()->name }}</div>
          <div class="text-secondary" style="font-size:.75rem;">{{ auth()->user()->email }}</div>
        </li>
        <li><a class="dropdown-item rounded-1" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2 me-2 text-secondary"></i>Dashboard</a></li>
        <li><a class="dropdown-item rounded-1" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2 text-secondary"></i>Profil</a></li>
        <li><hr class="dropdown-divider my-1"></li>
        <li>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="dropdown-item rounded-1 text-danger">
              <i class="bi bi-box-arrow-right me-2"></i>Déconnexion
            </button>
          </form>
        </li>
      </ul>
    </div>
  </div>
</header>

{{-- ─── BODY WRAPPER ───────────────────────────────────── --}}
<div class="d-flex">

  {{-- SIDEBAR --}}
  <aside class="sidebar d-none d-lg-block" id="sidebar">
    <div class="sidebar-section-label">Menu</div>
    {{ $sidebar }}
  </aside>

  {{-- MAIN --}}
  <main class="main-content">
    {{-- Flash messages --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4 d-flex align-items-center gap-2" role="alert">
      <i class="bi bi-check-circle-fill"></i>
      <span>{{ session('success') }}</span>
      <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4 d-flex align-items-center gap-2" role="alert">
      <i class="bi bi-exclamation-circle-fill"></i>
      <span>{{ session('error') }}</span>
      <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('info'))
    <div class="alert alert-info alert-dismissible fade show mb-4 d-flex align-items-center gap-2" role="alert">
      <i class="bi bi-info-circle-fill"></i>
      <span>{{ session('info') }}</span>
      <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{ $slot }}
  </main>
</div>

{{-- Mobile sidebar overlay --}}
<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileSidebar" style="width:256px;background:var(--nj-sidebar-bg);">
  <div class="offcanvas-header border-bottom border-secondary">
    <span class="topbar-brand" style="font-size:1.1rem;">🛒 NjangiMarket</span>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body p-3">
    {{ $sidebar }}
  </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@stack('scripts')
<script>
  document.getElementById('sidebarToggle')?.addEventListener('click', () => {
    new bootstrap.Offcanvas(document.getElementById('mobileSidebar')).show();
  });
</script>
</body>
</html>
