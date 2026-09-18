<?php

namespace Database\Seeders;

use App\Enums\StatutContrat;
use App\Models\ContratBail;
use App\Models\LogementCivil;
use App\Models\Policier;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContratBailSeeder extends Seeder
{
    public function run(): void
    {
        // Ré-exécutable : repart d'une table vide.
        ContratBail::withTrashed()->get()->each->forceDelete();

        $policiers = Policier::with('grade')->eligibles()->inRandomOrder()->get()->values();
        $logements = LogementCivil::where('actif', true)->inRandomOrder()
            ->take($policiers->count())->get()->values();
        $adminId   = User::query()->value('id');

        // Liste à plat des statuts à répartir, puis mélange pour varier l'ordre.
        $repartition = [
            StatutContrat::Actif->value     => 55,
            StatutContrat::EnAttente->value => 15,
            StatutContrat::Suspendu->value  => 12,
            StatutContrat::Resilie->value   => 10,
        ];

        $statuts = [];
        foreach ($repartition as $statut => $nombre) {
            $statuts = array_merge($statuts, array_fill(0, $nombre, $statut));
        }
        shuffle($statuts);

        $sequence = 1;

        foreach ($statuts as $i => $statut) {
            $policier = $policiers->get($i);
            $logement = $logements->get($i);

            if (! $policier || ! $logement) {
                break;
            }

            $dateDebut = now()->subMonths(rand(1, 36))->startOfMonth();

            $attrs = [
                'numero_contrat'    => sprintf('CB-2026-%04d', $sequence++),
                'policier_id'       => $policier->id,
                'logement_civil_id' => $logement->id,
                'grade_id'          => $policier->grade_id,
                'taux_bail'         => $policier->grade?->taux_bail ?? 0,
                'date_debut'        => $dateDebut,
                'statut'            => $statut,
                'preavis_mois'      => 3,
                'saisi_par'         => $adminId,
            ];

            if (in_array($statut, [StatutContrat::Actif->value, StatutContrat::Suspendu->value, StatutContrat::Resilie->value], true)) {
                $attrs['valide_par']      = $adminId;
                $attrs['date_validation'] = $dateDebut->copy()->addDays(rand(1, 10));
            }

            if ($statut === StatutContrat::Suspendu->value) {
                $attrs['motif_suspension'] = 'Suspension pour régularisation de dossier.';
                $attrs['date_suspension']  = now()->subMonths(rand(1, 6));
            }

            if ($statut === StatutContrat::Resilie->value) {
                $dateResil = now()->subMonths(rand(1, 6));
                $attrs['motif_resiliation'] = 'Mutation du policier hors localité.';
                $attrs['date_resiliation']  = $dateResil;
                $attrs['date_fin_preavis']  = $dateResil->copy()->addMonths(3);
            }

            ContratBail::create($attrs);
        }
    }
}
