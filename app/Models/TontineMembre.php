<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TontineMembre extends Model
{
    protected $fillable = ['tontine_id', 'user_id', 'statut', 'rejoint_le'];

    protected function casts(): array
    {
        return ['rejoint_le' => 'datetime'];
    }

    public function tontine() { return $this->belongsTo(Tontine::class); }
    public function user()    { return $this->belongsTo(User::class); }
}
