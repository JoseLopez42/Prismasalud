<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Deja pasar solo a un Superadministrador con sesión activa.
 * Un Administrador o Usuario de una IPS NO entra por aquí aunque
 * tenga sesión: su sesión guarda "usuario_cedula", no "superadmin_id".
 */
class EsSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $id = $_SESSION['superadmin_id'] ?? null;

        $valido = $id && User::where('id', $id)->where('rol', 'superadmin')->exists();

        if (! $valido) {
            unset($_SESSION['superadmin_id'], $_SESSION['superadmin_nombre']);

            return redirect()->route('superadmin.login')
                ->with('error', 'Inicia sesión para entrar al panel.');
        }

        return $next($request);
    }
}