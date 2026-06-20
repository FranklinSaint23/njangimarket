<x-dashboard-layout>
<x-slot name="title">Statut du paiement</x-slot>

<x-slot name="sidebar">
  <a class="nav-link" href="{{ route('client.dashboard') }}"><i class="bi bi-house"></i>Accueil</a>
  <a class="nav-link active" href="{{ route('client.commandes.index') }}"><i class="bi bi-receipt-cutoff"></i>Mes Commandes</a>
  <a class="nav-link" href="{{ route('tontines.index') }}"><i class="bi bi-people-fill"></i>Tontines</a>
</x-slot>

<div class="row justify-content-center mt-4">
  <div class="col-md-6 col-lg-5">
    <div class="card">
      <div class="card-body p-5 text-center">
        @if($commande->statut === 'payee')
          <div style="font-size:4rem;margin-bottom:1rem;">✅</div>
          <h4 class="fw-700 text-success mb-2">Paiement confirmé !</h4>
          <p class="text-secondary mb-1">Commande <strong>{{ $commande->reference }}</strong></p>
          <p class="text-secondary fs-sm mb-4">Votre commande est maintenant en cours de traitement.</p>
          <a href="{{ route('client.commandes.show', $commande) }}" class="btn btn-primary w-100">
            <i class="bi bi-receipt-cutoff me-1"></i>Voir ma commande
          </a>

        @elseif(in_array($commande->statut, ['annulee']))
          <div style="font-size:4rem;margin-bottom:1rem;">❌</div>
          <h4 class="fw-700 text-danger mb-2">Paiement échoué</h4>
          <p class="text-secondary mb-4">La transaction a été annulée ou a expiré. Veuillez réessayer.</p>
          <a href="{{ route('paiement.initier', $commande) }}" class="btn btn-primary w-100 mb-2">
            <i class="bi bi-arrow-clockwise me-1"></i>Réessayer
          </a>
          <a href="{{ route('client.dashboard') }}" class="btn btn-outline-secondary w-100">Retour</a>

        @else
          <div style="font-size:4rem;margin-bottom:1rem;">⏳</div>
          <h4 class="fw-700 mb-2" style="color:#f59e0b;">Paiement en attente</h4>
          <p class="text-secondary mb-4">
            Vérifiez votre téléphone et confirmez le paiement de
            <strong>{{ number_format($commande->total) }} FCFA</strong>
            avec votre PIN Mobile Money.
          </p>
          <div class="mb-4 p-3 rounded-xl" style="background:#fffbeb;border:1px solid #fcd34d;">
            <div class="fs-sm text-secondary">
              <i class="bi bi-clock text-warning me-1"></i>
              La page se rafraîchit automatiquement…
            </div>
          </div>
          <a href="{{ route('paiement.statut', $commande) }}" class="btn btn-primary w-100 mb-2">
            <i class="bi bi-arrow-clockwise me-1"></i>Vérifier le statut
          </a>
          <a href="{{ route('client.commandes.show', $commande) }}" class="btn btn-outline-secondary w-100">
            Voir la commande
          </a>
        @endif
      </div>
    </div>
  </div>
</div>

@if($commande->statut === 'paiement_en_cours')
@push('scripts')
<script>
  // Auto-refresh après 10 secondes
  setTimeout(() => location.reload(), 10000);
</script>
@endpush
@endif
</x-dashboard-layout>
