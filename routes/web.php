<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\AdminController; 

// --- 1. RUTAS PÚBLICAS (Solo Login con Token) ---
Route::get('/', function () { return redirect()->route('login'); });
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login-token', [AuthController::class, 'loginWithToken'])->name('login.token');

// --- 2. RUTAS PROTEGIDAS (Usuarios y Admin) ---
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DocumentController::class, 'index'])->name('dashboard');
    
    // Subir documento
    Route::post('/documentos', [DocumentController::class, 'store'])->name('documents.store');
    
    // Acciones sobre el documento (Previsualizar, Descargar, Eliminar)
    Route::get('/documentos/{id}/ver', [DocumentController::class, 'preview'])->name('documents.preview'); 
    Route::get('/documentos/{id}', [DocumentController::class, 'download'])->name('documents.download');
    Route::delete('/documentos/{id}', [DocumentController::class, 'destroy'])->name('documents.destroy');
    
    // Cerrar sesión
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// --- 3. ZONA DE MANDO (Solo Admin) ---
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    // Ruta para que el Admin registre nuevo personal desde el Dashboard
    Route::post('/crear-personal', [AdminController::class, 'createUser'])->name('admin.create_user');
});