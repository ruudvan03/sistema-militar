<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth; 

class AuthController extends Controller
{
    public function showRegister() {
        return view('auth.register');
    }

    public function register(Request $request) {
        $request->validate([
            'grado' => 'required',
            'name' => 'required',
            'matricula' => 'required|unique:users',
            'area' => 'required',
            'especialidad' => 'required',
            'password' => 'required|min:6', 
            'vigencia' => 'required|in:15,30,60', 
        ]);

        $user = User::create([
            'grado' => $request->grado,
            'name' => $request->name,
            'matricula' => $request->matricula,
            'area' => $request->area,
            'especialidad' => $request->especialidad,
            'password' => Hash::make($request->password),
            'role' => 'usuario'
        ]);


        $minutosTTL = (int) $request->vigencia * 24 * 60;

        JWTAuth::factory()->setTTL($minutosTTL);
        
        $token = JWTAuth::fromUser($user);

        return view('auth.show_token', compact('token', 'user'));
    }

    public function showLogin() {
        return view('auth.login');
    }

    public function loginWithToken(Request $request) {
        $request->validate(['token' => 'required']);

        try {
            if (!$user = JWTAuth::setToken($request->token)->authenticate()) {
                return back()->with('error', 'TOKEN NO VÁLIDO O USUARIO NO ENCONTRADO.');
            }

            if ($user->role === 'admin') {
                JWTAuth::factory()->setTTL(null);
                
                $finalToken = JWTAuth::fromUser($user);
            } else {
                $finalToken = $request->token;
            }

            Auth::login($user);

            session(['jwt_token' => $finalToken]);

            return redirect()->route('dashboard');

        } catch (\PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException $e) {
            return back()->with('error', 'EL TOKEN HA EXPIRADO. SOLICITE UNO NUEVO AL ADMINISTRADOR.');
        } catch (\Exception $e) {
            return back()->with('error', 'ERROR DE AUTENTICACIÓN: Token inválido o corrupto.');
        }
    }
    
    public function logout() {
        Auth::logout();
        session()->forget('jwt_token');
        return redirect()->route('login');
    }
}