<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    protected $fillable = [
        'vendeur_id', 'categorie_id', 'nom', 'slug', 'description',
        'prix', 'prix_tontine', 'stock', 'image_principale', 'images',
        'latitude', 'longitude', 'localisation', 'statut',
    ];

    protected function casts(): array
    {
        return [
            'images' => 'array',
            'prix' => 'float',
            'prix_tontine' => 'float',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function vendeur()   { return $this->belongsTo(User::class, 'vendeur_id'); }
    public function categorie() { return $this->belongsTo(Categorie::class); }
    public function items()     { return $this->hasMany(CommandeItem::class); }
    public function avis()      { return $this->hasMany(Avis::class); }

    public function scopeActif($q)  { return $q->where('statut', 'actif'); }
    public function getNoteMoyenneAttribute(): float
    {
        return round($this->avis()->avg('note') ?? 0, 1);
    }
}
