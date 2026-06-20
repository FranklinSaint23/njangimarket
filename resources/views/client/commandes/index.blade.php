<x-dashboard-layout>
<x-slot name="title">Mes Commandes</x-slot>

<x-slot name="sidebar">
  <a class="nav-link" href="{{ route('client.dashboard') }}"><i class="bi bi-house"></i>Accueil</a>
  <a class="nav-link" href="{{ route('client.panier.index') }}"><i class="bi bi-cart3"></i>Mon Panier</a>
  <a class="nav-link active" href="{{ route('client.commandes.index') }}"><i class="bi bi-receipt-cutoff"></i>Mes Commandes</a>
  <a class="nav-link" href="{{ route('tontines.index') }}"><i class="bi bi-people-fill"></i>Tontines</a>
  <div class="sidebar-section-label">Compte</div>
  <a class="nav-link" href="{{ route('profile.edit') }}"><i class="bi bi-person-circle"></i>Mon Profil</a>
</x-slot>

<div class="page-header">
  <h1 class="page-title">Mes Commandes</h1>
  <p class="page-subtitle">Historique de vos achats</p>
</div>

@if($commandes->isEmpty())
  <div class="empty-state">
    <i class="bi bi-receipt-cutoff"></i>
    <h5>Aucune commande</h5>
    <p>Vous n'avez pas encore passé de commande.</p>
    <a href="{{ route('client.dashboard') }}" class="btn btn-primary">
      <i class="bi bi-shop me-1"></i>Voir les produits
    </a>
  </div>
@else
<div class="card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table mb-0">
        <thead>
          <tr><th>Référence</th><th>Articles</th><th>Total</th><th>Paiement</th><th>Statut</th><th>Date</th><th></th></tr>
        </thead>
        <tbody>
          @foreach($commandes as $cmd)
          @php
            $pm=['orange_money'=>['#ea580c','Orange'],'mtn_money'=>['#ca8a04','MTN'],'tontine'=>['#7c3aed','Tontine']];
            [$pc,$pl] = $pm[$cmd->methode_paiement] ?? ['#64748b','—'];
          @endphp
          <tr>
            <td class="fw-600 text-primary">{{ $cmd->reference }}</td>
            <td class="text-secondary fs-sm">{{ $cmd->items->count() }} article(s)</td>
            <td class="fw-600">{{ number_format($cmd->total) }} F</td>
            <td>
              <span class="status-badge" style="background:{{ $pc }}18;color:{{ $pc }};">{{ $pl }}</span>
            </td>
            <td><span class="status-badge status-{{ $cmd->statut }}">{{ str_replace('_',' ',ucfirst($cmd->statut)) }}</span></td>
            <td class="text-secondary fs-xs">{{ $cmd->created_at->format('d/m/Y') }}</td>
            <td class="d-flex gap-1">
              <a href="{{ route('client.commandes.show', $cmd) }}" class="btn btn-icon btn-outline-secondary">
                <i class="bi bi-eye fs-xs"></i>
              </a>
              @if($cmd->statut === 'en_attente')
              <form method="POST" action="{{ route('client.commandes.destroy', $cmd) }}"
                    onsubmit="return confirm('Annuler la commande {{ $cmd->reference }} ?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-icon btn-outline-danger" title="Annuler la commande">
                  <i class="bi bi-trash fs-xs"></i>
                </button>
              </form>
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@if($commandes->hasPages())
  <div class="mt-4">{{ $commandes->links() }}</div>
@endif
@endif
</x-dashboard-layout>
