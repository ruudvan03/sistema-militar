<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AdminController extends Controller
{
    /**
     * 1. ALTA DE PERSONAL
     * Crea un nuevo elemento y genera su primer token de acceso.
     */
    public function createUser(Request $request)
    {
        // Validar los datos con mensajes personalizados en español
        $request->validate([
            'grado' => 'required',
            'name' => 'required',
            'matricula' => 'required|unique:users',
            'area' => 'required',
            'especialidad' => 'required',
        ], [
            'matricula.unique' => 'ALERTA: Esta matrícula ya pertenece a un elemento registrado en la base de datos.',
            'matricula.required' => 'El campo de matrícula es estrictamente obligatorio.',
            'grado.required' => 'Debe especificar el grado militar del elemento.',
            'name.required' => 'El nombre completo es obligatorio para el registro.',
            'area.required' => 'Especifique el área a la que pertenece.',
            'especialidad.required' => 'Especifique el área de trabajo del elemento.',
        ]);

        // Crear al usuario en la BD
        $user = User::create([
            'grado' => $request->grado,
            'name' => $request->name,
            'matricula' => $request->matricula,
            'area' => $request->area,
            'especialidad' => $request->especialidad, // Se guarda en el campo 'especialidad' pero se muestra como 'Área de Trabajo'
            'password' => Hash::make('militar123'), 
            'role' => 'usuario' 
        ]);

        // Generar su Token JWT (Sigue la vigencia global de 60 min)
        $token = JWTAuth::fromUser($user);

        // Regresar al Dashboard y mostrarle el token al Admin
        return back()->with('new_user_token', $token)
                     ->with('new_user_name', $user->grado . ' ' . $user->name);
    }

    /**
     * 2. REGENERAR TOKEN POR MATRÍCULA
     * Busca a un elemento por su matrícula y genera un nuevo acceso si perdió el anterior.
     */
    public function regenerarToken(Request $request)
    {
        // Validar que se haya ingresado la matrícula
        $request->validate([
            'matricula' => 'required'
        ], [
            'matricula.required' => 'Debe ingresar una matrícula válida para buscar al elemento.'
        ]);

        // Buscar al usuario
        $user = User::where('matricula', $request->matricula)->first();

        if (!$user) {
            return back()->with('error', 'ERROR: No se encontró ningún elemento con la matrícula ' . $request->matricula);
        }

        // Generar el nuevo token
        $newToken = JWTAuth::fromUser($user);

        return back()->with('new_user_token', $newToken)
                     ->with('new_user_name', $user->grado . ' ' . $user->name . ' (TOKEN REGENERADO)');
    }

    /**
     * 3. REFRESH TOKEN POR ID
     * Función auxiliar para regenerar el token directamente desde una lista (si se implementa).
     */
    public function refreshToken($id)
    {
        $user = User::findOrFail($id);
        
        // Generamos un nuevo token basado en el usuario existente
        $newToken = JWTAuth::fromUser($user);

        return back()->with('new_user_token', $newToken)
                     ->with('new_user_name', $user->grado . ' ' . $user->name . ' (TOKEN REGENERADO)');
    }
}