<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentController; // <<-- ¡ESTA LÍNEA ES LA CLAVE!

// Rutas Públicas
Route::get('/', function () { return redirect()->route('login'); });
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login-token', [AuthController::class, 'loginWithToken'])->name('login.token');

Route::get('/registro', [AuthController::class, 'showRegister'])->name('register');
Route::post('/registro', [AuthController::class, 'register'])->name('register.post');

// Rutas Protegidas (Requieren Token/Login)
Route::middleware('auth')->group(function () {
    
    // Dashboard (Lista de Archivos)
    Route::get('/dashboard', [DocumentController::class, 'index'])->name('dashboard');
    
    // Subir y Descargar Archivos
    Route::post('/documentos', [DocumentController::class, 'store'])->name('documents.store');
    Route::get('/documentos/{id}', [DocumentController::class, 'download'])->name('documents.download');

    // Salir
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});