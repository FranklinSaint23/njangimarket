<x-dashboard-layout>
<x-slot name="title">Modifier {{ $user->name }}</x-slot>

<x-slot name="sidebar">
  <a class="nav-link" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i>Dashboard</a>
  <a class="nav-link active" href="{{ route('admin.users.index') }}"><i class="bi bi-people"></i>Utilisateurs</a>
</x-slot>

<div class="page-header d-flex justify-content-between align-items-start">
  <div>
    <h1 class="page-title">Modifier l'utilisateur</h1>
    <p class="page-subtitle">{{ $user->email }}</p>
  </div>
  <span class="badge badge-role-{{ $user->role }}">{{ ucfirst($user->role) }}</span>
</div>

<div class="row justify-content-center">
  <div class="col-lg-6">
    <div class="card">
      <div class="card-header"><i class="bi bi-pencil me-2 text-primary"></i>Informations</div>
      <div class="card-body p-4">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
          @csrf @method('PUT')

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
            <label class="form-label">Téléphone</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-phone"></i></span>
              <input type="tel" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Adresse</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
              <input type="text" name="adresse" class="form-control" value="{{ old('adresse', $user->adresse) }}"
                     placeholder="Quartier, ville…">
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Rôle <span class="text-danger">*</span></label>
            <select name="role" class="form-select @error('role') is-invalid @enderror" required>
              @foreach(['client','vendeur','livreur','admin'] as $r)
                <option value="{{ $r }}" {{ old('role', $user->role) === $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
              @endforeach
            </select>
            @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="mb-3">
            <label class="form-label">
              Nouveau mot de passe
              <span class="text-secondary fw-400">(laisser vide pour ne pas changer)</span>
            </label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-lock"></i></span>
              <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                     placeholder="Minimum 8 caractères">
              @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="mb-4">
            <label class="form-label">Confirmer le mot de passe</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
              <input type="password" name="password_confirmation" class="form-control">
            </div>
          </div>

          <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" name="actif" id="actif" value="1"
                   {{ old('actif', $user->actif) ? 'checked' : '' }}>
            <label class="form-check-label" for="actif">Compte actif</label>
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-grow-1 fw-600">
              <i class="bi bi-check2-circle me-1"></i>Enregistrer
            </button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Annuler</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
</x-dashboard-layout>
