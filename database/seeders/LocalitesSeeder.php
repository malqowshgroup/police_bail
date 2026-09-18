<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocalitesSeeder extends Seeder {
    public function run(): void {
        $localites = [
            ['code'=>'ABJ','libelle'=>'Abidjan'],
            ['code'=>'BKE','libelle'=>'Bouaké'],
            ['code'=>'SNP','libelle'=>'San-Pédro'],
            ['code'=>'DLO','libelle'=>'Daloa'],
            ['code'=>'KRG','libelle'=>'Korhogo'],
            ['code'=>'YMK','libelle'=>'Yamoussoukro'],
            ['code'=>'ABG','libelle'=>'Abengourou'],
            ['code'=>'MAN','libelle'=>'Man'],
            ['code'=>'DIV','libelle'=>'Divo'],
            ['code'=>'GGN','libelle'=>'Gagnoa'],
        ];
        foreach ($localites as $localite) {
            DB::table('localites')->insertOrIgnore(array_merge($localite, ['actif'=>true,'created_at'=>now(),'updated_at'=>now()]));
        }
    }
}
