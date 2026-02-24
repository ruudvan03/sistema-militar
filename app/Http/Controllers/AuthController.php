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

        // Generamos el token inicial siguiendo la configuración global
        $token = JWTAuth::fromUser($user);

        return view('auth.show_token', compact('token', 'user'));
    }

    // 3. Mostrar el Login (Solo pide Token)
    public function showLogin() {
        return view('auth.login');
    }

    // 4. Procesar el Login con Token (CON EXPIRACIÓN DIFERENCIADA)
    public function loginWithToken(Request $request) {
        $request->validate(['token' => 'required']);

        try {
            // Verificamos si el token es válido y obtenemos al usuario
            if (!$user = JWTAuth::setToken($request->token)->authenticate()) {
                return back()->with('error', 'TOKEN NO VÁLIDO O USUARIO NO ENCONTRADO.');
            }

            // --- LÓGICA DE VIGENCIA ADMINISTRADA POR EL ROOT ---
            // Si es ADMIN, generamos un token persistente (sin expiración)
            if ($user->role === 'admin') {
                $finalToken = JWTAuth::fromUser($user, ['exp' => null]);
            } else {
                // Si es USUARIO, el token mantiene su vigencia estándar
                $finalToken = $request->token;
            }

            // Iniciamos la sesión en el protector Web de Laravel
            Auth::login($user);

            // Almacenamos el token final en la sesión por seguridad
            session(['jwt_token' => $finalToken]);

            return redirect()->route('dashboard');

        } catch (\PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException $e) {
            return back()->with('error', 'EL TOKEN HA EXPIRADO. SOLICITE UNO NUEVO AL ADMINISTRADOR.');
        } catch (\Exception $e) {
            return back()->with('error', 'ERROR DE AUTENTICACIÓN: Token inválido o corrupto.');
        }
    }
    
    // 5. Salir
    public function logout() {
        Auth::logout();
        session()->forget('jwt_token');
        return redirect()->route('login');
    }
}