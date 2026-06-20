<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'role',
        'avatar', 'adresse', 'latitude', 'longitude', 'actif',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'actif' => 'boolean',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function isAdmin(): bool    { return $this->role === 'admin'; }
    public function isVendeur(): bool  { return $this->role === 'vendeur'; }
    public function isClient(): bool   { return $this->role === 'client'; }
    public function isLivreur(): bool  { return $this->role === 'livreur'; }

    public function produits()    { return $this->hasMany(Produit::class, 'vendeur_id'); }
    public function commandes()   { return $this->hasMany(Commande::class, 'client_id'); }
    public function livraisons()  { return $this->hasMany(Livraison::class, 'livreur_id'); }
    public function tontines()    { return $this->hasMany(Tontine::class, 'createur_id'); }
    public function cotisations() { return $this->hasMany(Cotisation::class); }
    public function avis()        { return $this->hasMany(Avis::class); }
}
