<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['DGPN',  'Direction Générale de la Police Nationale', 'Abidjan'],
            ['DPF',   'Direction de la Police des Frontières',     'Abidjan'],
            ['DSP',   'Direction de la Sécurité Publique',         'Abidjan'],
            ['DPJ',   'Direction de la Police Judiciaire',         'Abidjan'],
            ['CRS',   'Compagnie Républicaine de Sécurité',        'Yamoussoukro'],
            ['BAE',   'Brigade Anti-Émeute',                       'Bouaké'],
            ['GMI',   'Groupement Mobile d\'Intervention',         'Daloa'],
            ['CMC',   'Commissariat Central',                      'San-Pédro'],
            ['DST',   'Direction de la Surveillance du Territoire','Abidjan'],
            ['DRH',   'Direction des Ressources Humaines',         'Abidjan'],
        ];

        foreach ($services as [$code, $libelle, $localite]) {
            Service::updateOrCreate(
                ['code' => $code],
                ['libelle' => $libelle, 'localite' => $localite, 'actif' => true]
            );
        }
    }
}
