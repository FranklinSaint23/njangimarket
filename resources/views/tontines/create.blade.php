<x-dashboard-layout>
<x-slot name="title">Créer une Tontine</x-slot>

<x-slot name="sidebar">
  <a class="nav-link" href="{{ auth()->user()->isVendeur() ? route('vendeur.dashboard') : route('client.dashboard') }}">
    <i class="bi bi-house"></i>Accueil
  </a>
  <div class="sidebar-section-label">Tontines</div>
  <a class="nav-link" href="{{ route('tontines.index') }}"><i class="bi bi-people-fill"></i>Mes Tontines</a>
  <a class="nav-link active" href="{{ route('tontines.create') }}"><i class="bi bi-plus-circle"></i>Créer une tontine</a>
</x-slot>

<div class="page-header">
  <h1 class="page-title">Créer une Tontine</h1>
  <p class="page-subtitle">Organisez un groupe d'achat et économisez jusqu'à 15%</p>
</div>

<div class="row justify-content-center">
  <div class="col-lg-7">

    {{-- Info banner --}}
    <div class="card border-0 mb-4" style="background:linear-gradient(135deg,#4f46e5,#7c3aed);">
      <div class="card-body p-4 text-white d-flex gap-3">
        <div style="font-size:2rem;flex-shrink:0;">🤝</div>
        <div>
          <h6 class="fw-700 mb-1">Comment fonctionne une Tontine Njangi ?</h6>
          <ul class="mb-0 opacity-75 fs-sm" style="padding-left:1.25rem;">
            <li>Vous créez un groupe avec un montant de cotisation</li>
            <li>Les membres rejoignent et cotisent ensemble</li>
            <li>Les achats groupés bénéficient de <strong>15% de réduction</strong></li>
            <li>Les fonds sont utilisés pour passer des commandes groupées</li>
          </ul>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header"><i class="bi bi-people-fill me-2 text-primary"></i>Nouvelle tontine</div>
      <div class="card-body p-4">
        <form method="POST" action="{{ route('tontines.store') }}">
          @csrf

          <div class="mb-3">
            <label for="nom" class="form-label">Nom de la tontine <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-people-fill"></i></span>
              <input type="text" id="nom" name="nom"
                     class="form-control @error('nom') is-invalid @enderror"
                     value="{{ old('nom') }}"
                     placeholder="Ex: Tontine Marché Central Yaoundé" required>
              @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="mb-3">
            <label for="description" class="form-label">
              Description <span class="text-secondary fw-400">(optionnel)</span>
            </label>
            <textarea id="description" name="description"
                      class="form-control @error('description') is-invalid @enderror"
                      rows="3"
                      placeholder="Décrivez l'objectif de cette tontine…">{{ old('description') }}</textarea>
            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="row g-3 mb-3">
            <div class="col-sm-6">
              <label for="montant_cotisation" class="form-label">Cotisation par membre (FCFA) <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-cash"></i></span>
                <input type="number" id="montant_cotisation" name="montant_cotisation"
                       class="form-control @error('montant_cotisation') is-invalid @enderror"
                       value="{{ old('montant_cotisation', 5000) }}" min="500" step="500" required>
                <span class="input-group-text">FCFA</span>
                @error('montant_cotisation')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-sm-6">
              <label for="nb_membres_max" class="form-label">Nombre max de membres <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person-plus"></i></span>
                <input type="number" id="nb_membres_max" name="nb_membres_max"
                       class="form-control @error('nb_membres_max') is-invalid @enderror"
                       value="{{ old('nb_membres_max', 10) }}" min="2" max="50" required>
                <span class="input-group-text">membres</span>
                @error('nb_membres_max')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
          </div>

          <div class="row g-3 mb-4">
            <div class="col-sm-6">
              <label for="date_debut" class="form-label">Date de début <span class="text-danger">*</span></label>
              <input type="date" id="date_debut" name="date_debut"
                     class="form-control @error('date_debut') is-invalid @enderror"
                     value="{{ old('date_debut', date('Y-m-d')) }}" required>
              @error('date_debut')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-sm-6">
              <label for="date_fin" class="form-label">Date de fin <span class="text-danger">*</span></label>
              <input type="date" id="date_fin" name="date_fin"
                     class="form-control @error('date_fin') is-invalid @enderror"
                     value="{{ old('date_fin', date('Y-m-d', strtotime('+3 months'))) }}" required>
              @error('date_fin')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          {{-- Résumé dynamique --}}
          <div class="p-3 rounded-xl mb-4" style="background:#f0fdf4;border:1px solid #bbf7d0;">
            <div class="fw-600 fs-sm text-success mb-1"><i class="bi bi-calculator me-1"></i>Résumé prévisionnel</div>
            <div class="fs-sm text-secondary">
              Fond total estimé :
              <strong id="fondTotal" class="text-success">{{ number_format(5000 * 10) }} FCFA</strong>
              ({{ 10 }} membres × {{ number_format(5000) }} FCFA)
            </div>
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-grow-1 py-2 fw-600">
              <i class="bi bi-check2-circle me-1"></i>Créer la tontine
            </button>
            <a href="{{ route('tontines.index') }}" class="btn btn-outline-secondary">Annuler</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
const cotInput = document.getElementById('montant_cotisation');
const nbInput  = document.getElementById('nb_membres_max');
const fondEl   = document.getElementById('fondTotal');

function updateFond() {
  const total = (parseFloat(cotInput.value)||0) * (parseInt(nbInput.value)||0);
  fondEl.textContent = new Intl.NumberFormat('fr-FR').format(total) + ' FCFA';
}

cotInput.addEventListener('input', updateFond);
nbInput.addEventListener('input', updateFond);
</script>
@endpush
</x-dashboard-layout>
