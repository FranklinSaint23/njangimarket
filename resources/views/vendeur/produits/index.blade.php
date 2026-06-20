<x-dashboard-layout>
<x-slot name="title">Mes Produits</x-slot>

<x-slot name="sidebar">
  <a class="nav-link" href="{{ route('vendeur.dashboard') }}"><i class="bi bi-speedometer2"></i>Dashboard</a>
  <div class="sidebar-section-label">Catalogue</div>
  <a class="nav-link active" href="{{ route('vendeur.produits.index') }}"><i class="bi bi-box-seam"></i>Mes Produits</a>
  <a class="nav-link" href="{{ route('vendeur.produits.create') }}"><i class="bi bi-plus-circle"></i>Ajouter un produit</a>
  <div class="sidebar-section-label">Commerce</div>
  <a class="nav-link" href="#"><i class="bi bi-receipt-cutoff"></i>Commandes reçues</a>
  <a class="nav-link" href="{{ route('tontines.index') }}"><i class="bi bi-people-fill"></i>Mes Tontines</a>
  <div class="sidebar-section-label">Compte</div>
  <a class="nav-link" href="{{ route('profile.edit') }}"><i class="bi bi-person-circle"></i>Mon Profil</a>
</x-slot>

<div class="page-header d-flex justify-content-between align-items-start">
  <div>
    <h1 class="page-title">Mes Produits</h1>
    <p class="page-subtitle">Gérez votre catalogue</p>
  </div>
  <a href="{{ route('vendeur.produits.create') }}" class="btn btn-primary">
    <i class="bi bi-plus-circle me-1"></i>Nouveau produit
  </a>
</div>

@if(session('success'))
  <div class="alert alert-success mb-4 d-flex align-items-center gap-2">
    <i class="bi bi-check-circle-fill"></i>{{ session('success') }}
  </div>
@endif

{{-- Filtres rapides --}}
<div class="d-flex gap-2 flex-wrap mb-4">
  <button class="btn btn-primary btn-sm rounded-pill filter-btn active" data-filter="all">Tous</button>
  <button class="btn btn-outline-secondary btn-sm rounded-pill filter-btn" data-filter="actif">Actifs</button>
  <button class="btn btn-outline-secondary btn-sm rounded-pill filter-btn" data-filter="inactif">Inactifs</button>
  <button class="btn btn-outline-secondary btn-sm rounded-pill filter-btn" data-filter="rupture">Rupture</button>
</div>

<div class="card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table mb-0" id="produitsTable">
        <thead>
          <tr>
            <th style="width:48px;"></th>
            <th>Produit</th>
            <th>Catégorie</th>
            <th>Prix</th>
            <th>Stock</th>
            <th>Statut</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($produits as $produit)
          <tr class="produit-row" data-statut="{{ $produit->statut }}">
            <td>
              @if($produit->image_principale)
                <img src="{{ Storage::url($produit->image_principale) }}" width="40" height="40"
                     style="border-radius:.5rem;object-fit:cover;">
              @else
                <div style="width:40px;height:40px;border-radius:.5rem;background:#f1f5f9;display:flex;align-items:center;justify-content:center;">
                  <i class="bi bi-image text-secondary fs-sm"></i>
                </div>
              @endif
            </td>
            <td>
              <div class="fw-600">{{ $produit->nom }}</div>
              @if($produit->localisation)
                <div class="fs-xs text-secondary"><i class="bi bi-geo-alt"></i> {{ $produit->localisation }}</div>
              @endif
            </td>
            <td class="text-secondary fs-sm">{{ $produit->categorie?->nom ?? '—' }}</td>
            <td>
              <div class="fw-600">{{ number_format($produit->prix) }} F</div>
              @if($produit->prix_tontine)
                <div class="fs-xs" style="color:#7c3aed;"><i class="bi bi-people-fill"></i> {{ number_format($produit->prix_tontine) }} F</div>
              @endif
            </td>
            <td>
              <span class="fw-600 {{ $produit->stock < 5 ? 'text-danger' : ($produit->stock < 10 ? 'text-warning' : 'text-success') }}">
                {{ $produit->stock }}
              </span>
            </td>
            <td>
              @php $sc=['actif'=>['#dcfce7','#14532d'],'inactif'=>['#f1f5f9','#475569'],'rupture'=>['#fee2e2','#991b1b']]; @endphp
              <span class="status-badge" style="background:{{ ($sc[$produit->statut]??['#f1f5f9','#475569'])[0] }};color:{{ ($sc[$produit->statut]??['#f1f5f9','#475569'])[1] }}">
                {{ ucfirst($produit->statut) }}
              </span>
            </td>
            <td>
              <div class="d-flex gap-1">
                <a href="{{ route('vendeur.produits.edit', $produit) }}" class="btn btn-icon btn-outline-primary" title="Modifier">
                  <i class="bi bi-pencil fs-xs"></i>
                </a>
                <form method="POST" action="{{ route('vendeur.produits.destroy', $produit) }}">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-icon btn-outline-danger" title="Supprimer"
                          onclick="return confirm('Supprimer « {{ addslashes($produit->nom) }} » ?')">
                    <i class="bi bi-trash fs-xs"></i>
                  </button>
                </form>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7">
              <div class="empty-state py-5">
                <i class="bi bi-box-seam"></i>
                <h5>Aucun produit</h5>
                <p class="text-secondary fs-sm">Commencez par ajouter votre premier produit.</p>
                <a href="{{ route('vendeur.produits.create') }}" class="btn btn-primary btn-sm">
                  <i class="bi bi-plus-circle me-1"></i>Ajouter un produit
                </a>
              </div>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

@if($produits->hasPages())
<div class="mt-4">{{ $produits->links() }}</div>
@endif

@push('scripts')
<script>
document.querySelectorAll('.filter-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    document.querySelectorAll('.filter-btn').forEach(b => {
      b.classList.remove('active','btn-primary');
      b.classList.add('btn-outline-secondary');
    });
    this.classList.add('active','btn-primary');
    this.classList.remove('btn-outline-secondary');

    const filter = this.dataset.filter;
    document.querySelectorAll('.produit-row').forEach(row => {
      row.style.display = (filter === 'all' || row.dataset.statut === filter) ? '' : 'none';
    });
  });
});
</script>
@endpush
</x-dashboard-layout>
