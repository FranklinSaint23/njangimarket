<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Avis extends Model
{
    protected $fillable = [
        'user_id', 'produit_id', 'livreur_id', 'commande_id',
        'note', 'commentaire', 'type',
    ];

    public function user()    { return $this->belongsTo(User::class); }
    public function produit() { return $this->belongsTo(Produit::class); }
    public function livreur() { return $this->belongsTo(User::class, 'livreur_id'); }
}
