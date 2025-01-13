<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fournisseur extends Model
{
    protected $fillable = [
        'nom',
        'telephone'
    ];

    public function factureAchats(): HasMany
    {
        return $this->hasMany(FactureAchat::class);
    }
}
