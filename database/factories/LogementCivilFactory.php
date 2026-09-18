<?php

namespace Database\Factories;

use App\Models\Localite;
use App\Models\Proprietaire;
use Illuminate\Database\Eloquent\Factories\Factory;

class LogementCivilFactory extends Factory
{
    public function definition(): array
    {
        $localite = Localite::inRandomOrder()->first();
        $proprietaire = Proprietaire::inRandomOrder()->first();

        $quartiers = [
            'Cocody', 'Plateau', 'Adjamé', 'Yopougon', 'Marcory',
            'Koumassi', 'Treichville', 'Abobo', 'Port-Bouët', 'Attécoubé',
            'Riviera', 'Zone 4', 'Angré', 'Bingerville', 'Anyama',
        ];

        $localiteCode = $localite ? $localite->code : 'ABJ';
        $year = $this->faker->numberBetween(2018, 2026);
        $seq  = str_pad($this->faker->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT);

        return [
            'reference'        => "{$localiteCode}-{$year}-{$seq}",
            'localite_id'      => $localite?->id,
            'proprietaire_id'  => $proprietaire?->id,
            'beneficiaire_id'  => null,
            'quartier'         => $this->faker->randomElement($quartiers),
            'ilot'             => $this->faker->optional(0.6)->numerify('##'),
            'lot'              => $this->faker->optional(0.6)->numerify('###'),
            'adresse_complete' => $this->faker->optional(0.7)->sentence(6),
            'actif'            => $this->faker->boolean(90),
        ];
    }
}
