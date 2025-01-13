<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ArticleFactureAchat extends Pivot
{
    protected $fillable = [
        'article_id',
        'facture_achat_id',
        'quantite',
        'prix_achat'
    ];
}
