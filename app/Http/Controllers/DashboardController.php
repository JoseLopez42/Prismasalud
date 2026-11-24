<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Licencia;
use App\Models\Plan;
use App\Models\Transaccion;
use App\Models\Admin\CitaMedica;
use App\Models\Admin\HistoriaClinica;
use App\Models\Admin\Medico;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Muestra el panel principal del Administrador.
     * La protección de "solo Administrador puede entrar" ya la
     * hace el middleware EsAdministrador, asignado en las rutas.
     */
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Traemos el usuario logueado (guardado en la sesión) para
        // mostrar su nombre en el saludo de bienvenida.
        $usuarioActual = Usuario::find($_SESSION['usuario_cedula']);

        $hoy = now()->toDateString();

        // Estadísticas reales (cada tarjeta enlaza a su módulo)
        $estadisticas = [
            [
                'icono' => 'citas',
                'numero' => CitaMedica::whereDate('fecha', $hoy)->where('activo', true)->count(),
                'texto' => 'Citas de hoy',
                'ruta'  => route('cita.index'),
            ],
            [
                'icono' => 'usuarios',
                'numero' => Usuario::where('tipo_usuario_id', 2)->where('activo', true)->count(),
                'texto' => 'Pacientes activos',
                'ruta'  => route('usuarios.index'),
            ],
            [
                'icono' => 'medicos',
                'numero' => Medico::where('estado', 'activo')->count(),
                'texto' => 'Médicos activos',
                'ruta'  => route('medico.index'),
            ],
            [
                'icono' => 'historia',
                'numero' => HistoriaClinica::count(),
                'texto' => 'Historias clínicas',
                'ruta'  => route('historia.index'),
            ],
        ];

        // Panel de citas: próximas citas activas (hoy en adelante)
        $citasProximas = CitaMedica::with(['paciente', 'medico', 'consultorio', 'tipoCita', 'estadoCita'])
            ->where('activo', true)
            ->whereDate('fecha', '>=', $hoy)
            ->orderBy('fecha')
            ->orderBy('hora_inicio')
            ->limit(8)
            ->get();

        $citasHoy = CitaMedica::whereDate('fecha', $hoy)->where('activo', true)->count();

        // Licencia de la IPS de ESTE Administrador (no la última creada
        // en todo el sistema, sino la que pertenece a su propia cuenta).
        $licenciaActual = $usuarioActual?->licencia;

        return view('admin.dashboard', compact('usuarioActual', 'estadisticas', 'citasProximas', 'citasHoy', 'licenciaActual'))
            ->with('seccionActiva', 'dashboard');
    }

    /**
     * Vista "Mi Plan" del Administrador: licencia contratada, vigencia,
     * beneficios, pagos y planes superiores sugeridos.
     */
    public function miPlan()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $usuarioActual = Usuario::find($_SESSION['usuario_cedula']);

        // Licencia ya activada (asignada por el superadmin).
        $licenciaActual = $usuarioActual?->licencia;

        // Si compró pero aún no la activaron, la mostramos como pendiente.
        $licenciaPendiente = $licenciaActual ? null : Licencia::where('usuario_cedula', $usuarioActual?->cedula)
            ->where('estado', 'inactiva')
            ->latest()
            ->first();

        $planInfo = $licenciaActual
            ? Plan::porSlug($licenciaActual->plan)
            : null;

        $licenciaHistorial = $licenciaActual ?: $licenciaPendiente;

        $historialPagos = $licenciaHistorial
            ? Transaccion::where('licencia_id', $licenciaHistorial->id)
                ->orderByDesc('created_at')
                ->get()
            : collect();

        return view('admin.mi-plan', compact('licenciaActual', 'licenciaPendiente', 'planInfo', 'historialPagos'))
            ->with('seccionActiva', 'plan');
    }

    /**
     * Catálogo de planes dentro del panel del Administrador.
     */
    public function planes()
    {
        return view('admin.planes', ['planes' => Plan::catalogoArray()])
            ->with('seccionActiva', 'plan');
    }
}
