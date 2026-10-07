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
use App\Http\Controllers\Admin\EspecialidadController;
use App\Http\Controllers\Admin\MedicoController;
use App\Http\Controllers\Admin\ConsultorioController;
use App\Http\Controllers\Admin\HorarioMedicoController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\SuperAdminLoginController;
use App\Http\Controllers\SuperAdminRecuperarController;
use App\Http\Controllers\CompraController;
use App\Models\Plan;

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

// Detalle de un plan (publico; es el "Ver más")
Route::get('/planes/{plan}', function (string $plan) {
    $selectedPlan = Plan::porSlug($plan);

    abort_unless($selectedPlan, 404);

    return view('planes.detalle', [
        'plan' => $selectedPlan,
        'planes' => Plan::catalogoArray(),
        'faqs' => array_merge(
            config('planes.faq_general', []),
            config('planes.faq_por_plan.' . $plan, [])
        ),
    ]);
})->where('plan', '[a-z0-9-]+')->name('planes.detalle');

// Compra de un plan (publico): formulario + procesamiento.
// throttle:5,1 -> maximo 5 intentos por minuto, para evitar spam de licencias falsas.
Route::middleware('throttle:5,1')->group(function () {
    Route::get('/comprar/{plan}', [CompraController::class, 'mostrarFormulario'])->where('plan', '[a-z0-9-]+')->name('comprar.form');
    Route::post('/comprar/{plan}', [CompraController::class, 'procesar'])->where('plan', '[a-z0-9-]+')->name('comprar.procesar');
});

// Marco legal (publico)
Route::view('/terminos-y-condiciones', 'terminos_condiciones')->name('terminos.index');
Route::view('/politica-de-privacidad', 'politica_privacidad')->name('privacidad.index');

// Todo lo que hace el Administrador va bajo /admin/...
Route::prefix('admin')->middleware(['esadministrador', 'verificarlicencia'])->group(function () {

    // Dashboard: http://127.0.0.1:8000/admin
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Mi Plan: http://127.0.0.1:8000/admin/mi-plan
    Route::get('/mi-plan', [DashboardController::class, 'miPlan'])->name('admin.plan');

    // Ver planes disponibles sin salir del panel: http://127.0.0.1:8000/admin/planes
    Route::get('/planes', [DashboardController::class, 'planes'])->name('admin.planes');

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

    // Médicos y Consultorios: /admin/medicos/...
    Route::prefix('medicos')->group(function () {

        // Médicos (su cuenta de acceso se crea aquí): /admin/medicos/medicos
        Route::get('/medicos', [MedicoController::class, 'index'])->name('medico.index');
        Route::get('/medicos/crear', [MedicoController::class, 'create'])->name('medico.create');
        Route::post('/medicos', [MedicoController::class, 'store'])->name('medico.store');
        Route::get('/medicos/{cedula}/editar', [MedicoController::class, 'edit'])->name('medico.edit');
        Route::put('/medicos/{cedula}', [MedicoController::class, 'update'])->name('medico.update');
        Route::patch('/medicos/{cedula}/estado', [MedicoController::class, 'cambiarEstado'])->name('medico.estado');

        // Especialidades: /admin/medicos/especialidades
        Route::get('/especialidades', [EspecialidadController::class, 'index'])->name('especialidad.index');
        Route::get('/especialidades/crear', [EspecialidadController::class, 'create'])->name('especialidad.create');
        Route::post('/especialidades', [EspecialidadController::class, 'store'])->name('especialidad.store');
        Route::get('/especialidades/{id}/editar', [EspecialidadController::class, 'edit'])->name('especialidad.edit');
        Route::put('/especialidades/{id}', [EspecialidadController::class, 'update'])->name('especialidad.update');
        Route::patch('/especialidades/{id}/estado', [EspecialidadController::class, 'cambiarEstado'])->name('especialidad.estado');

        // Consultorios: /admin/medicos/consultorios
        Route::get('/consultorios', [ConsultorioController::class, 'index'])->name('consultorio.index');
        Route::get('/consultorios/crear', [ConsultorioController::class, 'create'])->name('consultorio.create');
        Route::post('/consultorios', [ConsultorioController::class, 'store'])->name('consultorio.store');
        Route::get('/consultorios/{id}/editar', [ConsultorioController::class, 'edit'])->name('consultorio.edit');
        Route::put('/consultorios/{id}', [ConsultorioController::class, 'update'])->name('consultorio.update');
        Route::patch('/consultorios/{id}/estado', [ConsultorioController::class, 'cambiarEstado'])->name('consultorio.estado');

        // Horarios de atención: /admin/medicos/horarios
        Route::get('/horarios', [HorarioMedicoController::class, 'index'])->name('horario_medico.index');
        Route::get('/horarios/crear', [HorarioMedicoController::class, 'create'])->name('horario_medico.create');
        Route::post('/horarios', [HorarioMedicoController::class, 'store'])->name('horario_medico.store');
        Route::get('/horarios/{id}/editar', [HorarioMedicoController::class, 'edit'])->name('horario_medico.edit');
        Route::put('/horarios/{id}', [HorarioMedicoController::class, 'update'])->name('horario_medico.update');
        Route::patch('/horarios/{id}/estado', [HorarioMedicoController::class, 'cambiarEstado'])->name('horario_medico.estado');
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

// Login del Superadministrador (dueños de PrismaSalud): URL aparte, NO es el login de IPS.
// throttle:5,1 -> máximo 5 intentos por minuto.
Route::prefix('super-admin')->group(function () {
    Route::get('/login', [SuperAdminLoginController::class, 'mostrarFormulario'])->name('superadmin.login');
    Route::post('/login', [SuperAdminLoginController::class, 'iniciarSesion'])->middleware('throttle:5,1')->name('superadmin.login.post');
    Route::get('/login/codigo', [SuperAdminLoginController::class, 'mostrarCodigo'])->name('superadmin.login.codigo');
    Route::post('/login/codigo', [SuperAdminLoginController::class, 'verificarCodigo'])->middleware('throttle:5,1')->name('superadmin.login.codigo.verificar');
    Route::post('/login/codigo/reenviar', [SuperAdminLoginController::class, 'reenviarCodigo'])->middleware('throttle:5,1')->name('superadmin.login.codigo.reenviar');
    Route::post('/logout', [SuperAdminLoginController::class, 'cerrarSesion'])->name('superadmin.logout');

    // Recuperar contraseña del Superadministrador (3 pasos)
    Route::get('/recuperar', [SuperAdminRecuperarController::class, 'paso1'])->name('superadmin.recuperar');
    Route::post('/recuperar', [SuperAdminRecuperarController::class, 'enviarCodigo'])->middleware('throttle:5,1')->name('superadmin.recuperar.enviar');
    Route::get('/recuperar/codigo', [SuperAdminRecuperarController::class, 'paso2'])->name('superadmin.recuperar.codigo');
    Route::post('/recuperar/codigo', [SuperAdminRecuperarController::class, 'verificarCodigo'])->middleware('throttle:5,1')->name('superadmin.recuperar.codigo.verificar');
    Route::get('/recuperar/nueva', [SuperAdminRecuperarController::class, 'paso3'])->name('superadmin.recuperar.nueva');
    Route::post('/recuperar/nueva', [SuperAdminRecuperarController::class, 'guardarContrasena'])->middleware('throttle:5,1')->name('superadmin.recuperar.nueva.guardar');
});

// Panel del Superadministrador: http://127.0.0.1:8000/super-admin
Route::prefix('super-admin')->middleware('essuperadmin')->group(function () {
    Route::get('/', [SuperAdminController::class, 'index'])->name('superadmin.dashboard');
});

// Empresas y Administradores del Superadmin (están en routes/superadmin.php)
require __DIR__.'/superadmin.php';

// Rutas de recuperación de contraseña (públicas).
// Son tres pasos separados: 1) correo -> 2) código -> 3) nueva contraseña.
Route::get('/recuperar', [RecuperarPasswordController::class, 'mostrarFormularioSolicitud'])->name('recuperar.solicitar.form');
Route::post('/recuperar', [RecuperarPasswordController::class, 'enviarCodigo'])->name('recuperar.solicitar');

Route::get('/verificar-codigo', [RecuperarPasswordController::class, 'mostrarFormularioVerificar'])->name('recuperar.verificar.form');
Route::post('/verificar-codigo', [RecuperarPasswordController::class, 'verificarCodigo'])->name('recuperar.verificar');

Route::get('/nueva-contrasena', [RecuperarPasswordController::class, 'mostrarFormularioNuevaContrasena'])->name('recuperar.nueva.form');
Route::post('/nueva-contrasena', [RecuperarPasswordController::class, 'guardarNuevaContrasena'])->name('recuperar.nueva');