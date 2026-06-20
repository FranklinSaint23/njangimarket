<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tontine extends Model
{
    protected $fillable = [
        'createur_id', 'nom', 'description', 'montant_cotisation',
        'nb_membres_max', 'fond_total', 'statut', 'date_debut', 'date_fin',
    ];

    protected function casts(): array
    {
        return [
            'montant_cotisation' => 'float',
            'fond_total' => 'float',
            'date_debut' => 'date',
            'date_fin' => 'date',
        ];
    }

    public function createur()   { return $this->belongsTo(User::class, 'createur_id'); }
    public function membres()    { return $this->hasMany(TontineMembre::class); }
    public function cotisations(){ return $this->hasMany(Cotisation::class); }
    public function commandes()  { return $this->hasMany(Commande::class); }

    public function getNbMembresAttribute(): int
    {
        return $this->membres()->where('statut', 'actif')->count();
    }
}
