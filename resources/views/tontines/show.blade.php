<x-dashboard-layout>
<x-slot name="title">{{ $tontine->nom }}</x-slot>

<x-slot name="sidebar">
  <a class="nav-link" href="{{ auth()->user()->isVendeur() ? route('vendeur.dashboard') : route('client.dashboard') }}">
    <i class="bi bi-house"></i>Accueil
  </a>
  <div class="sidebar-section-label">Tontines</div>
  <a class="nav-link active" href="{{ route('tontines.index') }}"><i class="bi bi-people-fill"></i>Mes Tontines</a>
  <a class="nav-link" href="{{ route('tontines.create') }}"><i class="bi bi-plus-circle"></i>Créer une tontine</a>
</x-slot>

<div class="page-header d-flex justify-content-between align-items-start">
  <div>
    <h1 class="page-title">{{ $tontine->nom }}</h1>
    <p class="page-subtitle">Créée par {{ $tontine->createur->name }} · {{ $tontine->date_debut->format('d/m/Y') }} → {{ $tontine->date_fin->format('d/m/Y') }}</p>
  </div>
  @php $sc = ['ouvert'=>['#f0fdf4','#16a34a'],'en_cours'=>['#fffbeb','#d97706'],'clos'=>['#f1f5f9','#64748b']]; [$sbg,$sc2] = $sc[$tontine->statut] ?? ['#f1f5f9','#64748b']; @endphp
  <span class="status-badge" style="background:{{ $sbg }};color:{{ $sc2 }};padding:.5rem 1rem;font-size:.85rem;">{{ ucfirst($tontine->statut) }}</span>
</div>

@if(session('success'))
  <div class="alert alert-success mb-4 d-flex align-items-center gap-2">
    <i class="bi bi-check-circle-fill"></i>{{ session('success') }}
  </div>
@endif
@if(session('error'))
  <div class="alert alert-danger mb-4 d-flex align-items-center gap-2">
    <i class="bi bi-exclamation-circle-fill"></i>{{ session('error') }}
  </div>
@endif

{{-- Stats --}}
<div class="row g-3 mb-4">
  @foreach([
    ['Fond total', number_format($total_cotise).' FCFA', 'bi-cash-stack', '#16a34a','#f0fdf4'],
    ['Membres actifs', $tontine->nb_membres.'/'.$tontine->nb_membres_max, 'bi-people-fill', '#6366f1','#eef2ff'],
    ['Cotisation', number_format($tontine->montant_cotisation).' FCFA', 'bi-wallet2', '#f59e0b','#fffbeb'],
    ['Mes cotisations', number_format($mon_total_cotise).' FCFA', 'bi-person-check', '#ef4444','#fef2f2'],
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
  {{-- Cotiser --}}
  <div class="col-lg-4 d-flex flex-column gap-3">

    @if($est_membre && in_array($tontine->statut, ['ouvert', 'en_cours']))
    <div class="card">
      <div class="card-header"><i class="bi bi-wallet2 me-2 text-primary"></i>Cotiser</div>
      <div class="card-body">

        {{-- Progression quota personnel --}}
        @php $pct_perso = $tontine->montant_cotisation > 0 ? min(100, round($mon_total_cotise / $tontine->montant_cotisation * 100)) : 0; @endphp
        <div class="mb-3">
          <div class="d-flex justify-content-between fs-xs mb-1">
            <span class="text-secondary">Mon quota</span>
            <span class="fw-600">{{ $pct_perso }}%</span>
          </div>
          <div style="height:8px;background:#f1f5f9;border-radius:99px;overflow:hidden;">
            <div style="height:100%;width:{{ $pct_perso }}%;background:{{ $pct_perso >= 100 ? '#16a34a' : '#6366f1' }};border-radius:99px;transition:width .3s;"></div>
          </div>
          <div class="d-flex justify-content-between fs-xs mt-1">
            <span class="text-secondary">Cotisé : <strong>{{ number_format($mon_total_cotise) }} F</strong></span>
            <span class="{{ $restant_cotisation > 0 ? 'text-primary' : 'text-success' }} fw-600">
              @if($restant_cotisation > 0)
                Restant : {{ number_format($restant_cotisation) }} F
              @else
                Quota complet ✓
              @endif
            </span>
          </div>
        </div>

        @if($restant_cotisation > 0)
        <form method="POST" action="{{ route('tontines.cotiser', $tontine) }}">
          @csrf
          <div class="mb-3">
            <label class="form-label">Montant à cotiser (FCFA)</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-cash"></i></span>
              <input type="number" name="montant" class="form-control"
                     value="{{ $restant_cotisation }}" min="1" max="{{ $restant_cotisation }}" required>
              <span class="input-group-text">FCFA</span>
            </div>
            <p class="fs-xs text-secondary mt-1">Maximum autorisé : {{ number_format($restant_cotisation) }} FCFA</p>
          </div>
          <button type="submit" class="btn btn-primary w-100 fw-600">
            <i class="bi bi-plus-circle me-1"></i>Cotiser maintenant
          </button>
        </form>
        @else
        <div class="alert alert-success mb-0 d-flex align-items-center gap-2">
          <i class="bi bi-check-circle-fill"></i>
          Vous avez complété votre cotisation de {{ number_format($tontine->montant_cotisation) }} FCFA.
        </div>
        @endif
      </div>
    </div>
    @endif

    @if(!$est_membre && in_array($tontine->statut, ['ouvert', 'en_cours']) && $tontine->nb_membres < $tontine->nb_membres_max)
    <div class="card" style="border:2px solid #16a34a;">
      <div class="card-body p-4 text-center">
        <div style="font-size:2.5rem;margin-bottom:.75rem;">🤝</div>
        <h6 class="fw-700 mb-2">Rejoindre cette tontine</h6>
        <p class="fs-sm text-secondary mb-3">Cotisez {{ number_format($tontine->montant_cotisation) }} FCFA et économisez 15% sur vos achats groupés.</p>
        <form method="POST" action="{{ route('tontines.rejoindre', $tontine) }}">
          @csrf
          <button type="submit" class="btn btn-primary w-100 fw-600">
            <i class="bi bi-people-fill me-1"></i>Rejoindre
          </button>
        </form>
      </div>
    </div>
    @endif

    {{-- Progression --}}
    <div class="card">
      <div class="card-header"><i class="bi bi-bar-chart me-2 text-primary"></i>Progression</div>
      <div class="card-body">
        @php $pct = $tontine->nb_membres_max > 0 ? round($tontine->nb_membres / $tontine->nb_membres_max * 100) : 0; @endphp
        <div class="mb-3">
          <div class="d-flex justify-content-between fs-xs mb-1">
            <span>Membres</span><span class="fw-600">{{ $pct }}%</span>
          </div>
          <div style="height:8px;background:#f1f5f9;border-radius:99px;">
            <div style="height:100%;width:{{ $pct }}%;background:#16a34a;border-radius:99px;"></div>
          </div>
          <div class="fs-xs text-secondary mt-1">{{ $tontine->nb_membres }}/{{ $tontine->nb_membres_max }} membres</div>
        </div>
        @if($tontine->description)
        <p class="fs-sm text-secondary mb-0">{{ $tontine->description }}</p>
        @endif
      </div>
    </div>
  </div>

  <div class="col-lg-8 d-flex flex-column gap-3">
    {{-- Membres --}}
    <div class="card">
      <div class="card-header"><i class="bi bi-people me-2 text-primary"></i>Membres ({{ $tontine->nb_membres }})</div>
      <div class="card-body p-0">
        @forelse($tontine->membres as $m)
        <div class="d-flex align-items-center gap-3 px-4 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
          <div class="avatar" style="background:{{ $m->user_id === $tontine->createur_id ? '#16a34a' : '#6366f1' }};">
            {{ strtoupper(substr($m->user->name,0,1)) }}
          </div>
          <div class="flex-grow-1">
            <div class="fw-600 fs-sm">{{ $m->user->name }}
              @if($m->user_id === $tontine->createur_id)
                <span class="fs-xs ms-1" style="color:#16a34a;">★ Créateur</span>
              @endif
            </div>
            <div class="fs-xs text-secondary">Rejoint le {{ $m->rejoint_le?->format('d/m/Y') }}</div>
          </div>
          @php $tot = $tontine->cotisations->where('user_id', $m->user_id)->sum('montant'); @endphp
          <div class="text-end">
            <div class="fw-700 fs-sm text-success">{{ number_format($tot) }} F</div>
            <div class="fs-xs text-secondary">cotisé</div>
          </div>
        </div>
        @empty
        <div class="py-4 text-center text-secondary fs-sm">Aucun membre</div>
        @endforelse
      </div>
    </div>

    {{-- Commandes en attente de paiement tontine (créateur uniquement) --}}
    @if($commandes_en_attente->count() && auth()->id() === $tontine->createur_id)
    <div class="card" style="border:2px solid #fbbf24;">
      <div class="card-header d-flex align-items-center gap-2" style="background:#fffbeb;">
        <i class="bi bi-hourglass-split text-warning"></i>
        <span class="fw-700">Commandes en attente de paiement ({{ $commandes_en_attente->count() }})</span>
        <span class="ms-auto fs-xs text-secondary">Fond disponible : <strong class="text-success">{{ number_format($tontine->fond_total) }} FCFA</strong></span>
      </div>
      <div class="card-body p-0">
        @foreach($commandes_en_attente as $cmd)
        @php $suffisant = $tontine->fond_total >= $cmd->total; @endphp
        <div class="d-flex align-items-center gap-3 px-4 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
          <div class="flex-grow-1">
            <div class="fw-600 fs-sm">{{ $cmd->reference }}</div>
            <div class="fs-xs text-secondary">
              <i class="bi bi-person"></i> {{ $cmd->client->name }}
              · {{ $cmd->items->count() }} article(s)
              · {{ $cmd->created_at->format('d/m/Y') }}
            </div>
          </div>
          <div class="text-end me-3">
            <div class="fw-700 {{ $suffisant ? 'text-success' : 'text-danger' }}">{{ number_format($cmd->total) }} FCFA</div>
            <div class="fs-xs text-secondary">{{ $suffisant ? 'Payable' : 'Fonds insuffisants' }}</div>
          </div>
          @if($suffisant)
          <form method="POST" action="{{ route('tontines.payer_commande', [$tontine, $cmd]) }}"
                onsubmit="return confirm('Débiter {{ number_format($cmd->total) }} FCFA du fond pour payer la commande {{ $cmd->reference }} ?')">
            @csrf
            <button type="submit" class="btn btn-sm btn-success fw-600">
              <i class="bi bi-cash-stack me-1"></i>Payer
            </button>
          </form>
          @else
          <span class="btn btn-sm btn-outline-secondary disabled">
            <i class="bi bi-lock"></i>
          </span>
          @endif
        </div>
        @endforeach
      </div>
    </div>
    @endif

    {{-- Historique cotisations --}}
    @if($tontine->cotisations->count())
    <div class="card">
      <div class="card-header"><i class="bi bi-clock-history me-2 text-primary"></i>Historique des cotisations</div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table mb-0 fs-sm">
            <thead><tr><th>Membre</th><th>Montant</th><th>Statut</th><th>Date</th></tr></thead>
            <tbody>
              @foreach($tontine->cotisations->sortByDesc('created_at')->take(20) as $c)
              <tr>
                <td class="fw-600">{{ $c->user->name }}</td>
                <td class="text-success fw-600">{{ number_format($c->montant) }} FCFA</td>
                <td><span class="status-badge" style="background:#f0fdf4;color:#16a34a;">{{ ucfirst($c->statut) }}</span></td>
                <td class="text-secondary">{{ $c->created_at->format('d/m/Y H:i') }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
    @endif

    {{-- Commandes payées via cette tontine --}}
    @if($commandes_payees->count())
    <div class="card">
      <div class="card-header"><i class="bi bi-bag-check me-2 text-success"></i>Achats financés par la tontine</div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table mb-0 fs-sm">
            <thead><tr><th>Référence</th><th>Membre</th><th>Montant</th><th>Date</th></tr></thead>
            <tbody>
              @foreach($commandes_payees as $cmd)
              <tr>
                <td class="fw-600 text-primary">{{ $cmd->reference }}</td>
                <td>{{ $cmd->client->name }}</td>
                <td class="fw-600 text-success">{{ number_format($cmd->total) }} FCFA</td>
                <td class="text-secondary">{{ $cmd->created_at->format('d/m/Y') }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
    @endif
  </div>
</div>
</x-dashboard-layout>
