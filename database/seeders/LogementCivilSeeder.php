<?php

namespace Database\Seeders;

use App\Models\LogementCivil;
use Illuminate\Database\Seeder;

class LogementCivilSeeder extends Seeder
{
    public function run(): void
    {
        LogementCivil::factory(200)->create();
    }
}
