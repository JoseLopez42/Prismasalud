<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Licencia;
use App\Models\Plan;
use App\Models\Transaccion;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $usuarioActual = Usuario::find($_SESSION['usuario_cedula']);

        $estadisticas = [
            ['icono' => 'citas',       'numero' => '128',   'texto' => 'Citas de hoy'],
            ['icono' => 'afiliados',   'numero' => '3,542', 'texto' => 'Afiliados activos'],
            ['icono' => 'user-circle', 'numero' => '35',    'texto' => 'Médicos activos'],
            ['icono' => 'medicamentos','numero' => '212',   'texto' => 'Entregas de medicamentos'],
        ];

        // Licencia de la IPS de ESTE Administrador (no la ultima creada
        // en todo el sistema, sino la que pertenece a su propia cuenta).
        $licenciaActual = $usuarioActual->licencia;

        return view('admin.dashboard', compact('usuarioActual', 'estadisticas', 'licenciaActual'))
            ->with('seccionActiva', 'dashboard');
    }

    public function miPlan()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $usuarioActual = Usuario::find($_SESSION['usuario_cedula']);
        $licenciaActual = $usuarioActual->licencia;

        // Si aún no está activada (inactiva), mostrar como pendiente.
        $licenciaPendiente = $licenciaActual ? null : Licencia::where('usuario_cedula', $usuarioActual->cedula)
            ->where('estado', 'inactiva')
            ->latest()
            ->first();

        $planInfo = $licenciaActual
            ? Plan::porSlug($licenciaActual->plan)
            : null;

        $historialPagos = $licenciaActual
            ? Transaccion::where('licencia_id', $licenciaActual->id)
                ->orderByDesc('created_at')
                ->get()
            : ($licenciaPendiente ? Transaccion::where('licencia_id', $licenciaPendiente->id)->orderByDesc('created_at')->get() : collect());

        return view('admin.mi-plan', compact('licenciaActual', 'licenciaPendiente', 'planInfo', 'historialPagos'))
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