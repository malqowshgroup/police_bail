<?php

namespace Database\Seeders;

use App\Enums\StatutReglement;
use App\Enums\StatutVirement;
use App\Models\Reglement;
use App\Models\User;
use App\Models\Virement;
use Illuminate\Database\Seeder;

class VirementSeeder extends Seeder
{
    public function run(): void
    {
        // Ré-exécutable : détache les règlements et vide la table.
        Reglement::whereNotNull('virement_id')->update(['virement_id' => null]);
        Virement::query()->delete();

        $adminId = User::query()->value('id');
        $banques = ['SGBCI', 'BICICI', 'NSIA Banque', 'Ecobank', 'BACI', 'Coris Bank'];

        // Règlements en attente de virement groupés par propriétaire (via logement).
        $parProprietaire = Reglement::query()
            ->where('reglements.statut', StatutReglement::EnAttenteVirement->value)
            ->whereNull('reglements.virement_id')
            ->join('contrats_bail', 'contrats_bail.id', '=', 'reglements.contrat_bail_id')
            ->join('logements_civils', 'logements_civils.id', '=', 'contrats_bail.logement_civil_id')
            ->select('reglements.id', 'logements_civils.proprietaire_id as pid')
            ->get()
            ->groupBy('pid');

        // On crée jusqu'à 9 virements, à des stades variés.
        $cibles = $parProprietaire->take(9)->values();
        $sequence = 1;

        foreach ($cibles as $i => $reglements) {
            $pid = $reglements->first()->pid;
            $ids = $reglements->pluck('id');

            // Répartition des stades : exécuté / émis / en préparation.
            $statut = match ($i % 3) {
                0 => StatutVirement::Execute->value,
                1 => StatutVirement::Emis->value,
                default => StatutVirement::EnPreparation->value,
            };

            $virement = Virement::create([
                'numero'          => sprintf('VIR-2026-%04d', $sequence++),
                'proprietaire_id' => $pid,
                'banque'          => $banques[array_rand($banques)],
                'statut'          => StatutVirement::EnPreparation->value,
                'montant_total'   => 0,
            ]);

            Reglement::whereIn('id', $ids)->update(['virement_id' => $virement->id]);
            $virement->recalculerMontant();

            if (in_array($statut, [StatutVirement::Emis->value, StatutVirement::Execute->value], true)) {
                $virement->update([
                    'statut'            => StatutVirement::Emis->value,
                    'reference_fichier' => 'FIC-'.$virement->numero.'.txt',
                    'emis_par'          => $adminId,
                    'date_emission'     => now()->subDays(rand(3, 10)),
                ]);
            }

            if ($statut === StatutVirement::Execute->value) {
                $dateExec = now()->subDays(rand(1, 3));
                $virement->update([
                    'statut'         => StatutVirement::Execute->value,
                    'execute_par'    => $adminId,
                    'date_execution' => $dateExec,
                    'date_virement'  => $dateExec,
                ]);
                Reglement::whereIn('id', $ids)->update([
                    'statut'                     => StatutReglement::Vire->value,
                    'numero_virement'            => $virement->numero,
                    'date_virement'              => $dateExec,
                    'banque_bailleur'            => $virement->banque,
                    'reference_fichier_virement' => $virement->reference_fichier,
                ]);
            }
        }
    }
}
