<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\VenteController;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\FactureAchatController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [VenteController::class, 'index'])->name('home');

Route::get('/ventes/by-date', [VenteController::class, 'getVentesByDate'])->name('ventes.by-date');
Route::resource('ventes', VenteController::class)->only(['store', 'destroy']);

Route::resource('articles', ArticleController::class);
Route::resource('fournisseurs', FournisseurController::class)->only(['index', 'store', 'update', 'destroy']);
Route::resource('facture-achats', FactureAchatController::class)->only(['index', 'store', 'update', 'destroy']);
