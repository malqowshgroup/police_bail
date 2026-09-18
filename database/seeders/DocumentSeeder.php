<?php

namespace Database\Seeders;

use App\Enums\StatutDocument;
use App\Enums\TypeDocument;
use App\Models\Bordereau;
use App\Models\ContratBail;
use App\Models\Document;
use App\Models\LogementCivil;
use App\Models\Policier;
use App\Models\Proprietaire;
use App\Models\User;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    public function run(): void
    {
        // Ré-exécutable (cascade sur document_liens).
        Document::query()->delete();

        $adminId = User::query()->value('id');
        $formats = ['PDF', 'PDF', 'PDF', 'JPG', 'PNG', 'TIFF'];

        // Types de documents pertinents par entité.
        $typesParEntite = [
            'policier'       => ['carte_pro', 'fiche_identite', 'certificat_prise_service', 'decision_grade'],
            'logement_civil' => ['declaration_fonciere', 'extrait_topographique', 'etat_lieux_entree', 'attestation_occupation'],
            'proprietaire'   => ['piece_identite_proprio', 'rib', 'procuration'],
            'contrat_bail'   => ['contrat_bail_signe', 'pv_remise_cles', 'avis_resiliation', 'quitus_sodeci', 'quitus_cie'],
            'bordereau'      => ['autre'],
        ];

        // Pools d'identifiants par type d'entité.
        $pools = [
            'policier'       => Policier::inRandomOrder()->take(20)->pluck('id'),
            'logement_civil' => LogementCivil::inRandomOrder()->take(20)->pluck('id'),
            'proprietaire'   => Proprietaire::inRandomOrder()->take(15)->pluck('id'),
            'contrat_bail'   => ContratBail::inRandomOrder()->take(20)->pluck('id'),
            'bordereau'      => Bordereau::pluck('id'),
        ];

        $statuts = [
            StatutDocument::Valide->value, StatutDocument::Valide->value,
            StatutDocument::EnAttente->value, StatutDocument::EnAttente->value,
            StatutDocument::Rejete->value,
        ];

        $compteur = 1;

        foreach ($typesParEntite as $entiteType => $types) {
            $ids = $pools[$entiteType];
            if ($ids->isEmpty()) {
                continue;
            }

            // ~8 documents par type d'entité.
            $nb = min(8, $ids->count());
            foreach ($ids->take($nb) as $entiteId) {
                $type   = $types[array_rand($types)];
                $format = $formats[array_rand($formats)];
                $statut = $statuts[array_rand($statuts)];
                $valide = $statut === StatutDocument::Valide->value;

                $document = Document::create([
                    'nom_original'  => $this->nomFichier($type, $compteur++, $format),
                    'type_document' => $type,
                    'chemin_local'  => null, // métadonnées seules (pas de fichier physique en démo)
                    'taille_ko'     => rand(80, 4096),
                    'format'        => $format,
                    'statut'        => $statut,
                    'uploaded_par'  => $adminId,
                    'valide_par'    => $valide ? $adminId : null,
                    'date_validation' => $valide ? now()->subDays(rand(1, 30)) : null,
                ]);

                $document->liens()->create([
                    'entite_type' => $entiteType,
                    'entite_id'   => $entiteId,
                ]);
            }
        }
    }

    private function nomFichier(string $type, int $n, string $format): string
    {
        return strtoupper(str_replace('_', '-', $type)).'-'.str_pad((string) $n, 4, '0', STR_PAD_LEFT).'.'.strtolower($format);
    }
}
