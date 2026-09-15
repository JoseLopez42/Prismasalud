<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('index');
});

// Rutas de gestión de usuarios (protegidas: solo Administrador)
Route::middleware('esadministrador')->group(function () {
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::get('/usuarios/crear', [UsuarioController::class, 'create'])->name('usuarios.create');
    Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
    Route::get('/admin', [DashboardController::class, 'index'])->name('admin.dashboard');
});

// Rutas de login (públicas, cualquiera puede acceder al formulario)
Route::get('/login', [LoginController::class, 'mostrarFormulario'])->name('login');
Route::post('/login', [LoginController::class, 'iniciarSesion'])->name('login.post');
Route::post('/logout', [LoginController::class, 'cerrarSesion'])->name('logout');
