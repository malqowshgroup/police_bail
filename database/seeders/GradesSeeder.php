<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GradesSeeder extends Seeder {
    public function run(): void {
        $grades = [
            ['libelle'=>'Commissaire Divisionnaire','taux_bail'=>450000,'ordre'=>1],
            ['libelle'=>'Commissaire Principal','taux_bail'=>380000,'ordre'=>2],
            ['libelle'=>'Commissaire','taux_bail'=>320000,'ordre'=>3],
            ['libelle'=>'Commandant','taux_bail'=>270000,'ordre'=>4],
            ['libelle'=>'Capitaine','taux_bail'=>230000,'ordre'=>5],
            ['libelle'=>'Lieutenant','taux_bail'=>190000,'ordre'=>6],
            ['libelle'=>'Sous-Lieutenant','taux_bail'=>170000,'ordre'=>7],
            ['libelle'=>'Adjudant-Chef','taux_bail'=>160000,'ordre'=>8],
            ['libelle'=>'Adjudant','taux_bail'=>150000,'ordre'=>9],
            ['libelle'=>'Sergent-Chef','taux_bail'=>140000,'ordre'=>10],
            ['libelle'=>'Sergent','taux_bail'=>130000,'ordre'=>11],
            ['libelle'=>'Brigadier-Chef','taux_bail'=>120000,'ordre'=>12],
            ['libelle'=>'Brigadier','taux_bail'=>110000,'ordre'=>13],
            ['libelle'=>'Gardien de la Paix','taux_bail'=>100000,'ordre'=>14],
            ['libelle'=>'Élève Officier / Stagiaire','taux_bail'=>80000,'ordre'=>15],
        ];
        // Idempotent : clé sur le libellé (désormais unique). Pas de doublon si rejoué.
        foreach ($grades as $grade) {
            DB::table('grades')->updateOrInsert(
                ['libelle' => $grade['libelle']],
                ['taux_bail' => $grade['taux_bail'], 'ordre' => $grade['ordre'], 'actif' => true, 'updated_at' => now(), 'created_at' => now()]
            );
        }
    }
}
