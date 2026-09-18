<?php

namespace Database\Seeders;

use App\Enums\StatutBordereau;
use App\Enums\StatutContrat;
use App\Models\Bordereau;
use App\Models\ContratBail;
use App\Models\User;
use Illuminate\Database\Seeder;

class BordereauSeeder extends Seeder
{
    public function run(): void
    {
        // Ré-exécutable : détache les contrats puis vide la table.
        ContratBail::whereNotNull('bordereau_id')->update(['bordereau_id' => null]);
        Bordereau::query()->delete();

        $adminId = User::query()->value('id');

        // Pioche des contrats « en attente » non rattachés, en lots.
        $enAttente = ContratBail::where('statut', StatutContrat::EnAttente->value)
            ->whereNull('bordereau_id')
            ->inRandomOrder()
            ->get();

        if ($enAttente->isEmpty()) {
            return;
        }

        $lots = $enAttente->chunk(5)->values();
        $sequence = 1;

        // Bordereau 1 : en saisie
        if ($lot = $lots->get(0)) {
            $this->creerBordereau($sequence++, StatutBordereau::EnSaisie->value, $lot, $adminId);
        }

        // Bordereau 2 : en contrôle
        if ($lot = $lots->get(1)) {
            $this->creerBordereau($sequence++, StatutBordereau::EnControle->value, $lot, $adminId);
        }

        // Bordereau 3 : validé (active ses contrats)
        if ($lot = $lots->get(2)) {
            $this->creerBordereau($sequence++, StatutBordereau::Valide->value, $lot, $adminId);
        }
    }

    private function creerBordereau(int $seq, string $statut, $contrats, ?int $adminId): void
    {
        $bordereau = Bordereau::create([
            'numero'        => sprintf('BORD-2026-%04d', $seq),
            'annee'         => 2026,
            'statut'        => $statut,
            'saisi_par'     => $adminId,
            'date_saisie'   => now()->subDays(rand(5, 20)),
            'observations'  => null,
        ]);

        $ids = $contrats->pluck('id');
        ContratBail::whereIn('id', $ids)->update(['bordereau_id' => $bordereau->id]);

        if (in_array($statut, [StatutBordereau::EnValidation->value, StatutBordereau::Valide->value], true)) {
            $bordereau->update(['controle_par' => $adminId, 'date_controle' => now()->subDays(rand(2, 4))]);
        }

        if ($statut === StatutBordereau::Valide->value) {
            $bordereau->update(['valide_par' => $adminId, 'date_validation' => now()->subDay()]);
            ContratBail::whereIn('id', $ids)
                ->where('statut', StatutContrat::EnAttente->value)
                ->update([
                    'statut'          => StatutContrat::Actif->value,
                    'valide_par'      => $adminId,
                    'date_validation' => now()->subDay(),
                ]);
        }
    }
}
