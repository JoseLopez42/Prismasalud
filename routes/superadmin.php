<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdminEmpresaController;
use App\Http\Controllers\SuperAdminAdministradorController;
use App\Http\Controllers\SuperAdminPlanController;
use App\Http\Controllers\SuperAdminPagoController;
use App\Http\Controllers\SuperAdminCuentaController;

// Rutas del panel del Superadministrador (todas protegidas con "essuperadmin").
// Este archivo se carga desde routes/web.php con: require __DIR__.'/superadmin.php';
Route::prefix('super-admin')->middleware('essuperadmin')->group(function () {

    // Empresas: /super-admin/empresas
    Route::get('/empresas', [SuperAdminEmpresaController::class, 'index'])->name('superadmin.empresas.index');
    Route::get('/empresas/exportar', [SuperAdminEmpresaController::class, 'exportar'])->name('superadmin.empresas.exportar');
    Route::get('/empresas/crear', [SuperAdminEmpresaController::class, 'create'])->name('superadmin.empresas.create');
    Route::post('/empresas', [SuperAdminEmpresaController::class, 'store'])->name('superadmin.empresas.store');
    Route::get('/empresas/{id}/editar', [SuperAdminEmpresaController::class, 'edit'])->whereNumber('id')->name('superadmin.empresas.edit');
    Route::put('/empresas/{id}', [SuperAdminEmpresaController::class, 'update'])->whereNumber('id')->name('superadmin.empresas.update');
    Route::patch('/empresas/{id}/estado', [SuperAdminEmpresaController::class, 'cambiarEstado'])->whereNumber('id')->name('superadmin.empresas.estado');

    // Administradores: /super-admin/administradores
    Route::get('/administradores', [SuperAdminAdministradorController::class, 'index'])->name('superadmin.administradores.index');
    Route::get('/administradores/crear', [SuperAdminAdministradorController::class, 'create'])->name('superadmin.administradores.create');
    Route::post('/administradores', [SuperAdminAdministradorController::class, 'store'])->name('superadmin.administradores.store');
    Route::get('/administradores/{cedula}/editar', [SuperAdminAdministradorController::class, 'edit'])->whereNumber('cedula')->name('superadmin.administradores.edit');
    Route::put('/administradores/{cedula}', [SuperAdminAdministradorController::class, 'update'])->whereNumber('cedula')->name('superadmin.administradores.update');
    Route::patch('/administradores/{cedula}/estado', [SuperAdminAdministradorController::class, 'cambiarEstado'])->whereNumber('cedula')->name('superadmin.administradores.estado');

    // Planes: /super-admin/planes
    Route::get('/planes', [SuperAdminPlanController::class, 'index'])->name('superadmin.planes.index');
    Route::get('/planes/{id}/editar', [SuperAdminPlanController::class, 'edit'])->whereNumber('id')->name('superadmin.planes.edit');
    Route::put('/planes/{id}', [SuperAdminPlanController::class, 'update'])->whereNumber('id')->name('superadmin.planes.update');
    Route::patch('/planes/{id}/activo', [SuperAdminPlanController::class, 'cambiarActivo'])->whereNumber('id')->name('superadmin.planes.activo');

    // Pagos: /super-admin/pagos
    Route::get('/pagos', [SuperAdminPagoController::class, 'index'])->name('superadmin.pagos.index');

    // Mi cuenta: /super-admin/mi-cuenta
    Route::get('/mi-cuenta', [SuperAdminCuentaController::class, 'edit'])->name('superadmin.cuenta.edit');
    Route::put('/mi-cuenta', [SuperAdminCuentaController::class, 'update'])->name('superadmin.cuenta.update');
});