<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FactureAchat extends Model
{
    protected $fillable = [
        'fournisseur_id',
        'date_achat',
        'titre'
    ];

    protected $casts = [
        'date_achat' => 'date'
    ];

    public function fournisseur(): BelongsTo
    {
        return $this->belongsTo(Fournisseur::class);
    }

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'article_facture_achat')
            ->withPivot('quantite', 'prix_achat')
            ->withTimestamps();
    }
}
