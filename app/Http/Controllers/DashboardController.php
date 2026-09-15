<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
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
            ['icono' => '📅', 'numero' => '128',   'texto' => 'Citas de hoy'],
            ['icono' => '👥', 'numero' => '3,542', 'texto' => 'Afiliados activos'],
            ['icono' => '⚕️', 'numero' => '35',    'texto' => 'Médicos activos'],
            ['icono' => '💊', 'numero' => '212',   'texto' => 'Entregas de medicamentos'],
        ];

        return view('admin.dashboard', compact('usuarioActual', 'estadisticas'));
    }
}