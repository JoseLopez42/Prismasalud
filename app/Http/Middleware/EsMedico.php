<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EsMedico
{
    /**
     * Verifica que haya una sesión activa de un MÉDICO. El médico
     * se autentica contra la tabla "medico" (su cuenta tiene su
     * propia contraseña) y queda marcado en $_SESSION['medico_cedula'].
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['medico_cedula'])) {
            return redirect('/login')->with('error', 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}
