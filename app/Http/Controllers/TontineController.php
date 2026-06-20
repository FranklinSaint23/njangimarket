<?php

namespace App\Http\Controllers;

use App\Models\Tontine;
use App\Models\TontineMembre;
use App\Models\Cotisation;
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
        $tontine->load(['createur', 'membres.user', 'cotisations.user', 'commandes.client']);

        $user = auth()->user();
        $est_membre = $tontine->membres->where('user_id', $user->id)->isNotEmpty();
        $mon_membre = $tontine->membres->firstWhere('user_id', $user->id);

        $total_cotise = $tontine->cotisations->sum('montant');
        $mes_cotisations = $tontine->cotisations->where('user_id', $user->id);

        return view('tontines.show', compact(
            'tontine', 'est_membre', 'mon_membre', 'total_cotise', 'mes_cotisations'
        ));
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

        DB::transaction(function () use ($request, $tontine, $user) {
            Cotisation::create([
                'tontine_id' => $tontine->id,
                'user_id'    => $user->id,
                'montant'    => $request->montant,
                'statut'     => 'confirme',
            ]);

            $tontine->increment('fond_total', $request->montant);
        });

        return back()->with('success', number_format($request->montant) . ' FCFA cotisés avec succès !');
    }

    public function destroy(Tontine $tontine)
    {
        abort_unless($tontine->createur_id === auth()->id(), 403);
        $tontine->delete();
        return redirect()->route('tontines.index')
            ->with('success', 'Tontine supprimée.');
    }
}
