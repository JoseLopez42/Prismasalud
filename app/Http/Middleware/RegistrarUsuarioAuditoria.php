<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/**
 * DBA-03 (parte 2): le dice a MariaDB QUIÉN está usando la app.
 *
 * La app entra a la base siempre como prisma_app, así que USER() no sirve
 * para saber qué persona hizo un cambio. Antes de cada petición, este
 * middleware lee la cédula de la sesión (session_start, PHP nativo) y la
 * guarda en la variable de MariaDB @usuario_app. Los triggers de
 * auditoria_clinica la copian en la columna usuario_app.
 *
 * Ley 2015 de 2020: se debe saber quién modificó la historia clínica.
 */
class RegistrarUsuarioAuditoria
{
    public function handle(Request $request, Closure $next): Response
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Cada tipo de usuario guarda su cédula con un nombre distinto:
        //   médico           -> medico_cedula   (tabla medico)
        //   admin, paciente,
        //   farmaceuta       -> usuario_cedula  (tabla usuarios)
        //   superadmin       -> superadmin_id   (tabla users, también es cédula)
        $cedula = $_SESSION['medico_cedula']
            ?? $_SESSION['usuario_cedula']
            ?? $_SESSION['superadmin_id']
            ?? null;

        // Variable de la conexión actual; si nadie tiene sesión queda NULL.
        DB::statement('SET @usuario_app = ?', [$cedula]);

        return $next($request);
    }
}