<?php

namespace Database\Seeders;

use App\Enums\StatutBordereau;
use App\Enums\StatutContrat;
use App\Enums\StatutDocument;
use App\Enums\StatutPolicier;
use App\Enums\StatutReglement;
use App\Enums\StatutVirement;
use App\Enums\TypeDocument;
use App\Enums\TypeReglement;
use App\Models\Nomenclature;
use App\Support\Nomenclatures;
use Illuminate\Database\Seeder;

class NomenclatureSeeder extends Seeder
{
    /** Tous les enums alimentant les nomenclatures. */
    private const ENUMS = [
        StatutContrat::class,
        StatutPolicier::class,
        StatutBordereau::class,
        StatutReglement::class,
        TypeReglement::class,
        StatutVirement::class,
        StatutDocument::class,
        TypeDocument::class,
    ];

    public function run(): void
    {
        // Repart de zéro puis vide le cache pour relire les fallbacks des enums.
        Nomenclature::query()->delete();
        Nomenclatures::flush();

        foreach (self::ENUMS as $enum) {
            $categorie = $enum::CATEGORIE;

            foreach ($enum::cases() as $ordre => $case) {
                Nomenclature::create([
                    'categorie'     => $categorie,
                    'code'          => $case->value,
                    'libelle'       => $case->label(),
                    'couleur_badge' => $case->badge(),
                    'couleur_dot'   => method_exists($case, 'dot') ? $case->dot() : null,
                    'ordre'         => $ordre,
                    'actif'         => true,
                    'systeme'       => true, // valeurs câblées au workflow
                ]);
            }
        }

        Nomenclatures::flush();
    }
}
