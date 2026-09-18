<?php

namespace Database\Seeders;

use App\Enums\StatutContrat;
use App\Enums\StatutReglement;
use App\Enums\TypeReglement;
use App\Models\ContratBail;
use App\Models\Reglement;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReglementSeeder extends Seeder
{
    public function run(): void
    {
        // Ré-exécutable.
        Reglement::query()->delete();

        $adminId  = User::query()->value('id');
        $contrats = ContratBail::where('statut', StatutContrat::Actif->value)->get();

        if ($contrats->isEmpty()) {
            return;
        }

        // Génère les loyers des 3 derniers mois (mois courant inclus).
        for ($recul = 2; $recul >= 0; $recul--) {
            $periode = now()->copy()->subMonths($recul);
            $mois  = $periode->month;
            $annee = $periode->year;

            foreach ($contrats as $contrat) {
                // Le contrat doit avoir démarré avant la fin de la période.
                if ($contrat->date_debut && $contrat->date_debut->gt($periode->copy()->endOfMonth())) {
                    continue;
                }

                // Mois courant : à payer. Mois antérieurs : en attente de virement
                // (le passage à « viré » est géré par VirementSeeder, option B).
                $statut = $recul === 0
                    ? StatutReglement::APayer->value
                    : StatutReglement::EnAttenteVirement->value;

                Reglement::create([
                    'contrat_bail_id' => $contrat->id,
                    'periode_mois'    => $mois,
                    'periode_annee'   => $annee,
                    'type_reglement'  => TypeReglement::LoyerMensuel->value,
                    'montant'         => $contrat->taux_bail,
                    'statut'          => $statut,
                    'genere_par'      => $adminId,
                ]);
            }
        }
    }
}
