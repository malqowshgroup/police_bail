<?php

namespace Database\Factories;

use App\Models\Grade;
use App\Models\Service;
use App\Models\Localite;
use App\Models\Policier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Policier>
 */
class PolicierFactory extends Factory
{
    public function definition(): array
    {
        $noms = ['KONÉ','DIALLO','COULIBALY','TRAORÉ','BAMBA','OUATTARA','TOURÉ','KONATÉ','DEMBÉLÉ','CISSÉ','SANOGO','SY','KOUYATÉ','BAKAYOKO','SILUÉ','FOFANA','CAMARA','DIABATÉ','DOUMBIA','TUO'];
        $prenoms = ['Mamadou','Ibrahima','Moussa','Seydou','Adama','Boubacar','Abdoulaye','Modibo','Sékou','Lamine','Aminata','Fatoumata','Kadiatou','Mariam','Oumou'];

        return [
            'matricule'              => strtoupper($this->faker->unique()->bothify('CI##???##')),
            'nom'                    => $this->faker->randomElement($noms),
            'prenoms'                => $this->faker->randomElement($prenoms),
            'sexe'                   => $this->faker->randomElement(['M','M','M','F']),
            'grade_id'               => Grade::inRandomOrder()->first()?->id ?? 1,
            'service_id'             => Service::inRandomOrder()->first()?->id,
            'localite_id'            => Localite::inRandomOrder()->first()?->id,
            'statut'                 => $this->faker->randomElement(['actif','actif','actif','actif','suspendu','retraite']),
            'date_naissance'         => $this->faker->dateTimeBetween('-55 years', '-25 years'),
            'date_prise_service'     => $this->faker->dateTimeBetween('-20 years', '-1 year'),
            'proprietaire_logement'  => false,
        ];
    }
}
