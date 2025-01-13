<?php

namespace App\Http\Controllers;

use App\Models\Vente;
use App\Models\Article;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Carbon;

class VenteController extends Controller
{
    public function index()
    {
        $ventesJour = Vente::with('article')
            ->whereDate('created_at', Carbon::today())
            ->get();

        $totalVentesJour = $ventesJour->sum(function ($vente) {
            return $vente->prix * $vente->quantite;
        });

        return Inertia::render('Ventes/Index', [
            'ventes' => $ventesJour,
            'total' => $totalVentesJour,
            'articles' => Article::all()
        ]);
    }

    public function getVentesByDate(Request $request)
    {
        $request->validate([
            'date' => 'required|date_format:Y-m-d'
        ]);

        try {
            $date = Carbon::createFromFormat('Y-m-d', $request->date)->startOfDay();
            
            $ventes = Vente::with('article')
                ->whereDate('created_at', $date)
                ->get();

            $total = $ventes->sum(function ($vente) {
                return $vente->prix * $vente->quantite;
            });

            return response()->json([
                'ventes' => $ventes,
                'total' => $total
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'ventes' => [],
                'total' => 0
            ]);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'article_id' => 'required|exists:articles,id',
            'quantite' => 'required|integer|min:1',
            'prix' => 'required|numeric|min:0'
        ]);

        Vente::create($validated);

        return redirect()->back();
    }

    public function destroy(Vente $vente)
    {
        $vente->delete();

        return redirect()->back();
    }
}
