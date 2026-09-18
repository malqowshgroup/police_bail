<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder {
    public function run(): void {
        $admin = User::firstOrCreate(
            ['email' => 'admin@gestbail.ci'],
            [
                'name'     => 'Administrateur GESTBAIL',
                'password' => Hash::make('Admin@2026'),
            ]
        );
        $admin->assignRole('administrateur');
    }
}
