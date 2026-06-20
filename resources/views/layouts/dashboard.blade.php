<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} — NjangiMarket</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>

<nav class="navbar navbar-light bg-white border-bottom shadow-sm">
    <div class="container-fluid px-4">
        <button class="btn btn-sm me-3 d-lg-none" id="sidebarToggle">
            <i class="bi bi-list fs-5"></i>
        </button>
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
            <span style="color:#1a6b3c; font-size:1.3rem;">🛒</span> NjangiMarket
        </a>
        <div class="ms-auto d-flex align-items-center gap-3">
            <span class="badge badge-role-{{ auth()->user()->role }} text-white px-2 py-1">
                {{ ucfirst(auth()->user()->role) }}
            </span>
            <div class="dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center gap-1" href="#" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle fs-5"></i>
                    <span class="d-none d-sm-inline">{{ auth()->user()->name }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person"></i> Profil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right"></i> Déconnexion
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<div class="d-flex" style="min-height: calc(100vh - 56px);">
    <div class="sidebar p-3" style="width:240px; flex-shrink:0;" id="sidebar">
        <div class="mb-3 px-2">
            <small class="text-white-50 text-uppercase fw-bold" style="font-size:0.7rem;">Navigation</small>
        </div>
        {{ $sidebar }}
    </div>

    <main class="flex-grow-1 p-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{ $slot }}
    </main>
</div>

@stack('scripts')
<script>
document.getElementById('sidebarToggle')?.addEventListener('click', () => {
    document.getElementById('sidebar').classList.toggle('d-none');
});
</script>
</body>
</html>
