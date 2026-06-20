<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Inscription — NjangiMarket</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="auth-bg" style="padding: 2rem 1rem;">
  <div class="auth-card" style="max-width:480px;">

    <div class="auth-card-header">
      <div class="auth-logo">🛒</div>
      <h1 class="auth-title">Créer un compte</h1>
      <p class="auth-subtitle">Rejoignez la marketplace locale du Cameroun</p>
    </div>

    <div class="auth-card-body">
      <form method="POST" action="{{ route('register') }}">
        @csrf

        {{-- Rôle --}}
        <div class="mb-4">
          <label class="form-label">Je souhaite...</label>
          <div class="row g-2">
            @foreach([
              'client'  => ['Acheter',  'bi-bag-heart',  '#16a34a'],
              'vendeur' => ['Vendre',   'bi-shop',       '#2563eb'],
              'livreur' => ['Livrer',   'bi-bicycle',    '#d97706'],
            ] as $r => [$label, $icon, $color])
            <div class="col-4">
              <input type="radio" class="role-option" name="role" id="role_{{ $r }}"
                     value="{{ $r }}" {{ old('role','client') === $r ? 'checked' : '' }} required>
              <label class="role-label w-100" for="role_{{ $r }}" style="--role-color:{{ $color }}">
                <i class="bi {{ $icon }}" style="color:{{ $color }}"></i>
                {{ $label }}
              </label>
            </div>
            @endforeach
          </div>
          @error('role')<div class="text-danger fs-xs mt-1">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
          <label for="name" class="form-label">Nom complet</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-person"></i></span>
            <input id="name" type="text" name="name"
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name') }}" required autofocus placeholder="Jean Dupont">
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>

        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
            <input id="email" type="email" name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}" required placeholder="vous@exemple.com">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>

        <div class="mb-3">
          <label for="phone" class="form-label">
            Téléphone <span class="text-secondary fw-400">(optionnel)</span>
          </label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-phone"></i></span>
            <input id="phone" type="tel" name="phone" class="form-control"
                   value="{{ old('phone') }}" placeholder="+237 6XX XXX XXX">
          </div>
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Mot de passe</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input id="password" type="password" name="password"
                   class="form-control @error('password') is-invalid @enderror"
                   required placeholder="Minimum 8 caractères">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>

        <div class="mb-4">
          <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
            <input id="password_confirmation" type="password" name="password_confirmation"
                   class="form-control" required placeholder="••••••••">
          </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2 fw-600">
          Créer mon compte <i class="bi bi-arrow-right ms-1"></i>
        </button>
      </form>

      <div class="text-center mt-4 fs-sm">
        Déjà un compte ?
        <a href="{{ route('login') }}" class="text-primary fw-600 text-decoration-none">Se connecter</a>
      </div>
    </div>
  </div>
</div>
</body>
</html>
