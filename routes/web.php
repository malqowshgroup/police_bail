<?php

use App\Support\ActivityLogger;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\PolicierController;
use App\Http\Controllers\ProprietaireController;
use App\Http\Controllers\LogementCivilController;
use App\Http\Controllers\ContratBailController;
use App\Http\Controllers\BordereauController;
use App\Http\Controllers\ReglementController;
use App\Http\Controllers\VirementController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\RapportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Parametres\ParametreController;
use App\Http\Controllers\Parametres\GradeController;
use App\Http\Controllers\Parametres\LocaliteController;
use App\Http\Controllers\Parametres\ServiceController;
use App\Http\Controllers\Parametres\NomenclatureController;
use App\Http\Controllers\Parametres\ActiviteController;
use App\Http\Controllers\Parametres\UserController;

/*
|--------------------------------------------------------------------------
| Routes publiques — Authentification
|--------------------------------------------------------------------------
*/

// Redirection racine
Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// Formulaire de connexion
Route::get('/login', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('auth.login');
})->name('login');

// Traitement de la connexion
Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email'    => ['required', 'email'],
        'password' => ['required', 'string'],
    ]);

    if (Auth::attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();
        ActivityLogger::log('connexion', 'User', Auth::id(), Auth::user()?->name);
        return redirect()->intended(route('dashboard'));
    }

    return back()
        ->withInput($request->only('email'))
        ->withErrors(['email' => 'Identifiants incorrects. Veuillez réessayer.']);
})->name('login.submit')->middleware('throttle:10,1');

// Déconnexion
Route::post('/logout', function (Request $request) {
    ActivityLogger::log('deconnexion', 'User', Auth::id(), Auth::user()?->name);
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

/*
|--------------------------------------------------------------------------
| Routes authentifiées
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── GESTION ──────────────────────────────────────────────────────

    Route::resource('policiers', PolicierController::class);

    Route::resource('logements', LogementCivilController::class);

    Route::resource('proprietaires', ProprietaireController::class);

    // ── CONTRATS ─────────────────────────────────────────────────────

    Route::resource('bordereaux', BordereauController::class)
        ->parameters(['bordereaux' => 'bordereau']);

    Route::post('/bordereaux/{bordereau}/soumettre', [BordereauController::class, 'soumettre'])->name('bordereaux.soumettre');
    Route::post('/bordereaux/{bordereau}/controler', [BordereauController::class, 'controler'])->name('bordereaux.controler');
    Route::post('/bordereaux/{bordereau}/valider', [BordereauController::class, 'valider'])->name('bordereaux.valider');
    Route::post('/bordereaux/{bordereau}/rejeter', [BordereauController::class, 'rejeter'])->name('bordereaux.rejeter');

    Route::resource('contrats', ContratBailController::class)
        ->parameters(['contrats' => 'contrat']);

    Route::post('/contrats/{contrat}/activer', [ContratBailController::class, 'activer'])->name('contrats.activer');
    Route::post('/contrats/{contrat}/suspendre', [ContratBailController::class, 'suspendre'])->name('contrats.suspendre');
    Route::post('/contrats/{contrat}/resilier', [ContratBailController::class, 'resilier'])->name('contrats.resilier');

    // ── PAIEMENTS ────────────────────────────────────────────────────

    Route::post('/reglements/generer', [ReglementController::class, 'genererMensuel'])->name('reglements.generer');
    Route::post('/reglements/{reglement}/en-attente', [ReglementController::class, 'mettreEnAttente'])->name('reglements.en_attente');
    Route::post('/reglements/{reglement}/annuler', [ReglementController::class, 'annuler'])->name('reglements.annuler');

    Route::resource('reglements', ReglementController::class);

    Route::post('/virements/{virement}/emettre', [VirementController::class, 'emettre'])->name('virements.emettre');
    Route::post('/virements/{virement}/executer', [VirementController::class, 'executer'])->name('virements.executer');
    Route::post('/virements/{virement}/annuler', [VirementController::class, 'annuler'])->name('virements.annuler');

    Route::resource('virements', VirementController::class);

    // ── GED ──────────────────────────────────────────────────────────

    Route::get('/documents/{document}/preview', [DocumentController::class, 'preview'])->name('documents.preview');
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::post('/documents/{document}/valider', [DocumentController::class, 'valider'])->name('documents.valider');
    Route::post('/documents/{document}/rejeter', [DocumentController::class, 'rejeter'])->name('documents.rejeter');

    Route::resource('documents', DocumentController::class);

    // ── RAPPORTS ─────────────────────────────────────────────────────

    Route::get('/rapports', [RapportController::class, 'index'])->name('rapports.index');

    // ── PARAMÈTRES (réservé administrateur) ──────────────────────────

    Route::middleware('role:administrateur')
        ->prefix('parametres')->name('parametres.')
        ->group(function () {
            Route::get('/', [ParametreController::class, 'index'])->name('index');

            $only = ['index', 'create', 'store', 'edit', 'update', 'destroy'];
            Route::resource('grades', GradeController::class)->only($only);
            Route::resource('localites', LocaliteController::class)->only($only);
            Route::resource('services', ServiceController::class)->only($only);
            Route::resource('nomenclatures', NomenclatureController::class)->only($only);
            Route::resource('utilisateurs', UserController::class)->only($only)
                ->parameters(['utilisateurs' => 'user']);
            Route::get('activites', [ActiviteController::class, 'index'])->name('activites.index');
        });

});
