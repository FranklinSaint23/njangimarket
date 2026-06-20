<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Commande extends Model
{
    protected $fillable = [
        'client_id', 'tontine_id', 'reference', 'type_livraison', 'statut',
        'methode_paiement', 'statut_paiement', 'sous_total', 'frais_livraison',
        'total', 'adresse_livraison', 'latitude_livraison', 'longitude_livraison',
        'heure_souhaitee', 'notes', 'note', 'campay_reference',
    ];

    protected function casts(): array
    {
        return [
            'heure_souhaitee' => 'datetime',
            'sous_total' => 'float',
            'frais_livraison' => 'float',
            'total' => 'float',
        ];
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($commande) {
            $commande->reference = 'NJM-' . strtoupper(Str::random(8));
        });
    }

    public function client()   { return $this->belongsTo(User::class, 'client_id'); }
    public function tontine()  { return $this->belongsTo(Tontine::class); }
    public function items()    { return $this->hasMany(CommandeItem::class); }
    public function livraison(){ return $this->hasOne(Livraison::class); }
}
