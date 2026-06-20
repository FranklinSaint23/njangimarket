<x-dashboard-layout>
<x-slot name="title">Mes Livraisons</x-slot>

<x-slot name="sidebar">
  <a class="nav-link" href="{{ route('livreur.dashboard') }}"><i class="bi bi-speedometer2"></i>Dashboard</a>
  <div class="sidebar-section-label">Livraisons</div>
  <a class="nav-link active" href="{{ route('livreur.livraisons.index') }}"><i class="bi bi-truck"></i>Mes livraisons</a>
  <div class="sidebar-section-label">Compte</div>
  <a class="nav-link" href="{{ route('profile.edit') }}"><i class="bi bi-person-circle"></i>Mon Profil</a>
</x-slot>

<div class="page-header">
  <h1 class="page-title">Mes Livraisons</h1>
  <p class="page-subtitle">Livraisons en cours et disponibles</p>
</div>

@if(session('success'))
  <div class="alert alert-success mb-4 d-flex align-items-center gap-2">
    <i class="bi bi-check-circle-fill"></i>{{ session('success') }}
  </div>
@endif

{{-- Commandes disponibles à accepter --}}
@if($disponibles->count())
<h2 style="font-size:1rem;font-weight:700;margin-bottom:1rem;">
  <span class="badge me-2" style="background:#fffbeb;color:#d97706;font-weight:700;">{{ $disponibles->count() }}</span>
  Livraisons disponibles à accepter
</h2>
<div class="row g-3 mb-5">
  @foreach($disponibles as $cmd)
  <div class="col-md-6 col-xl-4">
    <div class="card h-100" style="border:2px solid #bbf7d0;">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between mb-2">
          <div class="fw-600">{{ $cmd->reference }}</div>
          <span class="fw-700 text-success fs-sm">+{{ number_format($cmd->total * 0.10) }} F</span>
        </div>
        <div class="fs-sm text-secondary mb-1">
          <i class="bi bi-person"></i> {{ $cmd->client->name }}
        </div>
        <div class="fs-sm text-secondary mb-3">
          <i class="bi bi-geo-alt text-primary"></i>
          {{ Str::limit($cmd->adresse_livraison ?? 'Adresse non définie', 50) }}
        </div>
        <form method="POST" action="{{ route('livreur.livraisons.accepter', $cmd) }}">
          @csrf
          <button type="submit" class="btn btn-primary w-100 fw-600 btn-sm">
            <i class="bi bi-truck me-1"></i>Accepter cette livraison
          </button>
        </form>
      </div>
    </div>
  </div>
  @endforeach
</div>
@endif

{{-- Mes livraisons --}}
<h2 style="font-size:1rem;font-weight:700;margin-bottom:1rem;">Mes livraisons</h2>
<div class="card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table mb-0">
        <thead><tr><th>Commande</th><th>Client</th><th>Commission</th><th>Bonus</th><th>Statut</th><th>Date</th><th></th></tr></thead>
        <tbody>
          @forelse($mes_livraisons as $lv)
          <tr>
            <td class="fw-600 text-primary">{{ $lv->commande->reference }}</td>
            <td class="fs-sm">{{ $lv->commande->client->name }}</td>
            <td class="fw-600 text-success">{{ number_format($lv->commission) }} F</td>
            <td class="text-secondary fs-sm">{{ number_format($lv->bonus_satisfaction) }} F</td>
            <td>
              @php $sc=['assignee'=>['#fff7ed','#ea580c'],'en_route'=>['#fffbeb','#d97706'],'livree'=>['#f0fdf4','#16a34a']]; [$bg,$c]=$sc[$lv->statut]??['#f1f5f9','#64748b']; @endphp
              <span class="status-badge" style="background:{{ $bg }};color:{{ $c }};">{{ ucfirst($lv->statut) }}</span>
            </td>
            <td class="fs-xs text-secondary">{{ $lv->created_at->format('d/m/Y') }}</td>
            <td>
              @if(in_array($lv->statut, ['assignee','en_route']))
              <form method="POST" action="{{ route('livreur.livraisons.livrer', $lv) }}">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-success btn-sm fw-600"
                        onclick="return confirm('Marquer cette livraison comme effectuée ?')">
                  <i class="bi bi-check2-all me-1"></i>Livrée
                </button>
              </form>
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7">
              <div class="empty-state py-4">
                <i class="bi bi-truck"></i>
                <h5>Aucune livraison</h5>
                <p class="fs-sm text-secondary">Acceptez une livraison ci-dessus pour commencer.</p>
              </div>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@if($mes_livraisons->hasPages())
  <div class="mt-4">{{ $mes_livraisons->links() }}</div>
@endif
</x-dashboard-layout>
