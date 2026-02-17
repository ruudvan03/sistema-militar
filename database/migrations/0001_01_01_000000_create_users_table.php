<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            
            // Datos del Personal
            $table->string('grado');           // Ej: Teniente
            $table->string('name');            // Nombre completo
            $table->string('matricula')->unique(); // Login único
            $table->string('area');            // Ej: Inteligencia
            $table->string('especialidad');    // Ej: Ciberseguridad
            
            // Sistema
            $table->string('role')->default('usuario'); // 'usuario' o 'admin'
            $table->string('password');        // Contraseña segura
            
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
