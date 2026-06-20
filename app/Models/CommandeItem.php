<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommandeItem extends Model
{
    protected $fillable = ['commande_id', 'produit_id', 'quantite', 'prix_unitaire', 'sous_total'];

    protected function casts(): array
    {
        return ['prix_unitaire' => 'float', 'sous_total' => 'float'];
    }

    public function commande() { return $this->belongsTo(Commande::class); }
    public function produit()  { return $this->belongsTo(Produit::class); }
}
