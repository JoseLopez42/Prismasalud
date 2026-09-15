<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EsAdministrador
{
    /**
     * Verifica que haya una sesión activa (session_start) y que el
     * usuario logueado sea de tipo_usuario_id = 1 (Administrador).
     * Si no cumple, lo redirige al login antes de llegar al controlador.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['usuario_cedula']) || $_SESSION['tipo_usuario_id'] != 1) {
            return redirect('/login')->with('error', 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}