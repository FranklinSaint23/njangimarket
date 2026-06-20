<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Connexion — NjangiMarket</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="auth-bg">
  <div class="auth-card">

    {{-- Header --}}
    <div class="auth-card-header">
      <div class="auth-logo">🛒</div>
      <h1 class="auth-title">Bon retour !</h1>
      <p class="auth-subtitle">Connectez-vous à votre espace NjangiMarket</p>
    </div>

    {{-- Body --}}
    <div class="auth-card-body">
      @if(session('status'))
        <div class="alert alert-info mb-4">{{ session('status') }}</div>
      @endif

      <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-4">
          <label for="email" class="form-label">Adresse email</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
            <input id="email" type="email" name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}" required autofocus
                   placeholder="vous@exemple.com">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>

        <div class="mb-4">
          <div class="d-flex justify-content-between">
            <label for="password" class="form-label">Mot de passe</label>
            @if(Route::has('password.request'))
              <a href="{{ route('password.request') }}" class="fs-xs text-primary text-decoration-none">Oublié ?</a>
            @endif
          </div>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input id="password" type="password" name="password"
                   class="form-control @error('password') is-invalid @enderror"
                   required placeholder="••••••••">
            <button type="button" class="btn btn-outline-secondary border-start-0"
                    onclick="this.previousElementSibling.type==='password'?(this.previousElementSibling.type='text',this.querySelector('i').className='bi bi-eye-slash'):(this.previousElementSibling.type='password',this.querySelector('i').className='bi bi-eye')">
              <i class="bi bi-eye"></i>
            </button>
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>

        <div class="form-check mb-4">
          <input class="form-check-input" type="checkbox" name="remember" id="remember">
          <label class="form-check-label fs-sm" for="remember">Se souvenir de moi</label>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2 fw-600">
          Se connecter <i class="bi bi-arrow-right ms-1"></i>
        </button>
      </form>

      <div class="text-center mt-4 fs-sm">
        Pas encore de compte ?
        <a href="{{ route('register') }}" class="text-primary fw-600 text-decoration-none">Créer un compte</a>
      </div>

      {{-- Comptes de demo --}}
      <div class="mt-4 p-3 rounded-xl" style="background:#f8fafc; border:1px dashed #cbd5e1;">
        <p class="fs-xs fw-600 text-secondary mb-2 text-uppercase">Comptes de démo</p>
        <div class="d-flex flex-wrap gap-1">
          @foreach(['admin','vendeur','client','livreur'] as $r)
            <button type="button" class="btn btn-sm btn-outline-secondary fs-xs demo-login"
                    data-email="{{ $r }}@njangimarket.cm" data-pw="password">
              {{ ucfirst($r) }}
            </button>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</div>
<script>
  document.querySelectorAll('.demo-login').forEach(b => b.addEventListener('click', () => {
    document.getElementById('email').value = b.dataset.email;
    document.getElementById('password').value = b.dataset.pw;
  }));
</script>
</body>
</html>
