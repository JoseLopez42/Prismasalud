<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EsUsuario
{
    /**
     * Verifica que haya una sesión activa y que el usuario logueado
     * NO sea Administrador (tipo_usuario_id = 1), es decir, que sea
     * un usuario regular con acceso a su panel personal.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['usuario_cedula']) || $_SESSION['tipo_usuario_id'] == 1) {
            return redirect('/login')->with('error', 'No tienes permiso para acceder a esta sección.');
        }

        // El farmacéutico tiene su propio panel: no entra al de pacientes.
        if ($_SESSION['tipo_usuario_id'] == 4) {
            return redirect()->route('farmacia.inicio');
        }

        return $next($request);
    }
}