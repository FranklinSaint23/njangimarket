<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cotisation extends Model
{
    protected $fillable = [
        'tontine_id', 'user_id', 'montant', 'methode',
        'statut', 'reference_transaction',
    ];

    protected function casts(): array
    {
        return ['montant' => 'float'];
    }

    public function tontine() { return $this->belongsTo(Tontine::class); }
    public function user()    { return $this->belongsTo(User::class); }
}
