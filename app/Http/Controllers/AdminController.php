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
        $request->validate([
            'grado' => 'required',
            'name' => 'required',
            'matricula' => 'required|unique:users',
            'area' => 'required',
            'especialidad' => 'required',
            'vigencia' => 'required|in:15,30,60', 
        ], [
            'matricula.unique' => 'ALERTA: Esta matrícula ya pertenece a un elemento registrado en la base de datos.',
            'matricula.required' => 'El campo de matrícula es estrictamente obligatorio.',
            'grado.required' => 'Debe especificar el grado militar del elemento.',
            'name.required' => 'El nombre completo es obligatorio para el registro.',
            'area.required' => 'Especifique el área a la que pertenece.',
            'especialidad.required' => 'Especifique el área de trabajo del elemento.',
            'vigencia.required' => 'Debe seleccionar la duración del token de acceso.',
            'vigencia.in' => 'La vigencia del token debe ser de 15, 30 o 60 días.',
        ]);

        $user = User::create([
            'grado' => $request->grado,
            'name' => $request->name,
            'matricula' => $request->matricula,
            'area' => $request->area,
            'especialidad' => $request->especialidad, 
            'password' => Hash::make('militar123'), 
            'role' => 'usuario' 
        ]);

        $minutosTTL = (int) $request->vigencia * 24 * 60;
        
        JWTAuth::factory()->setTTL($minutosTTL);
        $token = JWTAuth::fromUser($user);

        return back()->with('new_user_token', $token)
                     ->with('new_user_name', $user->grado . ' ' . $user->name);
    }


    public function regenerarToken(Request $request)
    {
        $request->validate([
            'matricula' => 'required',
            'vigencia' => 'required|in:15,30,60' 
        ], [
            'matricula.required' => 'Debe ingresar una matrícula válida para buscar al elemento.',
            'vigencia.required' => 'Debe seleccionar la duración del nuevo token.',
            'vigencia.in' => 'La vigencia del token debe ser de 15, 30 o 60 días.',
        ]);

        $user = User::where('matricula', $request->matricula)->first();

        if (!$user) {
            return back()->with('error', 'ERROR: No se encontró ningún elemento con la matrícula ' . $request->matricula);
        }

        $minutosTTL = (int) $request->vigencia * 24 * 60;
        
        JWTAuth::factory()->setTTL($minutosTTL);
        $newToken = JWTAuth::fromUser($user);

        return back()->with('new_user_token', $newToken)
                     ->with('new_user_name', $user->grado . ' ' . $user->name . ' (TOKEN REGENERADO)');
    }


    public function refreshToken($id)
    {
        $user = User::findOrFail($id);
        
        $newToken = JWTAuth::fromUser($user);

        return back()->with('new_user_token', $newToken)
                     ->with('new_user_name', $user->grado . ' ' . $user->name . ' (TOKEN REGENERADO)');
    }
}