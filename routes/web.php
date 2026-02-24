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
    
    // Gestión de documentos
    Route::post('/documentos', [DocumentController::class, 'store'])->name('documents.store');
    Route::get('/documentos/{id}/ver', [DocumentController::class, 'preview'])->name('documents.preview'); 
    Route::get('/documentos/{id}', [DocumentController::class, 'download'])->name('documents.download');
    Route::put('/documentos/{id}', [DocumentController::class, 'update'])->name('documents.update');
    Route::delete('/documentos/{id}', [DocumentController::class, 'destroy'])->name('documents.destroy');
    
    // Sesión
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// --- 3. ZONA DE MANDO (Solo Admin) ---
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    // Alta de nuevo personal
    Route::post('/crear-personal', [AdminController::class, 'createUser'])->name('admin.create_user');
    
    // Gestión de seguridad: Regenerar tokens por extravío o vigencia
    Route::post('/regenerar-token', [AdminController::class, 'regenerarToken'])->name('admin.regenerar_token');
});