<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PanelUsuarioController;
use App\Http\Controllers\UsuarioCitaController;
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
use App\Http\Controllers\Admin\EpsController;
use App\Http\Controllers\Admin\RegimenController;
use App\Http\Controllers\Admin\EspecialidadController;
use App\Http\Controllers\Admin\MedicoController;
use App\Http\Controllers\Admin\ConsultorioController;
use App\Http\Controllers\Admin\HorarioMedicoController;
use App\Http\Controllers\Admin\TipoCitaController;
use App\Http\Controllers\Admin\EstadoCitaController;
use App\Http\Controllers\Admin\CitaMedicaController;
use App\Http\Controllers\Admin\HistoriaClinicaController;
use App\Http\Controllers\Admin\MedicamentoController;
use App\Http\Controllers\Admin\ApiMedicamentoController;
use App\Http\Controllers\Admin\ProcedimientoController as AdminProcedimientoController;
use App\Http\Controllers\Admin\TipoProcedimientoController as AdminTipoProcedimientoController;
use App\Http\Controllers\Medico\PanelMedicoController;
use App\Http\Controllers\Medico\HorarioMedicoController as MedicoHorarioController;
use App\Http\Controllers\Medico\PerfilController;
use App\Http\Controllers\Medico\HistoriaMedicaController;
use App\Http\Controllers\Medico\ProcedimientoController;
use App\Http\Controllers\Medico\ConsultaMedicaController;
use App\Http\Controllers\Farmacia\FarmaciaController;
use App\Http\Controllers\Admin\AfiliacionController;
use App\Http\Controllers\Admin\BeneficiarioController;
use App\Http\Controllers\Admin\EstadoController;
use App\Http\Controllers\PrimerIngresoController;

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

// Blog (publico)
Route::view('/blog', 'blog')->name('blog.index');

// Contacto (publico)
Route::view('/contacto', 'contacto')->name('contacto.index');

// Planes del software (publico)
Route::view('/planes', 'planes')->name('planes.index');

// Aviso público cuando la licencia de la IPS no permite el acceso
Route::view('/licencia-sin-acceso', 'licencia.sin-acceso')->name('licencia.bloqueada');

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

    // Citas Médicas: /admin/citas/...
    Route::prefix('citas')->group(function () {

        // Citas: /admin/citas
        Route::get('/', [CitaMedicaController::class, 'index'])->name('cita.index');
        Route::get('/crear', [CitaMedicaController::class, 'create'])->name('cita.create');
        Route::post('/', [CitaMedicaController::class, 'store'])->name('cita.store');
        Route::get('/{id}/editar', [CitaMedicaController::class, 'edit'])->name('cita.edit');
        Route::put('/{id}', [CitaMedicaController::class, 'update'])->name('cita.update');
        Route::patch('/{id}/estado', [CitaMedicaController::class, 'cambiarEstado'])->name('cita.estado');

        // Tipos de cita: /admin/citas/tipos
        Route::get('/tipos', [TipoCitaController::class, 'index'])->name('tipo_cita.index');
        Route::get('/tipos/crear', [TipoCitaController::class, 'create'])->name('tipo_cita.create');
        Route::post('/tipos', [TipoCitaController::class, 'store'])->name('tipo_cita.store');
        Route::get('/tipos/{id}/editar', [TipoCitaController::class, 'edit'])->name('tipo_cita.edit');
        Route::put('/tipos/{id}', [TipoCitaController::class, 'update'])->name('tipo_cita.update');
        Route::patch('/tipos/{id}/estado', [TipoCitaController::class, 'cambiarEstado'])->name('tipo_cita.estado');

        // Estados de cita: /admin/citas/estados
        Route::get('/estados', [EstadoCitaController::class, 'index'])->name('estado_cita.index');
        Route::get('/estados/crear', [EstadoCitaController::class, 'create'])->name('estado_cita.create');
        Route::post('/estados', [EstadoCitaController::class, 'store'])->name('estado_cita.store');
        Route::get('/estados/{id}/editar', [EstadoCitaController::class, 'edit'])->name('estado_cita.edit');
        Route::put('/estados/{id}', [EstadoCitaController::class, 'update'])->name('estado_cita.update');
        Route::patch('/estados/{id}/estado', [EstadoCitaController::class, 'cambiarEstado'])->name('estado_cita.estado');
    });

    // Historia Clínica: el administrador visualiza TODAS las historias
    // médicas creadas por los médicos (búsqueda por cédula del paciente).
    Route::get('/historia', [HistoriaClinicaController::class, 'index'])->name('historia.index');

    // Farmacia y Medicamentos: /admin/farmacia/medicamentos
    Route::prefix('farmacia')->group(function () {
        // Medicamentos: catálogo. La llave primaria es el código del
        // medicamento (string), por eso las rutas usan {codigo}.
        Route::get('/medicamentos', [MedicamentoController::class, 'index'])->name('medicamento.index');
        Route::get('/medicamentos/crear', [MedicamentoController::class, 'create'])->name('medicamento.create');
        Route::post('/medicamentos', [MedicamentoController::class, 'store'])->name('medicamento.store');
        Route::get('/medicamentos/{codigo}/editar', [MedicamentoController::class, 'edit'])->name('medicamento.edit');
        Route::put('/medicamentos/{codigo}', [MedicamentoController::class, 'update'])->name('medicamento.update');
        Route::patch('/medicamentos/{codigo}/estado', [MedicamentoController::class, 'cambiarEstado'])->name('medicamento.estado');
    });

    // API de medicamentos de Colombia (Invima - datos.gov.co): /admin/api/medicamentos
    Route::get('/api/medicamentos', [ApiMedicamentoController::class, 'index'])->name('api.medicamentos');

    Route::prefix('procedimientos')->group(function () {
        Route::get('/', [AdminProcedimientoController::class, 'index'])->name('procedimiento.index');
        Route::get('/crear', [AdminProcedimientoController::class, 'create'])->name('procedimiento.create');
        Route::post('/', [AdminProcedimientoController::class, 'store'])->name('procedimiento.store');
        Route::get('/{id}/editar', [AdminProcedimientoController::class, 'edit'])->name('procedimiento.edit');
        Route::put('/{id}', [AdminProcedimientoController::class, 'update'])->name('procedimiento.update');
        Route::patch('/{id}/estado', [AdminProcedimientoController::class, 'cambiarEstado'])->name('procedimiento.estado');

        Route::get('/tipos', [AdminTipoProcedimientoController::class, 'index'])->name('tipo_procedimiento.index');
        Route::get('/tipos/crear', [AdminTipoProcedimientoController::class, 'create'])->name('tipo_procedimiento.create');
        Route::post('/tipos', [AdminTipoProcedimientoController::class, 'store'])->name('tipo_procedimiento.store');
        Route::get('/tipos/{id}/editar', [AdminTipoProcedimientoController::class, 'edit'])->name('tipo_procedimiento.edit');
        Route::put('/tipos/{id}', [AdminTipoProcedimientoController::class, 'update'])->name('tipo_procedimiento.update');
        Route::patch('/tipos/{id}/estado', [AdminTipoProcedimientoController::class, 'cambiarEstado'])->name('tipo_procedimiento.estado');
    });

    // Afiliaciones: http://127.0.0.1:8000/admin/afiliaciones/...
    Route::prefix('afiliaciones')->group(function () {

        // Afiliación: /admin/afiliaciones
        Route::get('/', [AfiliacionController::class, 'index'])->name('afiliaciones.index');
        Route::get('/crear', [AfiliacionController::class, 'create'])->name('afiliaciones.create');
        Route::post('/', [AfiliacionController::class, 'store'])->name('afiliaciones.store');
        Route::get('/{id}/editar', [AfiliacionController::class, 'edit'])->name('afiliaciones.edit');
        Route::get('/{id}/detalle', [AfiliacionController::class, 'detalle'])->name('afiliaciones.detalle');
        Route::get('/detalle-nucleo', [AfiliacionController::class, 'detalleLista'])->name('afiliaciones.detalle.lista');
        Route::put('/{id}', [AfiliacionController::class, 'update'])->name('afiliaciones.update');
        Route::patch('/{id}/estado', [AfiliacionController::class, 'cambiarEstado'])->name('afiliaciones.estado');

        // Beneficiarios: /admin/afiliaciones/beneficiarios
        Route::get('/beneficiarios', [BeneficiarioController::class, 'index'])->name('beneficiarios.index');
        Route::get('/beneficiarios/crear', [BeneficiarioController::class, 'create'])->name('beneficiarios.create');
        Route::post('/beneficiarios', [BeneficiarioController::class, 'store'])->name('beneficiarios.store');
        Route::get('/beneficiarios/creado', [BeneficiarioController::class, 'creado'])->name('beneficiarios.creado');
        Route::get('/beneficiarios/{cc}/editar', [BeneficiarioController::class, 'edit'])->name('beneficiarios.edit');
        Route::put('/beneficiarios/{cc}', [BeneficiarioController::class, 'update'])->name('beneficiarios.update');
        Route::patch('/beneficiarios/{cc}/estado', [BeneficiarioController::class, 'cambiarEstado'])->name('beneficiarios.estado');

        // Estados: /admin/afiliaciones/estados
        Route::get('/estados', [EstadoController::class, 'index'])->name('estados.index');
        Route::get('/estados/crear', [EstadoController::class, 'create'])->name('estados.create');
        Route::post('/estados', [EstadoController::class, 'store'])->name('estados.store');
        Route::get('/estados/{id}/editar', [EstadoController::class, 'edit'])->name('estados.edit');
        Route::put('/estados/{id}', [EstadoController::class, 'update'])->name('estados.update');

    });
});

// Rutas de login (públicas, cualquiera puede acceder al formulario)
Route::get('/login', [LoginController::class, 'mostrarFormulario'])->name('login');
Route::post('/login', [LoginController::class, 'iniciarSesion'])->name('login.post');

// Primer ingreso: cotizante nuevo crea su propia contraseña (pública)
// Flujo en 4 pasos (wizard en una sola vista):
//   1) cédula      -> valida que exista, debe_crear_contrasena=1 y sea cotizante
//   2) correo      -> guarda correo, genera código, lo envía por mail
//   3) código      -> verifica el código de 6 dígitos (máx 5 intentos, 10 min)
//   4) contraseña  -> crea la contraseña y completa el ingreso
Route::get('/primer-ingreso', [PrimerIngresoController::class, 'mostrarFormulario'])->name('primer_ingreso.form');
Route::post('/primer-ingreso/cedula', [PrimerIngresoController::class, 'validarCedula'])->name('primer_ingreso.cedula');
Route::post('/primer-ingreso/correo', [PrimerIngresoController::class, 'enviarCodigo'])->middleware('throttle:3,60')->name('primer_ingreso.correo');
Route::post('/primer-ingreso/codigo', [PrimerIngresoController::class, 'verificarCodigo'])->name('primer_ingreso.codigo');
Route::post('/primer-ingreso/contrasena', [PrimerIngresoController::class, 'guardarContrasena'])->name('primer_ingreso.contrasena');
Route::post('/logout', [LoginController::class, 'cerrarSesion'])->name('logout');

// Panel del Usuario (roles distintos a Administrador)
Route::prefix('usuario')->middleware(['esusuario', 'verificarlicencia'])->group(function () {
    Route::get('/', [PanelUsuarioController::class, 'index'])->name('usuario.inicio');

    // Citas del paciente: agendar y consultar sus propias citas
    Route::get('/citas', [UsuarioCitaController::class, 'misCitas'])->name('usuario.citas.index');
    Route::get('/citas/crear', [UsuarioCitaController::class, 'crearCita'])->name('usuario.citas.create');
    Route::get('/citas/cupos', [UsuarioCitaController::class, 'cupos'])->name('usuario.citas.cupos');
    Route::post('/citas', [UsuarioCitaController::class, 'guardarCita'])->name('usuario.citas.store');

    // Medicamentos formulados al paciente
    Route::get('/medicamentos', [PanelUsuarioController::class, 'misMedicamentos'])->name('usuario.medicamentos.index');

    // Historia clínica, afiliación y perfil del paciente
    Route::get('/historia', [PanelUsuarioController::class, 'miHistoria'])->name('usuario.historia.index');
    Route::get('/afiliacion', [PanelUsuarioController::class, 'miAfiliacion'])->name('usuario.afiliacion.index');
    Route::get('/perfil', [PanelUsuarioController::class, 'miPerfil'])->name('usuario.perfil');
});

// Rutas de recuperación de contraseña (públicas).
// Son tres pasos separados: 1) correo -> 2) código -> 3) nueva contraseña.
Route::get('/recuperar', [RecuperarPasswordController::class, 'mostrarFormularioSolicitud'])->name('recuperar.solicitar.form');
Route::post('/recuperar', [RecuperarPasswordController::class, 'enviarCodigo'])->name('recuperar.solicitar');
Route::get('/verificar-codigo', [RecuperarPasswordController::class, 'mostrarFormularioVerificar'])->name('recuperar.verificar.form');
Route::post('/verificar-codigo', [RecuperarPasswordController::class, 'verificarCodigo'])->name('recuperar.verificar');
Route::get('/nueva-contrasena', [RecuperarPasswordController::class, 'mostrarFormularioNuevaContrasena'])->name('recuperar.nueva.form');
Route::post('/nueva-contrasena', [RecuperarPasswordController::class, 'guardarNuevaContrasena'])->name('recuperar.nueva');

// Panel del Médico. Su cuenta vive en la tabla "medico" (cédula +
// contraseña propia), por eso tiene su propio espacio /medico.
Route::prefix('medico')->middleware(['esmedico', 'verificarlicencia'])->group(function () {
    // Inicio: citas programadas del médico
    Route::get('/', [PanelMedicoController::class, 'inicio'])->name('medico.inicio');

    // Agenda: todos los horarios programados del médico y el detalle
    // de cada horario con sus pacientes agendados.
    Route::get('/horarios', [MedicoHorarioController::class, 'index'])->name('medico.horario.index');
    Route::get('/horarios/{id}', [MedicoHorarioController::class, 'detalle'])->name('medico.horario.detalle');

    // Perfil del médico: actualizar datos básicos y foto.
    Route::get('/perfil', [PerfilController::class, 'editar'])->name('medico.perfil');
    Route::put('/perfil', [PerfilController::class, 'actualizar'])->name('medico.perfil.update');

    // Atención de una cita: historia clínica, procedimientos y medicamentos
    Route::get('/citas/{id}', [ConsultaMedicaController::class, 'show'])->name('medico.cita.show');
    Route::post('/citas/{id}/historia', [ConsultaMedicaController::class, 'storeHistoria'])->name('medico.cita.historia');
    Route::post('/citas/{id}/diagnostico', [ConsultaMedicaController::class, 'storeDiagnostico'])->name('medico.cita.diagnostico');
    Route::post('/citas/{id}/enfermedad', [ConsultaMedicaController::class, 'storeEnfermedad'])->name('medico.cita.enfermedad');
    Route::post('/citas/{id}/procedimiento', [ConsultaMedicaController::class, 'storeProcedimiento'])->name('medico.cita.procedimiento');
    Route::post('/citas/{id}/prescripcion', [ConsultaMedicaController::class, 'storePrescripcion'])->name('medico.cita.prescripcion');
    Route::patch('/citas/{id}/atendida', [ConsultaMedicaController::class, 'marcarAtendida'])->name('medico.cita.atendida');

    // Historia médica: buscar la historia de un paciente por cédula
    // y registrar historia clínica, diagnósticos y enfermedades.
    Route::get('/historia', [HistoriaMedicaController::class, 'index'])->name('medico.historia.index');
    Route::post('/historia', [HistoriaMedicaController::class, 'storeHistoria'])->name('medico.historia.store');
    Route::post('/diagnostico', [HistoriaMedicaController::class, 'storeDiagnostico'])->name('medico.diagnostico.store');
    Route::post('/enfermedad', [HistoriaMedicaController::class, 'storeEnfermedad'])->name('medico.enfermedad.store');

    // Procedimientos: buscar por cédula y registrar el procedimiento,
    // su tipo (catálogo) y el detalle del procedimiento.
    Route::get('/procedimientos', [ProcedimientoController::class, 'index'])->name('medico.procedimiento.index');
    Route::post('/procedimiento', [ProcedimientoController::class, 'storeProcedimiento'])->name('medico.procedimiento.store');
    Route::post('/tipo-procedimiento', [ProcedimientoController::class, 'storeTipoProcedimiento'])->name('medico.tipo_procedimiento.store');
    Route::post('/detalle-procedimiento', [ProcedimientoController::class, 'storeDetalleProcedimiento'])->name('medico.detalle_procedimiento.store');

    // Edición de registros clínicos (el médico solo edita los suyos; no se elimina nada).
    Route::put('/historia/{id}', [HistoriaMedicaController::class, 'updateHistoria'])->name('medico.historia.update');
    Route::put('/diagnostico/{id}', [HistoriaMedicaController::class, 'updateDiagnostico'])->name('medico.diagnostico.update');
    Route::put('/enfermedad/{id}', [HistoriaMedicaController::class, 'updateEnfermedad'])->name('medico.enfermedad.update');
    Route::put('/procedimiento/{id}', [ProcedimientoController::class, 'updateProcedimiento'])->name('medico.procedimiento.update');
    Route::put('/detalle-procedimiento/{id}', [ProcedimientoController::class, 'updateDetalleProcedimiento'])->name('medico.detalle_procedimiento.update');
    Route::put('/prescripcion/{id}', [ConsultaMedicaController::class, 'updatePrescripcion'])->name('medico.prescripcion.update');
});

// Panel del Farmacéutico: /farmacia/...
// Ve las prescripciones de los pacientes, registra la entrega de los
// medicamentos y administra el stock del inventario.
Route::prefix('farmacia')->middleware(['esfarmaceuta', 'verificarlicencia'])->group(function () {
    // Dashboard: http://127.0.0.1:8000/farmacia
    Route::get('/', [FarmaciaController::class, 'inicio'])->name('farmacia.inicio');

    // Prescripciones: listado con filtro de estado y búsqueda de paciente/medicamento
    Route::get('/prescripciones', [FarmaciaController::class, 'prescripciones'])->name('farmacia.prescripciones.index');
    Route::get('/prescripciones/{id}', [FarmaciaController::class, 'detalle'])->name('farmacia.prescripciones.detalle');
    Route::post('/prescripciones/{id}/entregar', [FarmaciaController::class, 'entregar'])->name('farmacia.prescripciones.entregar');

    // Inventario: el farmacéutico registra cuántas unidades hay de cada medicamento
    Route::get('/inventario', [FarmaciaController::class, 'inventario'])->name('farmacia.inventario.index');
    Route::put('/inventario/{codigo}', [FarmaciaController::class, 'actualizarStock'])->name('farmacia.inventario.stock');
});

// Login del Superadministrador (dueños de PrismaSalud): URL aparte, NO es el login de IPS.
// throttle:20,1 -> máximo 20 intentos por minuto.
Route::prefix('super-admin')->group(function () {
    Route::get('/login', [SuperAdminLoginController::class, 'mostrarFormulario'])->name('superadmin.login');
    Route::post('/login', [SuperAdminLoginController::class, 'iniciarSesion'])->middleware('throttle:20,1')->name('superadmin.login.post');
    Route::get('/login/codigo', [SuperAdminLoginController::class, 'mostrarCodigo'])->name('superadmin.login.codigo');
    Route::post('/login/codigo', [SuperAdminLoginController::class, 'verificarCodigo'])->middleware('throttle:20,1')->name('superadmin.login.codigo.verificar');
    Route::post('/login/codigo/reenviar', [SuperAdminLoginController::class, 'reenviarCodigo'])->middleware('throttle:20,1')->name('superadmin.login.codigo.reenviar');
    Route::post('/logout', [SuperAdminLoginController::class, 'cerrarSesion'])->name('superadmin.logout');

    // Recuperar contraseña del Superadministrador (3 pasos)
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
