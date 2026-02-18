<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth; 

class AuthController extends Controller
{
    // 1. Mostrar formulario de registro
    public function showRegister() {
        return view('auth.register');
    }

    // 2. Procesar el registro y GENERAR TOKEN
    public function register(Request $request) {
        // Validamos los datos militares
        $request->validate([
            'grado' => 'required',
            'name' => 'required',
            'matricula' => 'required|unique:users',
            'area' => 'required',
            'especialidad' => 'required',
            'password' => 'required|min:6', 
        ]);

        // Creamos al usuario
        $user = User::create([
            'grado' => $request->grado,
            'name' => $request->name,
            'matricula' => $request->matricula,
            'area' => $request->area,
            'especialidad' => $request->especialidad,
            'password' => Hash::make($request->password),
            'role' => 'usuario'
        ]);

        // --- MAGIA JWT ---
        // Generamos el token inmediatamente para este usuario nuevo
        $token = JWTAuth::fromUser($user);

        // Retornamos una vista especial que le muestre el token al usuario
        return view('auth.show_token', compact('token', 'user'));
    }

    // 3. Mostrar el Login (Solo pide Token)
    public function showLogin() {
        return view('auth.login');
    }

    // 4. Procesar el Login con Token
    public function loginWithToken(Request $request) {
        $request->validate(['token' => 'required']);

        try {
            // Intentamos obtener al usuario dueño de ese token
            // authenticate() verificará si el token es válido y no ha expirado
            $user = JWTAuth::setToken($request->token)->authenticate();

            if (!$user) {
                return back()->with('error', 'Token no válido o usuario no encontrado.');
            }

            // Si el token es real, iniciamos sesión "Web" manualmente
            Auth::login($user);

            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            return back()->with('error', 'Token inválido o expirado.');
        }
    }
    
    // 5. Salir
    public function logout() {
        Auth::logout();
        return redirect()->route('login');
    }
}