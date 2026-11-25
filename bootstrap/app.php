<?php

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
            'esadministrador'  => \App\Http\Middleware\EsAdministrador::class,
            'esusuario'        => \App\Http\Middleware\EsUsuario::class,
            'esmedico'         => \App\Http\Middleware\EsMedico::class,
            'esfarmaceuta'     => \App\Http\Middleware\EsFarmaceuta::class,
            'essuperadmin'     => \App\Http\Middleware\EsSuperAdmin::class,
            'verificarlicencia' => \App\Http\Middleware\VerificarLicencia::class,
        ]);

        // DBA-03: en TODAS las páginas web, antes de cada petición, le dice a
        // MariaDB qué persona tiene la sesión (@usuario_app) para la auditoría.
        $middleware->web(append: [
            \App\Http\Middleware\RegistrarUsuarioAuditoria::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();