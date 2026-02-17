<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth; // Importante

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear el Usuario Administrador (General)
        $admin = User::create([
            'grado' => 'General de División',
            'name' => 'Administrador del Sistema',
            'matricula' => 'ADMIN-001', // Matrícula fácil de recordar
            'area' => 'Estado Mayor - Informática',
            'especialidad' => 'Ciberdefensa',
            'role' => 'admin', // <--- AQUÍ ESTÁ EL PODER
            'password' => Hash::make('admin123'), // Contraseña de respaldo
        ]);

        // 2. Generar el Token JWT para este admin
        $token = JWTAuth::fromUser($admin);

        // 3. Imprimir el resultado en la consola (Para que lo copies)
        $this->command->info('--------------------------------------------------');
        $this->command->info('¡SERVIDOR CENTRAL INICIALIZADO!');
        $this->command->info('--------------------------------------------------');
        $this->command->info('Usuario: ' . $admin->grado . ' ' . $admin->name);
        $this->command->info('Matrícula: ' . $admin->matricula);
        $this->command->info('');
        $this->command->warn('COPIA TU TOKEN DE ACCESO ADMIN:');
        $this->command->line($token); // Imprime el token limpio
        $this->command->info('--------------------------------------------------');
    }
}