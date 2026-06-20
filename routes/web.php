<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Vendeur\DashboardController as VendeurDashboard;
use App\Http\Controllers\Vendeur\ProduitController;
use App\Http\Controllers\Vendeur\CommandeController as VendeurCommandeController;
use App\Http\Controllers\Client\DashboardController as ClientDashboard;
use App\Http\Controllers\Client\CommandeController;
use App\Http\Controllers\Client\PanierController;
use App\Http\Controllers\Livreur\DashboardController as LivreurDashboard;
use App\Http\Controllers\Livreur\PositionController;
use App\Http\Controllers\Livreur\LivraisonController;
use App\Http\Controllers\TontineController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

// Page d'accueil publique
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Redirection dashboard selon rôle
Route::get('/dashboard', function () {
    $user = auth()->user();
    return match($user->role) {
        'admin'   => redirect()->route('admin.dashboard'),
        'vendeur' => redirect()->route('vendeur.dashboard'),
        'livreur' => redirect()->route('livreur.dashboard'),
        default   => redirect()->route('client.dashboard'),
    };
})->middleware(['auth'])->name('dashboard');

// Profil commun (tous rôles)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// === ADMIN ===
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
    Route::resource('users', UserController::class);
});

// === VENDEUR ===
Route::middleware(['auth', 'role:vendeur'])->prefix('vendeur')->name('vendeur.')->group(function () {
    Route::get('/dashboard', [VendeurDashboard::class, 'index'])->name('dashboard');
    Route::resource('produits', ProduitController::class);
    Route::get('/commandes', [VendeurCommandeController::class, 'index'])->name('commandes.index');
    Route::get('/commandes/{commande}', [VendeurCommandeController::class, 'show'])->name('commandes.show');
    Route::patch('/commandes/{commande}/statut', [VendeurCommandeController::class, 'updateStatut'])->name('commandes.statut');
});

// === CLIENT ===
Route::middleware(['auth', 'role:client'])->prefix('client')->name('client.')->group(function () {
    Route::get('/dashboard', [ClientDashboard::class, 'index'])->name('dashboard');
    Route::resource('commandes', CommandeController::class)->only(['index', 'create', 'store', 'show', 'destroy']);

    // Panier
    Route::get('/panier', [PanierController::class, 'index'])->name('panier.index');
    Route::post('/panier/ajouter/{produit}', [PanierController::class, 'ajouter'])->name('panier.ajouter');
    Route::patch('/panier/modifier/{produit}', [PanierController::class, 'modifier'])->name('panier.modifier');
    Route::delete('/panier/supprimer/{produit}', [PanierController::class, 'supprimer'])->name('panier.supprimer');
    Route::delete('/panier/vider', [PanierController::class, 'vider'])->name('panier.vider');
});

// === LIVREUR ===
Route::middleware(['auth', 'role:livreur'])->prefix('livreur')->name('livreur.')->group(function () {
    Route::get('/dashboard', [LivreurDashboard::class, 'index'])->name('dashboard');
    Route::post('/position', [PositionController::class, 'update'])->name('position.update');
    Route::get('/livraisons', [LivraisonController::class, 'index'])->name('livraisons.index');
    Route::post('/livraisons/{commande}/accepter', [LivraisonController::class, 'accepter'])->name('livraisons.accepter');
    Route::patch('/livraisons/{livraison}/livrer', [LivraisonController::class, 'livrer'])->name('livraisons.livrer');
});

// === PAIEMENT CAMPAY ===
Route::middleware(['auth', 'role:client'])->group(function () {
    Route::get('/paiement/{commande}', [PaymentController::class, 'initier'])->name('paiement.initier');
    Route::post('/paiement/{commande}/payer', [PaymentController::class, 'payer'])->name('paiement.payer');
    Route::get('/paiement/{commande}/statut', [PaymentController::class, 'statut'])->name('paiement.statut');
});

// Webhook Campay (sans auth — appelé par Campay)
Route::post('/webhook/campay', [PaymentController::class, 'webhook'])->name('webhook.campay');

// === TONTINES (client + vendeur) ===
Route::middleware(['auth', 'role:client,vendeur'])->group(function () {
    Route::resource('tontines', TontineController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
    Route::post('/tontines/{tontine}/rejoindre', [TontineController::class, 'rejoindre'])->name('tontines.rejoindre');
    Route::post('/tontines/{tontine}/cotiser', [TontineController::class, 'cotiser'])->name('tontines.cotiser');
    Route::post('/tontines/{tontine}/payer-commande/{commande}', [TontineController::class, 'payerCommande'])->name('tontines.payer_commande');
});

require __DIR__.'/auth.php';
