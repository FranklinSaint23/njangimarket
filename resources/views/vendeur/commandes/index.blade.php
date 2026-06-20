<x-dashboard-layout>
<x-slot name="title">Commandes reçues</x-slot>

<x-slot name="sidebar">
  <a class="nav-link" href="{{ route('vendeur.dashboard') }}"><i class="bi bi-speedometer2"></i>Dashboard</a>
  <div class="sidebar-section-label">Catalogue</div>
  <a class="nav-link" href="{{ route('vendeur.produits.index') }}"><i class="bi bi-box-seam"></i>Mes Produits</a>
  <a class="nav-link" href="{{ route('vendeur.produits.create') }}"><i class="bi bi-plus-circle"></i>Ajouter un produit</a>
  <div class="sidebar-section-label">Commerce</div>
  <a class="nav-link active" href="{{ route('vendeur.commandes.index') }}"><i class="bi bi-receipt-cutoff"></i>Commandes reçues</a>
  <a class="nav-link" href="{{ route('tontines.index') }}"><i class="bi bi-people-fill"></i>Tontines</a>
  <div class="sidebar-section-label">Compte</div>
  <a class="nav-link" href="{{ route('profile.edit') }}"><i class="bi bi-person-circle"></i>Mon Profil</a>
</x-slot>

<div class="page-header d-flex justify-content-between align-items-start">
  <div>
    <h1 class="page-title">Commandes reçues</h1>
    <p class="page-subtitle">Commandes contenant vos produits</p>
  </div>
</div>

@if(session('success'))
  <div class="alert alert-success mb-4 d-flex align-items-center gap-2">
    <i class="bi bi-check-circle-fill"></i>{{ session('success') }}
  </div>
@endif

{{-- Filtres --}}
<div class="card mb-0">
  <div class="card-body p-3">
    <form method="GET" class="d-flex gap-2 flex-wrap align-items-center">
      <select name="statut" class="form-select" style="width:auto;">
        <option value="">Tous les statuts</option>
        @foreach(['en_attente','payee','confirmee','en_preparation','en_livraison','livree','annulee'] as $s)
          <option value="{{ $s }}" {{ request('statut')===$s ? 'selected' : '' }}>{{ str_replace('_',' ',ucfirst($s)) }}</option>
        @endforeach
      </select>
      <button type="submit" class="btn btn-primary btn-sm">Filtrer</button>
      @if(request('statut'))
        <a href="{{ route('vendeur.commandes.index') }}" class="btn btn-outline-secondary btn-sm">Réinitialiser</a>
      @endif
    </form>
  </div>
</div>

<div class="card mt-0" style="border-top:0;border-radius:0 0 1rem 1rem;">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table mb-0">
        <thead>
          <tr><th>Référence</th><th>Client</th><th>Articles</th><th>Total</th><th>Paiement</th><th>Statut</th><th>Date</th><th></th></tr>
        </thead>
        <tbody>
          @forelse($commandes as $cmd)
          @php
            $pm=['orange_money'=>['#ea580c','Orange'],'mtn_money'=>['#ca8a04','MTN'],'tontine'=>['#7c3aed','Tontine']];
            [$pc,$pl]=$pm[$cmd->methode_paiement]??['#64748b','—'];
          @endphp
          <tr>
            <td class="fw-600 text-primary">{{ $cmd->reference }}</td>
            <td>
              <div class="fw-600 fs-sm">{{ $cmd->client->name }}</div>
              <div class="fs-xs text-secondary">{{ $cmd->client->phone ?? $cmd->client->email }}</div>
            </td>
            <td class="text-secondary fs-sm">{{ $cmd->items->count() }} art.</td>
            <td class="fw-600">{{ number_format($cmd->total) }} F</td>
            <td><span class="status-badge" style="background:{{ $pc }}18;color:{{ $pc }};">{{ $pl }}</span></td>
            <td><span class="status-badge status-{{ $cmd->statut }}">{{ str_replace('_',' ',ucfirst($cmd->statut)) }}</span></td>
            <td class="fs-xs text-secondary">{{ $cmd->created_at->format('d/m/Y') }}</td>
            <td>
              <div class="d-flex gap-1">
                <a href="{{ route('vendeur.commandes.show', $cmd) }}" class="btn btn-icon btn-outline-primary">
                  <i class="bi bi-eye fs-xs"></i>
                </a>
                @if(in_array($cmd->statut, ['payee','en_attente']))
                <form method="POST" action="{{ route('vendeur.commandes.statut', $cmd) }}">
                  @csrf @method('PATCH')
                  <input type="hidden" name="statut" value="confirmee">
                  <button type="submit" class="btn btn-icon btn-outline-success" title="Confirmer">
                    <i class="bi bi-check2 fs-xs"></i>
                  </button>
                </form>
                @endif
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="8">
              <div class="empty-state py-4">
                <i class="bi bi-receipt-cutoff"></i>
                <h5>Aucune commande</h5>
                <p class="fs-sm text-secondary">Les commandes contenant vos produits apparaîtront ici.</p>
              </div>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@if($commandes->hasPages())
  <div class="mt-4">{{ $commandes->links() }}</div>
@endif
</x-dashboard-layout>
