<?php

use App\Http\Middleware\EsAdministrador;
use App\Http\Middleware\EsFarmaceuta;
use App\Http\Middleware\EsMedico;
use App\Http\Middleware\EsSuperAdmin;
use App\Http\Middleware\EsUsuario;
use App\Http\Middleware\RegistrarUsuarioAuditoria;
use App\Http\Middleware\VerificarLicencia;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'esadministrador' => EsAdministrador::class,
            'esusuario' => EsUsuario::class,
            'esmedico' => EsMedico::class,
            'esfarmaceuta' => EsFarmaceuta::class,
            'essuperadmin' => EsSuperAdmin::class,
            'verificarlicencia' => VerificarLicencia::class,
        ]);

        // DBA-03: en TODAS las páginas web, antes de cada petición, le dice a
        // MariaDB qué persona tiene la sesión (@usuario_app) para la auditoría.
        $middleware->web(append: [
            RegistrarUsuarioAuditoria::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // REC-05: si alguien envía datos de tarjeta a mano, no deben quedar en la sesión.
        // Así old() nunca los devuelve al formulario. También se excluyen las contraseñas.
        $exceptions->dontFlash([
            'numero_tarjeta',
            'vencimiento_tarjeta',
            'nombre_tarjeta',
            'cvv',
            'codigo',
            'contrasena',
            'contrasena_confirmation',
            'password',
            'password_confirmation',
            'password_actual',
        ]);
    })->create();
