<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        // Create test user
        User::create([
            'name' => 'Usuario de Prueba',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        $this->command->info('Usuarios de prueba creados:');
        $this->command->info('- admin@example.com (password: password123)');
        $this->command->info('- test@example.com (password: password123)');
    }
}
