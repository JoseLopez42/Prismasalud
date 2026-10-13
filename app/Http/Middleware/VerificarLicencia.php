<?php

namespace App\Http\Middleware;

use App\Models\Usuario;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarLicencia
{
    // Rutas que siempre deben quedar accesibles, incluso con la licencia
    // vencida, para que el Administrador pueda renovar su plan.
    protected array $rutasPermitidas = [
        'admin.plan',
        'admin.planes',
        'comprar.form',
        'comprar.procesar',
        'logout',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (in_array($request->route()?->getName(), $this->rutasPermitidas, true)) {
            return $next($request);
        }

        $usuarioActual = isset($_SESSION['usuario_cedula'])
            ? Usuario::find($_SESSION['usuario_cedula'])
            : null;

        $licencia = $usuarioActual?->licencia;

        if (! $licencia || $licencia->estado !== 'activa') {
            return redirect()->route('admin.plan')
                ->with('error', 'Tu licencia aún no está activa. Espera la aprobación del superadministrador para acceder al panel.');
        }

        if ($licencia->estaVencida()) {
            if ($licencia->estado !== 'inactiva') {
                $licencia->estado = 'inactiva';
                $licencia->save();
            }
            return redirect()->route('admin.plan')
                ->with('error', 'Tu licencia está vencida. Renueva tu plan para seguir usando PrismaSalud.');
        }

        return $next($request);
    }
}