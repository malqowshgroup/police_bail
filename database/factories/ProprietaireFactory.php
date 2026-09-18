<?php
namespace Database\Factories;

use App\Models\Localite;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProprietaireFactory extends Factory
{
    private static array $nomsIvoiriens = [
        'KOUASSI', 'KONAN', 'KOFFI', 'YAO', 'KOUAME', 'ASSI', 'N\'GUESSAN', 'BROU',
        'AHOU', 'AKISSI', 'ADJOUA', 'AMENAN', 'AFFOUE', 'DJEHOU', 'KAKOU',
        'COULIBALY', 'TRAORE', 'DIALLO', 'KONE', 'BAMBA', 'OUATTARA', 'DIABATE',
        'TOURE', 'DEMBELE', 'SORO', 'SILUE', 'FOFANA', 'DOUMBIA',
        'KACOU', 'EKRA', 'ETTIEN', 'ALLA', 'ANE', 'OKA', 'EBA',
    ];

    private static array $prenomsIvoiriens = [
        'Aya', 'Amenan', 'Adjoua', 'Affoue', 'Akissi', 'Ahou', 'Aïcha',
        'Konan', 'Kouassi', 'Koffi', 'Yao', 'Kouame', 'Brou', 'N\'Goran',
        'Moussa', 'Ibrahim', 'Mamadou', 'Oumar', 'Adama', 'Seydou', 'Bakary',
        'Jean', 'Pierre', 'Paul', 'Marc', 'David', 'Emmanuel', 'Christophe',
        'Marie', 'Fatou', 'Mariam', 'Fatoumata', 'Kadiatou', 'Aminata',
    ];

    private static array $raisonsSociales = [
        'Immobilière du Golfe', 'SCI Abidjan Invest', 'Groupe Latrille Properties',
        'Résidences Cocody', 'SARL Maisons Ivoiriennes', 'Immo Grand Bassam',
        'SCI Plateau Invest', 'Bâtiment & Habitat CI', 'Immobilière du Plateau',
        'SCI Treichville', 'Groupe Sicogi', 'Résidences Marcory',
    ];

    public function definition(): array
    {
        $typePersonne = $this->faker->randomElement(array_merge(
            array_fill(0, 7, 'physique'),
            array_fill(0, 3, 'morale')
        ));

        $nom = $this->faker->randomElement(self::$nomsIvoiriens);
        $prenoms = $typePersonne === 'physique'
            ? $this->faker->randomElement(self::$prenomsIvoiriens)
            : null;

        $localiteId = Localite::inRandomOrder()->value('id');

        return [
            'type_personne'       => $typePersonne,
            'nom'                 => $nom,
            'prenoms'             => $prenoms,
            'raison_sociale'      => $typePersonne === 'morale'
                ? $this->faker->randomElement(self::$raisonsSociales)
                : null,
            'num_piece_identite'  => strtoupper($this->faker->bothify('CI##??###??')),
            'type_piece'          => $this->faker->randomElement(['cni', 'passeport', 'sejour']),
            'telephone'           => '+225 0' . $this->faker->numberBetween(1, 9) . ' '
                . $this->faker->numerify('## ## ## ##'),
            'telephone2'          => $this->faker->boolean(30)
                ? '+225 0' . $this->faker->numberBetween(1, 9) . ' ' . $this->faker->numerify('## ## ## ##')
                : null,
            'email'               => $this->faker->boolean(60)
                ? strtolower($nom) . '.' . $this->faker->numberBetween(1, 99) . '@' . $this->faker->randomElement(['gmail.com', 'yahoo.fr', 'orange.ci'])
                : null,
            'adresse_postale'     => $this->faker->boolean(50)
                ? 'BP ' . $this->faker->numberBetween(100, 9999) . ' Abidjan'
                : null,
            'num_compte_contribuable' => $this->faker->boolean(40)
                ? strtoupper($this->faker->bothify('??##########'))
                : null,
            'localite_id'         => $localiteId,
            'actif'               => $this->faker->boolean(80),
        ];
    }
}
