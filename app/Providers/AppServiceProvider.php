<?php

namespace App\Providers;

use App\Models\Bordereau;
use App\Models\ContratBail;
use App\Models\LogementCivil;
use App\Models\Policier;
use App\Models\Proprietaire;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // GED : la table document_liens utilise des clés courtes (entite_type)
        // au lieu des noms de classes. morphMap ajoute les alias SANS imposer
        // que tous les modèles soient mappés (enforceMorphMap casserait User, etc.).
        Relation::morphMap([
            'policier'       => Policier::class,
            'logement_civil' => LogementCivil::class,
            'proprietaire'   => Proprietaire::class,
            'contrat_bail'   => ContratBail::class,
            'bordereau'      => Bordereau::class,
        ]);
    }
}
