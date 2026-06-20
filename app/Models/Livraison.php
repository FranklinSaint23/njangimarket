<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Livraison extends Model
{
    protected $fillable = [
        'commande_id', 'livreur_id', 'statut',
        'latitude_livreur', 'longitude_livreur',
        'commission', 'bonus_satisfaction',
        'pris_en_charge_le', 'livre_le', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'pris_en_charge_le' => 'datetime',
            'livre_le' => 'datetime',
            'commission' => 'float',
            'bonus_satisfaction' => 'float',
            'latitude_livreur' => 'float',
            'longitude_livreur' => 'float',
        ];
    }

    public function commande() { return $this->belongsTo(Commande::class); }
    public function livreur()  { return $this->belongsTo(User::class, 'livreur_id'); }
}
