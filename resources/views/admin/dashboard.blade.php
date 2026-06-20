<x-dashboard-layout>
<x-slot name="title">Dashboard Admin</x-slot>

<x-slot name="sidebar">
  <a class="nav-link active" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i>Dashboard</a>
  <a class="nav-link" href="{{ route('admin.users.index') }}"><i class="bi bi-people"></i>Utilisateurs</a>
  <div class="sidebar-section-label">Catalogue</div>
  <a class="nav-link" href="#"><i class="bi bi-grid-3x3-gap"></i>Catégories</a>
  <a class="nav-link" href="#"><i class="bi bi-box-seam"></i>Produits</a>
  <div class="sidebar-section-label">Commerce</div>
  <a class="nav-link" href="#"><i class="bi bi-receipt-cutoff"></i>Commandes</a>
  <a class="nav-link" href="#"><i class="bi bi-truck"></i>Livraisons</a>
  <a class="nav-link" href="{{ route('tontines.index') }}"><i class="bi bi-people-fill"></i>Tontines</a>
  <div class="sidebar-section-label">Paramètres</div>
  <a class="nav-link" href="{{ route('profile.edit') }}"><i class="bi bi-sliders"></i>Paramètres</a>
</x-slot>

{{-- Page header --}}
<div class="page-header d-flex justify-content-between align-items-start">
  <div>
    <h1 class="page-title">Dashboard</h1>
    <p class="page-subtitle">Bienvenue, {{ auth()->user()->name }} · {{ now()->format('d M Y') }}</p>
  </div>
  <a href="{{ route('admin.users.index') }}" class="btn btn-primary">
    <i class="bi bi-person-plus me-1"></i>Ajouter utilisateur
  </a>
</div>

{{-- Stats ─────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
  @foreach([
    ['Utilisateurs',  $stats['utilisateurs'], 'bi-people-fill',       '#6366f1', '#eef2ff', '+' . $stats['clients'] . ' clients'],
    ['Produits',      $stats['produits'],      'bi-box-seam',          '#16a34a', '#f0fdf4', $stats['vendeurs'] . ' vendeurs'],
    ['Commandes',     $stats['commandes'],     'bi-receipt-cutoff',    '#f59e0b', '#fffbeb', 'Toutes périodes'],
    ['CA Total',      number_format($stats['ca_total']) . ' F', 'bi-graph-up-arrow', '#ef4444', '#fef2f2', 'Payées uniquement'],
  ] as [$label, $val, $icon, $color, $bg, $sub])
  <div class="col-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-icon" style="background:{{ $bg }}; color:{{ $color }};">
        <i class="bi {{ $icon }}"></i>
      </div>
      <div>
        <div class="stat-value">{{ $val }}</div>
        <div class="stat-label">{{ $label }}</div>
        <div class="stat-change" style="color:{{ $color }}; margin-top:2px;">{{ $sub }}</div>
      </div>
    </div>
  </div>
  @endforeach
</div>

<div class="row g-3 mb-4">
  {{-- Chart commandes --}}
  <div class="col-lg-8">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-bar-chart-line me-2 text-primary"></i>Activité des commandes</span>
        <span class="badge" style="background:#f0fdf4; color:#16a34a;">7 derniers jours</span>
      </div>
      <div class="card-body">
        <canvas id="commandesChart" height="90"></canvas>
      </div>
    </div>
  </div>

  {{-- Répartition rôles --}}
  <div class="col-lg-4">
    <div class="card h-100">
      <div class="card-header"><i class="bi bi-pie-chart me-2 text-primary"></i>Répartition</div>
      <div class="card-body d-flex flex-column justify-content-center">
        <canvas id="rolesChart" height="180"></canvas>
        <div class="d-flex flex-column gap-2 mt-3">
          @foreach([
            ['Clients',  $stats['clients'],  '#16a34a'],
            ['Vendeurs', $stats['vendeurs'], '#2563eb'],
            ['Livreurs', $stats['livreurs'], '#f59e0b'],
          ] as [$l, $v, $c])
          <div class="d-flex align-items-center justify-content-between fs-sm">
            <div class="d-flex align-items-center gap-2">
              <div style="width:10px;height:10px;border-radius:50%;background:{{ $c }};flex-shrink:0;"></div>
              <span class="text-secondary">{{ $l }}</span>
            </div>
            <span class="fw-600">{{ $v }}</span>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row g-3">
  {{-- Derniers utilisateurs --}}
  <div class="col-lg-5">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-person-badge me-2 text-primary"></i>Derniers inscrits</span>
        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary">Voir tout</a>
      </div>
      <div class="card-body p-0">
        @forelse($derniers_users as $u)
        <div class="d-flex align-items-center gap-3 px-4 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
          <div class="avatar" style="background:{{ ['admin'=>'#6366f1','vendeur'=>'#2563eb','client'=>'#16a34a','livreur'=>'#f59e0b'][$u->role] }};">
            {{ strtoupper(substr($u->name,0,1)) }}
          </div>
          <div class="flex-grow-1 min-w-0">
            <div class="fw-600 fs-sm text-truncate">{{ $u->name }}</div>
            <div class="text-secondary fs-xs text-truncate">{{ $u->email }}</div>
          </div>
          <span class="badge badge-role-{{ $u->role }} flex-shrink-0">{{ ucfirst($u->role) }}</span>
        </div>
        @empty
        <div class="empty-state"><i class="bi bi-people"></i><p>Aucun utilisateur</p></div>
        @endforelse
      </div>
    </div>
  </div>

  {{-- Dernières commandes --}}
  <div class="col-lg-7">
    <div class="card h-100">
      <div class="card-header"><i class="bi bi-receipt-cutoff me-2 text-primary"></i>Dernières commandes</div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table mb-0">
            <thead>
              <tr>
                <th>Réf.</th><th>Client</th><th>Total</th><th>Paiement</th><th>Statut</th>
              </tr>
            </thead>
            <tbody>
              @forelse($dernieres_commandes as $cmd)
              <tr>
                <td class="fw-600 text-primary">{{ $cmd->reference }}</td>
                <td>{{ $cmd->client->name }}</td>
                <td class="fw-600">{{ number_format($cmd->total) }}&nbsp;F</td>
                <td>
                  @php $pm=['orange_money'=>['#ea580c','Orange'],'mtn_money'=>['#ca8a04','MTN'],'tontine'=>['#7c3aed','Tontine']]; @endphp
                  <span class="status-badge" style="background:{{ ($pm[$cmd->methode_paiement]??['#94a3b8','—'])[0] }}20; color:{{ ($pm[$cmd->methode_paiement]??['#94a3b8','—'])[0] }}">
                    {{ ($pm[$cmd->methode_paiement]??['','—'])[1] }}
                  </span>
                </td>
                <td><span class="status-badge status-{{ $cmd->statut }}">{{ str_replace('_',' ',ucfirst($cmd->statut)) }}</span></td>
              </tr>
              @empty
              <tr><td colspan="5" class="text-center py-4 text-secondary">Aucune commande</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Bar chart – commandes 7 jours
const days = Array.from({length:7},(_,i)=>{
  const d=new Date(); d.setDate(d.getDate()-6+i);
  return d.toLocaleDateString('fr-FR',{weekday:'short',day:'numeric'});
});
new Chart(document.getElementById('commandesChart'), {
  type: 'bar',
  data: {
    labels: days,
    datasets: [{
      label: 'Commandes',
      data: [3,5,2,8,6,9,4],
      backgroundColor: 'rgba(22,163,74,.15)',
      borderColor: '#16a34a',
      borderWidth: 2,
      borderRadius: 6,
    },{
      label: 'CA (×1000 F)',
      data: [45,78,30,120,95,140,62],
      backgroundColor: 'rgba(99,102,241,.12)',
      borderColor: '#6366f1',
      borderWidth: 2,
      borderRadius: 6,
      type: 'line',
      yAxisID: 'y2',
      tension: .4,
      fill: true,
    }]
  },
  options: {
    responsive:true, maintainAspectRatio:true,
    plugins:{legend:{position:'top',labels:{usePointStyle:true,font:{family:'Inter',size:11}}}},
    scales:{
      x:{grid:{display:false},ticks:{font:{family:'Inter',size:11}}},
      y:{beginAtZero:true,grid:{color:'#f1f5f9'},ticks:{font:{family:'Inter',size:11}}},
      y2:{beginAtZero:true,position:'right',grid:{display:false},ticks:{font:{family:'Inter',size:11}}},
    }
  }
});

// Doughnut – rôles
new Chart(document.getElementById('rolesChart'), {
  type: 'doughnut',
  data: {
    labels: ['Clients','Vendeurs','Livreurs'],
    datasets:[{
      data: [{{ $stats['clients'] }}, {{ $stats['vendeurs'] }}, {{ $stats['livreurs'] }}],
      backgroundColor: ['#16a34a','#2563eb','#f59e0b'],
      borderWidth: 0,
      hoverOffset: 4,
    }]
  },
  options: {
    cutout:'70%',
    plugins:{legend:{display:false}},
  }
});
</script>
@endpush
</x-dashboard-layout>
