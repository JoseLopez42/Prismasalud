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
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

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

        // Licencia de la IPS de ESTE Administrador (no la ultima creada
        // en todo el sistema, sino la que pertenece a su propia cuenta).
        $licenciaActual = $usuarioActual->licencia;

        return view('admin.dashboard', compact('usuarioActual', 'estadisticas', 'citasProximas', 'citasHoy', 'licenciaActual'))
            ->with('seccionActiva', 'dashboard');
    }

    public function miPlan()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $usuarioActual = Usuario::find($_SESSION['usuario_cedula']);
        $licenciaActual = $usuarioActual->licencia;

        $planInfo = $licenciaActual
            ? Plan::porSlug($licenciaActual->plan)
            : null;

        $historialPagos = $licenciaActual
            ? Transaccion::where('licencia_id', $licenciaActual->id)
                ->orderByDesc('created_at')
                ->get()
            : collect();

        return view('admin.mi-plan', compact('licenciaActual', 'planInfo', 'historialPagos'))
            ->with('seccionActiva', 'plan');
    }

    public function planes()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $usuarioActual = Usuario::find($_SESSION['usuario_cedula']);
        $planes = Plan::catalogoArray();

        return view('admin.planes', compact('planes', 'usuarioActual'))
            ->with('seccionActiva', 'plan');
    }
}