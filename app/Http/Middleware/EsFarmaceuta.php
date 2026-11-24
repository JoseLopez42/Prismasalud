<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EsFarmaceuta
{
    /**
     * Verifica que haya una sesión activa de un usuario con el rol
     * Farmaceuta (tipo_usuario_id = 4) para darle acceso al panel
     * de farmacia y entrega de medicamentos.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['usuario_cedula'])
            || !isset($_SESSION['tipo_usuario_id'])
            || $_SESSION['tipo_usuario_id'] != 4) {
            return redirect('/login')->with('error', 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}
