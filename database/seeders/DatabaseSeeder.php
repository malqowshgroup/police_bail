<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        $this->call([
            GradesSeeder::class,
            LocalitesSeeder::class,
            ServiceSeeder::class,
            NomenclatureSeeder::class,
            RolesPermissionsSeeder::class,
            AdminUserSeeder::class,
            PolicierSeeder::class,
            ProprietaireSeeder::class,
            LogementCivilSeeder::class,
            ContratBailSeeder::class,
            BordereauSeeder::class,
            ReglementSeeder::class,
            VirementSeeder::class,
            DocumentSeeder::class,
        ]);
    }
}
