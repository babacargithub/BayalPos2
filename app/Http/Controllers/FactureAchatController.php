<?php

namespace App\Http\Controllers;

use App\Models\FactureAchat;
use App\Models\Fournisseur;
use App\Models\Article;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FactureAchatController extends Controller
{
    public function index()
    {
        return Inertia::render('FactureAchats/Index', [
            'factureAchats' => FactureAchat::with(['fournisseur', 'articles'])->get(),
            'fournisseurs' => Fournisseur::all(),
            'articles' => Article::all()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fournisseur_id' => 'required|exists:fournisseurs,id',
            'date_achat' => 'required|date',
            'titre' => 'required|string|max:255',
            'articles' => 'required|array',
            'articles.*.id' => 'required|exists:articles,id',
            'articles.*.quantite' => 'required|integer|min:1',
            'articles.*.prix_achat' => 'required|numeric|min:0'
        ]);

        $factureAchat = FactureAchat::create([
            'fournisseur_id' => $validated['fournisseur_id'],
            'date_achat' => $validated['date_achat'],
            'titre' => $validated['titre']
        ]);

        foreach ($validated['articles'] as $article) {
            $factureAchat->articles()->attach($article['id'], [
                'quantite' => $article['quantite'],
                'prix_achat' => $article['prix_achat']
            ]);
        }

        return redirect()->back();
    }

    public function update(Request $request, FactureAchat $factureAchat)
    {
        $validated = $request->validate([
            'fournisseur_id' => 'required|exists:fournisseurs,id',
            'date_achat' => 'required|date',
            'titre' => 'required|string|max:255',
            'articles' => 'required|array',
            'articles.*.id' => 'required|exists:articles,id',
            'articles.*.quantite' => 'required|integer|min:1',
            'articles.*.prix_achat' => 'required|numeric|min:0'
        ]);

        $factureAchat->update([
            'fournisseur_id' => $validated['fournisseur_id'],
            'date_achat' => $validated['date_achat'],
            'titre' => $validated['titre']
        ]);

        $factureAchat->articles()->detach();
        
        foreach ($validated['articles'] as $article) {
            $factureAchat->articles()->attach($article['id'], [
                'quantite' => $article['quantite'],
                'prix_achat' => $article['prix_achat']
            ]);
        }

        return redirect()->back();
    }

    public function destroy(FactureAchat $factureAchat)
    {
        $factureAchat->delete();

        return redirect()->back();
    }
}
