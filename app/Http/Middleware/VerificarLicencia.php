<?php

namespace App\Http\Middleware;

use App\Models\Admin\Medico;
use App\Models\Empresa;
use App\Models\User;
use App\Models\Usuario;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarLicencia
{
    // Mensajes usados por todos los estados de la licencia. Los mismos textos
    // sirven para el Administrador (rama original) y para el resto de roles.
    public const MENSAJE_SIN_IPS = 'No se pudo identificar la IPS asociada a tu cuenta. Comunícate con el administrador de tu IPS.';
    public const MENSAJE_SUSPENDIDA = 'Tu licencia está suspendida. Comunícate con el administrador de tu IPS.';
    public const MENSAJE_VENCIDA = 'Tu licencia está vencida. Renueva tu plan para seguir usando PrismaSalud.';
    public const MENSAJE_NO_ACTIVA = 'Tu licencia aún no está activa. Espera la aprobación del superadministrador para acceder al panel.';

    // Rutas que siempre deben quedar accesibles, incluso con la licencia
    // vencida, para que el Administrador pueda renovar su plan.
    protected array $rutasPermitidas = [
        'admin.plan',
        'admin.planes',
        'admin.pago_aceptado',
        'comprar.form',
        'comprar.procesar',
        'login',
        'logout',
        'planes',
        'licencia.bloqueada',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // El superadministrador vive en "users" y no pertenece a ninguna IPS:
        // su acceso no depende de la licencia de un cliente.
        if (isset($_SESSION['superadmin_id'])) {
            $superadmin = User::find($_SESSION['superadmin_id']);

            if ($superadmin && $superadmin->rol === 'superadmin') {
                return $next($request);
            }
        }

        if (in_array($request->route()?->getName(), $this->rutasPermitidas, true)) {
            return $next($request);
        }

        $tipoUsuario = $_SESSION['tipo_usuario_id'] ?? null;

        // Rama 1: el Administrador (tipo 1) usa su propia licencia, guardada
        // en usuarios.licencia_id. Se conserva el comportamiento original:
        // mensajes, marca de 'inactiva' al vencer y vuelta a "Mi plan".
        if ((int) $tipoUsuario === 1) {
            $usuarioActual = isset($_SESSION['usuario_cedula'])
                ? Usuario::find($_SESSION['usuario_cedula'])
                : null;

            $licencia = $usuarioActual?->licencia;

            if (! $licencia || $licencia->estado !== 'activa') {
                return redirect()->route('admin.plan')
                    ->with('error', self::MENSAJE_NO_ACTIVA);
            }

            if ($licencia->estaVencida()) {
                if ($licencia->estado !== 'inactiva') {
                    $licencia->estado = 'inactiva';
                    $licencia->save();
                }

                return redirect()->route('admin.plan')
                    ->with('error', self::MENSAJE_VENCIDA);
            }

            return $next($request);
        }

        // Rama 2: el resto de roles entra con la licencia de su IPS. La
        // persona se vincula a la EMPRESA, no a una licencia concreta, porque
        // la licencia cambia en cada renovación.
        $persona = null;

        if ((int) $tipoUsuario === 3) {
            $persona = isset($_SESSION['medico_cedula'])
                ? Medico::find($_SESSION['medico_cedula'])
                : null;
        } elseif ((int) $tipoUsuario === 2 || (int) $tipoUsuario === 4) {
            $persona = isset($_SESSION['usuario_cedula'])
                ? Usuario::find($_SESSION['usuario_cedula'])
                : null;
        }

        if (! $persona || ! $persona->empresa_id) {
            return redirect()->route('licencia.bloqueada')
                ->with('error', self::MENSAJE_SIN_IPS);
        }

        $empresa = Empresa::find($persona->empresa_id);
        $licencia = $empresa?->licencia;

        // REC-03: NO se revisa empresas.estado, manda solo la licencia.
        if (! $empresa || ! $licencia) {
            return redirect()->route('licencia.bloqueada')
                ->with('error', self::MENSAJE_SIN_IPS);
        }

        if ($licencia->estado === 'suspendida') {
            return redirect()->route('licencia.bloqueada')
                ->with('error', self::MENSAJE_SUSPENDIDA);
        }

        if ($licencia->estado === 'vencida' || $licencia->estaVencida()) {
            // Solo se escribe 'inactiva' cuando el estado anterior era 'activa'.
            if ($licencia->estado === 'activa') {
                $licencia->estado = 'inactiva';
                $licencia->save();
            }

            return redirect()->route('licencia.bloqueada')
                ->with('error', self::MENSAJE_VENCIDA);
        }

        if ($licencia->estado !== 'activa') {
            return redirect()->route('licencia.bloqueada')
                ->with('error', self::MENSAJE_NO_ACTIVA);
        }

        return $next($request);
    }
}
