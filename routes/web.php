<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PanelUsuarioController;
use App\Http\Controllers\RecuperarPasswordController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\SuperAdminLoginController;
use App\Http\Controllers\SuperAdminRecuperarController;
use App\Models\Plan;
use App\Http\Controllers\Admin\DepartamentoController;
use App\Http\Controllers\Admin\CiudadController;
use App\Http\Controllers\Admin\SedeController;
use App\Http\Controllers\Admin\TipoDocumentoController;
use App\Http\Controllers\Admin\TipoUsuarioController;

Route::get('/', function () {
    return view('index');
});

// Pagina institucional "Nosotros" (publica)
Route::view('/nosotros', 'nosotros')->name('nosotros');

// Catalogo de servicios (publico)
Route::view('/servicios', 'servicios')->name('servicios.index');

// Marco legal (publico)
Route::view('/terminos-y-condiciones', 'terminos_condiciones')->name('terminos.index');
Route::view('/politica-de-privacidad', 'politica_privacidad')->name('privacidad.index');

// Compatibilidad con los enlaces de las vistas de planes (Blog y Contacto)
// hasta que esos mÃ³dulos se construyan en esta rama.
Route::view('/blog', 'nosotros')->name('blog.index');
Route::view('/contacto', 'servicios')->name('contacto.index');

// Planes del software (publico)
Route::view('/planes', 'planes')->name('planes.index');

// Detalle de un plan (publico; es el "Ver mÃ¡s")
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

// Compra de un plan (publico): formulario + procesamiento
Route::middleware('throttle:20,1')->group(function () {
    Route::get('/comprar/{plan}', [CompraController::class, 'mostrarFormulario'])->where('plan', '[a-z0-9-]+')->name('comprar.form');
    Route::post('/comprar/{plan}', [CompraController::class, 'procesar'])->where('plan', '[a-z0-9-]+')->name('comprar.procesar');
});

// Todo lo que hace el Administrador va bajo /admin/...
Route::prefix('admin')->middleware(['esadministrador', 'verificarlicencia'])->group(function () {

    // Dashboard: http://127.0.0.1:8000/admin
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Mi Plan: http://127.0.0.1:8000/admin/mi-plan
    Route::get('/mi-plan', [DashboardController::class, 'miPlan'])->name('admin.plan');

    // Mensaje de pago aceptado: accesible aunque la licencia siga inactiva
    Route::view('/pago-aceptado', 'admin.pago-aceptado')->name('admin.pago_aceptado');

    // Planes disponibles dentro del panel del administrador
    Route::get('/planes', [DashboardController::class, 'planes'])->name('admin.planes');

    // Recordatorios: vista previa de los mensajes automáticos por WhatsApp.
    Route::view('/recordatorios', 'admin.recordatorios')->name('admin.recordatorios');

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
});

// Rutas de login (pÃºblicas, cualquiera puede acceder al formulario)
Route::get('/login', [LoginController::class, 'mostrarFormulario'])->name('login');
Route::post('/login', [LoginController::class, 'iniciarSesion'])->name('login.post');
Route::post('/logout', [LoginController::class, 'cerrarSesion'])->name('logout');

// Panel del Usuario (roles distintos a Administrador)
Route::prefix('usuario')->middleware('esusuario')->group(function () {
    Route::get('/', [PanelUsuarioController::class, 'index'])->name('usuario.inicio');
});

// Rutas de recuperaciÃ³n de contraseÃ±a (pÃºblicas)
Route::get('/recuperar', [RecuperarPasswordController::class, 'mostrarFormularioSolicitud'])->name('recuperar.solicitar.form');
Route::post('/recuperar', [RecuperarPasswordController::class, 'enviarCodigo'])->name('recuperar.solicitar');

Route::get('/restablecer', [RecuperarPasswordController::class, 'mostrarFormularioRestablecer'])->name('recuperar.restablecer.form');
Route::post('/restablecer', [RecuperarPasswordController::class, 'restablecer'])->name('recuperar.restablecer');

// Login del Superadministrador (dueÃ±os de PrismaSalud): URL aparte del login de IPS
Route::prefix('super-admin')->group(function () {
    Route::get('/login', [SuperAdminLoginController::class, 'mostrarFormulario'])->name('superadmin.login');
    Route::post('/login', [SuperAdminLoginController::class, 'iniciarSesion'])->middleware('throttle:20,1')->name('superadmin.login.post');
    Route::get('/login/codigo', [SuperAdminLoginController::class, 'mostrarCodigo'])->name('superadmin.login.codigo');
    Route::post('/login/codigo', [SuperAdminLoginController::class, 'verificarCodigo'])->middleware('throttle:20,1')->name('superadmin.login.codigo.verificar');
    Route::post('/login/codigo/reenviar', [SuperAdminLoginController::class, 'reenviarCodigo'])->middleware('throttle:20,1')->name('superadmin.login.codigo.reenviar');
    Route::post('/logout', [SuperAdminLoginController::class, 'cerrarSesion'])->name('superadmin.logout');

    // Recuperar contraseÃ±a del Superadministrador (3 pasos)
    Route::get('/recuperar', [SuperAdminRecuperarController::class, 'paso1'])->name('superadmin.recuperar');
    Route::post('/recuperar', [SuperAdminRecuperarController::class, 'enviarCodigo'])->middleware('throttle:20,1')->name('superadmin.recuperar.enviar');
    Route::get('/recuperar/codigo', [SuperAdminRecuperarController::class, 'paso2'])->name('superadmin.recuperar.codigo');
    Route::post('/recuperar/codigo', [SuperAdminRecuperarController::class, 'verificarCodigo'])->middleware('throttle:20,1')->name('superadmin.recuperar.codigo.verificar');
    Route::get('/recuperar/nueva', [SuperAdminRecuperarController::class, 'paso3'])->name('superadmin.recuperar.nueva');
    Route::post('/recuperar/nueva', [SuperAdminRecuperarController::class, 'guardarContrasena'])->middleware('throttle:20,1')->name('superadmin.recuperar.nueva.guardar');
});

// Panel del Superadministrador
Route::prefix('super-admin')->middleware('essuperadmin')->group(function () {
    Route::get('/', [SuperAdminController::class, 'index'])->name('superadmin.dashboard');
});

// Empresas, administradores, planes y pagos del Superadministrador
require __DIR__.'/superadmin.php';
