<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AdminController extends Controller
{
    public function createUser(Request $request)
    {
        // 1. Validar los datos
        $request->validate([
            'grado' => 'required',
            'name' => 'required',
            'matricula' => 'required|unique:users',
            'area' => 'required',
            'especialidad' => 'required',
        ]);

        // 2. Crear al usuario en la BD
        $user = User::create([
            'grado' => $request->grado,
            'name' => $request->name,
            'matricula' => $request->matricula,
            'area' => $request->area,
            'especialidad' => $request->especialidad,
            'password' => Hash::make('militar123'), // Contraseña por defecto (recomienda cambiarla luego)
            'role' => 'usuario' // Siempre se crean como usuarios normales
        ]);

        // 3. Generar su Token JWT
        $token = JWTAuth::fromUser($user);

        // 4. Regresar al Dashboard y mostrarle el token al Admin
        return back()->with('new_user_token', $token)
                     ->with('new_user_name', $user->grado . ' ' . $user->name);
    }
}