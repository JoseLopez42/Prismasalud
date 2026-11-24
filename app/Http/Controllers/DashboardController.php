<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Licencia;
use App\Models\Plan;
use App\Models\Transaccion;
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

        // NOTA: estos números son de ejemplo (placeholder), tal como
        // en el diseño de Figma. Cuando construyamos cada módulo
        // (citas, afiliados, médicos, farmacia), estos valores se
        // reemplazan por consultas reales a la base de datos.
        $estadisticas = [
            ['icono' => 'citas',       'numero' => '128',   'texto' => 'Citas de hoy'],
            ['icono' => 'afiliados',   'numero' => '3,542', 'texto' => 'Afiliados activos'],
            ['icono' => 'user-circle', 'numero' => '35',    'texto' => 'Médicos activos'],
            ['icono' => 'medicamentos','numero' => '212',   'texto' => 'Entregas de medicamentos'],
        ];

        return view('admin.dashboard', compact('usuarioActual', 'estadisticas'))->with('seccionActiva', 'dashboard');
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
