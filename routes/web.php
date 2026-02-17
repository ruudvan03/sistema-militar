<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentController;

// --- RUTAS PÚBLICAS ---
Route::get('/', function () { return redirect()->route('login'); });
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login-token', [AuthController::class, 'loginWithToken'])->name('login.token');
Route::get('/registro', [AuthController::class, 'showRegister'])->name('register');
Route::post('/registro', [AuthController::class, 'register'])->name('register.post');

// --- RUTAS PROTEGIDAS (Cualquier usuario logueado con Token) ---
Route::middleware('auth')->group(function () {
    
    // Todos pueden ver el Dashboard (El controlador decide qué mostrar)
    Route::get('/dashboard', [DocumentController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Rutas Operativas (Subir/Bajar)
    Route::post('/documentos', [DocumentController::class, 'store'])->name('documents.store');
    Route::get('/documentos/{id}', [DocumentController::class, 'download'])->name('documents.download');

});

// --- ZONA DE SEGURIDAD (Solo Admin/Servidor) ---
// Si un usuario normal intenta escribir estas URLs, recibirá error 403
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    
    // Ejemplo: Una ruta futura para ver todos los usuarios registrados
    Route::get('/usuarios', function() {
        return "PANEL DE CONTROL DE PERSONAL - SOLO SERVIDOR";
    });
    
    // Aquí pondrías rutas como: Eliminar usuarios, Borrar archivos, etc.
});