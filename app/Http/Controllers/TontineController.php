<?php

namespace App\Http\Controllers;

use App\Models\Tontine;
use App\Models\TontineMembre;
use App\Models\Cotisation;
use App\Models\Commande;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TontineController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Tontines dont l'utilisateur est créateur ou membre
        $mes_tontines = Tontine::with(['createur', 'membres'])
            ->where('createur_id', $user->id)
            ->orWhereHas('membres', fn($q) => $q->where('user_id', $user->id))
            ->latest()
            ->get();

        // Tontines disponibles (actives, pas encore rejoint, pas encore pleines)
        $tontines_disponibles = Tontine::with(['createur', 'membres'])
            ->where('statut', 'ouvert')
            ->where('createur_id', '!=', $user->id)
            ->whereDoesntHave('membres', fn($q) => $q->where('user_id', $user->id))
            ->get()
            ->filter(fn($t) => $t->nb_membres < $t->nb_membres_max);

        return view('tontines.index', compact('mes_tontines', 'tontines_disponibles'));
    }

    public function create()
    {
        return view('tontines.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom'               => 'required|string|max:150',
            'description'       => 'nullable|string|max:500',
            'montant_cotisation'=> 'required|numeric|min:500',
            'nb_membres_max'    => 'required|integer|min:2|max:50',
            'date_debut'        => 'required|date|after_or_equal:today',
            'date_fin'          => 'required|date|after:date_debut',
        ]);

        $tontine = Tontine::create([
            'createur_id'       => auth()->id(),
            'nom'               => $request->nom,
            'description'       => $request->description,
            'montant_cotisation'=> $request->montant_cotisation,
            'nb_membres_max'    => $request->nb_membres_max,
            'fond_total'        => 0,
            'statut'            => 'ouvert',
            'date_debut'        => $request->date_debut,
            'date_fin'          => $request->date_fin,
        ]);

        // Le créateur devient automatiquement membre
        TontineMembre::create([
            'tontine_id' => $tontine->id,
            'user_id'    => auth()->id(),
            'statut'     => 'actif',
            'rejoint_le' => now(),
        ]);

        return redirect()->route('tontines.show', $tontine)
            ->with('success', "Tontine « {$tontine->nom} » créée avec succès !");
    }

    public function show(Tontine $tontine)
    {
        $tontine->load(['createur', 'membres.user', 'cotisations.user']);

        $user = auth()->user();
        $est_membre = $tontine->membres->where('user_id', $user->id)->isNotEmpty();
        $mon_membre = $tontine->membres->firstWhere('user_id', $user->id);

        $total_cotise = $tontine->cotisations->sum('montant');
        $mes_cotisations = $tontine->cotisations->where('user_id', $user->id);
        $mon_total_cotise = $mes_cotisations->where('statut', 'confirme')->sum('montant');
        $restant_cotisation = max(0, $tontine->montant_cotisation - $mon_total_cotise);

        // Commandes tontine en attente (visibles par le créateur pour approbation)
        $commandes_en_attente = Commande::where('tontine_id', $tontine->id)
            ->where('statut', 'en_attente')
            ->with(['client', 'items'])
            ->latest()
            ->get();

        // Commandes déjà payées via cette tontine
        $commandes_payees = Commande::where('tontine_id', $tontine->id)
            ->where('statut', 'payee')
            ->with('client')
            ->latest()
            ->take(10)
            ->get();

        return view('tontines.show', compact(
            'tontine', 'est_membre', 'mon_membre', 'total_cotise', 'mes_cotisations',
            'mon_total_cotise', 'restant_cotisation',
            'commandes_en_attente', 'commandes_payees'
        ));
    }

    public function payerCommande(Tontine $tontine, Commande $commande)
    {
        abort_unless($tontine->createur_id === auth()->id(), 403);

        if ($commande->tontine_id !== $tontine->id) {
            return back()->with('error', 'Cette commande n\'appartient pas à cette tontine.');
        }
        if ($commande->statut !== 'en_attente') {
            return back()->with('error', 'Cette commande a déjà été traitée.');
        }
        if ($tontine->fond_total < $commande->total) {
            return back()->with('error',
                'Fonds insuffisants. Disponible : ' . number_format($tontine->fond_total) .
                ' FCFA — Besoin : ' . number_format($commande->total) . ' FCFA.'
            );
        }

        DB::transaction(function () use ($tontine, $commande) {
            $tontine->decrement('fond_total', $commande->total);
            $commande->update(['statut' => 'payee']);
        });

        return back()->with('success',
            "Commande {$commande->reference} de {$commande->client->name} payée — " .
            number_format($commande->total) . ' FCFA débités du fond.'
        );
    }

    public function rejoindre(Request $request, Tontine $tontine)
    {
        $user = auth()->user();

        if ($tontine->statut !== 'ouvert') {
            return back()->with('error', 'Cette tontine n\'est plus active.');
        }
        if ($tontine->nb_membres >= $tontine->nb_membres_max) {
            return back()->with('error', 'Cette tontine est complète.');
        }
        if ($tontine->membres()->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'Vous êtes déjà membre de cette tontine.');
        }

        TontineMembre::create([
            'tontine_id' => $tontine->id,
            'user_id'    => $user->id,
            'statut'     => 'actif',
            'rejoint_le' => now(),
        ]);

        return redirect()->route('tontines.show', $tontine)
            ->with('success', "Vous avez rejoint la tontine « {$tontine->nom} » !");
    }

    public function cotiser(Request $request, Tontine $tontine)
    {
        $request->validate([
            'montant' => 'required|numeric|min:1',
        ]);

        $user = auth()->user();

        if (!$tontine->membres()->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'Vous n\'êtes pas membre de cette tontine.');
        }

        $deja_cotise = $tontine->cotisations()
            ->where('user_id', $user->id)
            ->where('statut', 'confirme')
            ->sum('montant');

        $restant = $tontine->montant_cotisation - $deja_cotise;

        if ($restant <= 0) {
            return back()->with('error', 'Vous avez déjà atteint votre quota de cotisation (' . number_format($tontine->montant_cotisation) . ' FCFA).');
        }

        // Plafonner le montant à ce qui reste
        $montant = min((int) $request->montant, (int) $restant);

        DB::transaction(function () use ($montant, $tontine, $user) {
            Cotisation::create([
                'tontine_id' => $tontine->id,
                'user_id'    => $user->id,
                'montant'    => $montant,
                'statut'     => 'confirme',
            ]);

            $tontine->increment('fond_total', $montant);
        });

        $msg = number_format($montant) . ' FCFA cotisés avec succès !';
        if ($montant < (int) $request->montant) {
            $msg .= ' (Montant ajusté : quota atteint.)';
        }

        return back()->with('success', $msg);
    }

    public function destroy(Tontine $tontine)
    {
        abort_unless($tontine->createur_id === auth()->id(), 403);
        $tontine->delete();
        return redirect()->route('tontines.index')
            ->with('success', 'Tontine supprimée.');
    }
}
