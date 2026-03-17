<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Crear el Usuario Administrador
        $admin = User::create([
            'grado' => 'Sgto. 2/o. Cdtes.',
            'name' => 'José de Jesús García Bello',
            'matricula' => 'D-7495657', 
            'area' => 'Direccion',
            'especialidad' => 'TICs',
            'role' => 'admin', 
            'password' => Hash::make('admin123'), 
        ]);

        // 2. Generar el Token JWT ETERNO
        JWTAuth::factory()->setTTL(null);
        
        $token = JWTAuth::fromUser($admin);

        // 3. Imprimir el resultado en la consola
        $this->command->info('--------------------------------------------------');
        $this->command->info('¡SERVIDOR CENTRAL INICIALIZADO!');
        $this->command->info('--------------------------------------------------');
        $this->command->info('Usuario: ' . $admin->grado . ' ' . $admin->name);
        $this->command->info('Matrícula: ' . $admin->matricula);
        $this->command->info('Estado del Token: ETERNO (Sin vencimiento)');
        $this->command->info('');
        $this->command->warn('COPIA TU TOKEN DE ACCESO ADMIN:');
        $this->command->line($token); 
        $this->command->info('--------------------------------------------------');
    }
}