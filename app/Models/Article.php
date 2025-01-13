<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'prix'
    ];

    public function ventes(): HasMany
    {
        return $this->hasMany(Vente::class);
    }

    public function factureAchats(): BelongsToMany
    {
        return $this->belongsToMany(FactureAchat::class, 'article_facture_achat')
            ->withPivot('quantite', 'prix_achat')
            ->withTimestamps();
    }
}
