<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Verificamos si está logueado y si su rol es 'admin'
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request); // ¡Pase, Comandante!
        }

        // 2. Si no es admin, abortamos con error 403 (Prohibido)
        abort(403, 'ACCESO DENEGADO: Área restringida al Servidor Central. Su intento ha sido registrado.');
    }
}