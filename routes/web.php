<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PanelUsuarioController;
use App\Http\Controllers\RecuperarPasswordController;
use App\Http\Controllers\Admin\DepartamentoController;
use App\Http\Controllers\Admin\CiudadController;
use App\Http\Controllers\Admin\SedeController;
use App\Http\Controllers\Admin\TipoDocumentoController;
use App\Http\Controllers\Admin\TipoUsuarioController;
use App\Http\Controllers\Admin\EpsController;
use App\Http\Controllers\Admin\RegimenController;

Route::get('/', function () {
    return view('index');
});

// Pagina institucional "Nosotros" (publica)
Route::view('/nosotros', 'nosotros')->name('nosotros');

// Catalogo de servicios (publico)
Route::view('/servicios', 'servicios')->name('servicios.index');

// Blog (publico)
Route::view('/blog', 'blog')->name('blog.index');

// Contacto (publico)
Route::view('/contacto', 'contacto')->name('contacto.index');

// Planes del software (publico)
Route::view('/planes', 'planes')->name('planes.index');

// Marco legal (publico)
Route::view('/terminos-y-condiciones', 'terminos_condiciones')->name('terminos.index');
Route::view('/politica-de-privacidad', 'politica_privacidad')->name('privacidad.index');

// Todo lo que hace el Administrador va bajo /admin/...
Route::prefix('admin')->middleware('esadministrador')->group(function () {

    // Dashboard: http://127.0.0.1:8000/admin
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Usuarios: http://127.0.0.1:8000/admin/usuarios
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::get('/usuarios/crear', [UsuarioController::class, 'create'])->name('usuarios.create');
    Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
    Route::get('/usuarios/{cedula}/editar', [UsuarioController::class, 'edit'])->name('usuarios.edit');
    Route::put('/usuarios/{cedula}', [UsuarioController::class, 'update'])->name('usuarios.update');
    Route::patch('/usuarios/{cedula}/estado', [UsuarioController::class, 'cambiarEstado'])->name('usuarios.estado');

    // Roles (tipos de usuario): /admin/usuarios/roles
    Route::get('/usuarios/roles', [TipoUsuarioController::class, 'index'])->name('roles.index');
    Route::get('/usuarios/roles/crear', [TipoUsuarioController::class, 'create'])->name('roles.create');
    Route::post('/usuarios/roles', [TipoUsuarioController::class, 'store'])->name('roles.store');
    Route::get('/usuarios/roles/{id}/editar', [TipoUsuarioController::class, 'edit'])->name('roles.edit');
    Route::put('/usuarios/roles/{id}', [TipoUsuarioController::class, 'update'])->name('roles.update');
    Route::patch('/usuarios/roles/{id}/estado', [TipoUsuarioController::class, 'cambiarEstado'])->name('roles.estado');

    // Tipos de documento: /admin/usuarios/tipos-documento
    Route::get('/usuarios/tipos-documento', [TipoDocumentoController::class, 'index'])->name('tipos_documento.index');
    Route::get('/usuarios/tipos-documento/crear', [TipoDocumentoController::class, 'create'])->name('tipos_documento.create');
    Route::post('/usuarios/tipos-documento', [TipoDocumentoController::class, 'store'])->name('tipos_documento.store');
    Route::get('/usuarios/tipos-documento/{id}/editar', [TipoDocumentoController::class, 'edit'])->name('tipos_documento.edit');
    Route::put('/usuarios/tipos-documento/{id}', [TipoDocumentoController::class, 'update'])->name('tipos_documento.update');
    Route::patch('/usuarios/tipos-documento/{id}/estado', [TipoDocumentoController::class, 'cambiarEstado'])->name('tipos_documento.estado');

    // Ubicaciones: http://127.0.0.1:8000/admin/ubicaciones/...
    // (refleja la carpeta de vistas admin/ubicaciones/...)
    Route::prefix('ubicaciones')->group(function () {

        // Departamentos: /admin/ubicaciones/departamentos
        Route::get('/departamentos', [DepartamentoController::class, 'index'])->name('departamentos.index');
        Route::get('/departamentos/crear', [DepartamentoController::class, 'create'])->name('departamentos.create');
        Route::post('/departamentos', [DepartamentoController::class, 'store'])->name('departamentos.store');
        Route::get('/departamentos/{id}/editar', [DepartamentoController::class, 'edit'])->name('departamentos.edit');
        Route::put('/departamentos/{id}', [DepartamentoController::class, 'update'])->name('departamentos.update');
        Route::patch('/departamentos/{id}/estado', [DepartamentoController::class, 'cambiarEstado'])->name('departamentos.estado');

        // Ciudades: /admin/ubicaciones/ciudades
        Route::get('/ciudades', [CiudadController::class, 'index'])->name('ciudades.index');
        Route::get('/ciudades/crear', [CiudadController::class, 'create'])->name('ciudades.create');
        Route::post('/ciudades', [CiudadController::class, 'store'])->name('ciudades.store');
        Route::get('/ciudades/{id}/editar', [CiudadController::class, 'edit'])->name('ciudades.edit');
        Route::put('/ciudades/{id}', [CiudadController::class, 'update'])->name('ciudades.update');
        Route::patch('/ciudades/{id}/estado', [CiudadController::class, 'cambiarEstado'])->name('ciudades.estado');

        // Sedes: /admin/ubicaciones/sedes
        Route::get('/sedes', [SedeController::class, 'index'])->name('sedes.index');
        Route::get('/sedes/crear', [SedeController::class, 'create'])->name('sedes.create');
        Route::post('/sedes', [SedeController::class, 'store'])->name('sedes.store');
        Route::get('/sedes/{id}/editar', [SedeController::class, 'edit'])->name('sedes.edit');
        Route::put('/sedes/{id}', [SedeController::class, 'update'])->name('sedes.update');
        Route::patch('/sedes/{id}/estado', [SedeController::class, 'cambiarEstado'])->name('sedes.estado');
    });

    // EPS: http://127.0.0.1:8000/admin/eps/...
    Route::prefix('eps')->group(function () {

        // EPS: /admin/eps
        Route::get('/', [EpsController::class, 'index'])->name('eps.index');
        Route::get('/crear', [EpsController::class, 'create'])->name('eps.create');
        Route::post('/', [EpsController::class, 'store'])->name('eps.store');
        Route::get('/{nit_eps}/editar', [EpsController::class, 'edit'])->name('eps.edit');
        Route::put('/{nit_eps}', [EpsController::class, 'update'])->name('eps.update');
        Route::patch('/{nit_eps}/estado', [EpsController::class, 'cambiarEstado'])->name('eps.estado');

        // Regímenes: /admin/eps/regimen
        Route::get('/regimen', [RegimenController::class, 'index'])->name('regimen.index');
        Route::get('/regimen/crear', [RegimenController::class, 'create'])->name('regimen.create');
        Route::post('/regimen', [RegimenController::class, 'store'])->name('regimen.store');
        Route::get('/regimen/{id}/editar', [RegimenController::class, 'edit'])->name('regimen.edit');
        Route::put('/regimen/{id}', [RegimenController::class, 'update'])->name('regimen.update');
        Route::patch('/regimen/{id}/estado', [RegimenController::class, 'cambiarEstado'])->name('regimen.estado');
    });
});

// Rutas de login (públicas, cualquiera puede acceder al formulario)
Route::get('/login', [LoginController::class, 'mostrarFormulario'])->name('login');
Route::post('/login', [LoginController::class, 'iniciarSesion'])->name('login.post');
Route::post('/logout', [LoginController::class, 'cerrarSesion'])->name('logout');

// Panel del Usuario (roles distintos a Administrador)
Route::prefix('usuario')->middleware('esusuario')->group(function () {
    Route::get('/', [PanelUsuarioController::class, 'index'])->name('usuario.inicio');
});

// Rutas de recuperación de contraseña (públicas)
Route::get('/recuperar', [RecuperarPasswordController::class, 'mostrarFormularioSolicitud'])->name('recuperar.solicitar.form');
Route::post('/recuperar', [RecuperarPasswordController::class, 'enviarCodigo'])->name('recuperar.solicitar');

Route::get('/restablecer', [RecuperarPasswordController::class, 'mostrarFormularioRestablecer'])->name('recuperar.restablecer.form');
Route::post('/restablecer', [RecuperarPasswordController::class, 'restablecer'])->name('recuperar.restablecer');