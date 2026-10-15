<?php

namespace App\Http\Controllers;

use App\Models\Admin\CitaMedica;
use App\Models\Admin\Diagnostico;
use App\Models\Admin\Enfermedad;
use App\Models\Admin\Eps;
use App\Models\Admin\HistoriaClinica;
use App\Models\Admin\Prescripcion;
use App\Models\Admin\Procedimiento;
use App\Models\Usuario;
use Illuminate\Http\Request;

class PanelUsuarioController extends Controller
{
    /**
     * Muestra el index del panel del Usuario (lo que ve una persona
     * con rol distinto a Administrador al iniciar sesión).
     */
    public function index()
    {
        $usuario = Usuario::find($this->cedulaUsuario());
        $hoy = now()->toDateString();

        // Resumen rápido del usuario con datos reales.
        $resumen = [
            [
                'icono'  => 'citas',
                'numero' => CitaMedica::where('cedula_usuario', $usuario->cedula)
                    ->where('activo', true)
                    ->whereDate('fecha', '>=', $hoy)
                    ->count(),
                'texto'  => 'Citas programadas',
            ],
            [
                'icono'  => 'medicamentos',
                'numero' => Prescripcion::where('cedula_paciente', $usuario->cedula)->count(),
                'texto'  => 'Medicamentos formulados',
            ],
            [
                'icono'  => 'historia',
                'numero' => HistoriaClinica::where('cedula_paciente', $usuario->cedula)->count(),
                'texto'  => 'Historias clínicas',
            ],
            [
                'icono'  => 'eps',
                'numero' => $usuario->nit_eps ? 'Activa' : 'Pendiente',
                'texto'  => 'Mi afiliación',
            ],
        ];

        // Servicios disponibles para el usuario (con su ruta real).
        $servicios = [
            ['icono' => 'citas',         'titulo' => 'Agendar cita',     'descripcion' => 'Solicita una cita médica y queda asignada a un médico.', 'ruta' => route('usuario.citas.create')],
            ['icono' => 'lista',         'titulo' => 'Ver mis citas',    'descripcion' => 'Consulta el historial y el estado de tus citas.',          'ruta' => route('usuario.citas.index')],
            ['icono' => 'historia',      'titulo' => 'Historia clínica', 'descripcion' => 'Accede a tus diagnósticos y a tu historia clínica.',       'ruta' => route('usuario.historia.index')],
            ['icono' => 'medicamentos',  'titulo' => 'Mis medicamentos', 'descripcion' => 'Revisa los medicamentos que te han formulado.',           'ruta' => route('usuario.medicamentos.index')],
            ['icono' => 'afiliaciones',  'titulo' => 'Mi afiliación',    'descripcion' => 'Consulta los datos de tu afiliación en salud.',            'ruta' => route('usuario.afiliacion.index')],
            ['icono' => 'perfil',        'titulo' => 'Mi perfil',        'descripcion' => 'Consulta tus datos personales y de contacto.',          'ruta' => route('usuario.perfil')],
        ];

        // Próximas citas del paciente (para el panel de inicio).
        $proximasCitas = CitaMedica::with(['medico.especialidad', 'consultorio', 'tipoCita', 'estadoCita'])
            ->where('cedula_usuario', $usuario->cedula)
            ->where('activo', true)
            ->whereDate('fecha', '>=', $hoy)
            ->orderBy('fecha')
            ->orderBy('hora_inicio')
            ->limit(3)
            ->get();

        $proximaCita = $proximasCitas->first();

        return view('usuario.inicio', compact('usuario', 'resumen', 'servicios', 'proximaCita', 'proximasCitas'))
            ->with('seccionActiva', 'inicio');
    }

    /**
     * Medicamentos que los médicos le han formulado al paciente.
     */
    public function misMedicamentos()
    {
        $prescripciones = Prescripcion::with(['medico', 'cita'])
            ->where('cedula_paciente', $this->cedulaUsuario())
            ->orderBy('fecha', 'desc')
            ->get();

        return view('usuario.medicamentos.index', compact('prescripciones'))->with('seccionActiva', 'medicinas');
    }

    /**
     * Historia clínica del paciente: historias, diagnósticos,
     * enfermedades y procedimientos registrados por los médicos.
     */
    public function miHistoria()
    {
        $cedula = $this->cedulaUsuario();

        $historias = HistoriaClinica::with(['medico.especialidad', 'cita', 'diagnosticos', 'enfermedades'])
            ->where('cedula_paciente', $cedula)
            ->orderBy('fecha', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $diagnosticos = Diagnostico::with(['medico.especialidad', 'historiaClinica'])
            ->where('cedula_paciente', $cedula)
            ->orderBy('fecha', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $enfermedades = Enfermedad::with(['medico.especialidad', 'historiaClinica'])
            ->where('cedula_paciente', $cedula)
            ->orderBy('fecha', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $procedimientos = Procedimiento::with(['medico.especialidad', 'tipoProcedimiento', 'cita', 'detalles'])
            ->where('cedula_paciente', $cedula)
            ->orderBy('fecha', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return view('usuario.historia.index', compact('historias', 'diagnosticos', 'enfermedades', 'procedimientos'))
            ->with('seccionActiva', 'historia');
    }

    /**
     * Datos de afiliación del paciente (EPS, documento, ciudad).
     */
    public function miAfiliacion()
    {
        $usuario = Usuario::with(['tipoDocumento', 'ciudad', 'tipoUsuario'])->find($this->cedulaUsuario());
        $eps = $usuario->nit_eps ? Eps::find($usuario->nit_eps) : null;

        return view('usuario.afiliacion.index', compact('usuario', 'eps'))->with('seccionActiva', 'afiliacion');
    }

    /**
     * Perfil del paciente.
     */
    public function miPerfil()
    {
        $usuario = Usuario::with(['tipoDocumento', 'ciudad', 'tipoUsuario'])->find($this->cedulaUsuario());

        return view('usuario.perfil', compact('usuario'))->with('seccionActiva', 'perfil');
    }

    // ---------- Ayudas ----------

    private function cedulaUsuario(): int
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return (int) $_SESSION['usuario_cedula'];
    }
}
