<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Policier;

class PolicierSeeder extends Seeder
{
    public function run(): void
    {
        Policier::factory()->count(150)->create();
    }
}
