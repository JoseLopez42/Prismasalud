<?php

namespace App\Providers;

use App\Services\Pagos\PagoSimulado;
use App\Services\Pagos\PasarelaPago;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $pasarela = config('services.pagos.pasarela', 'simulada');

        // REC-05: sin fallback: una pasarela no integrada no debe aprobar compras sin cobrar.
        $this->app->bind(PasarelaPago::class, function () use ($pasarela) {
            return match ($pasarela) {
                'simulada' => new PagoSimulado,
                default => throw new RuntimeException("Pasarela de pago no soportada: {$pasarela}"),
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // REC-02: se limita por cuenta (límite fuerte) y por IP, porque en
        // una IPS todo el personal comparte la misma dirección. Se usa el
        // RateLimiter de Laravel, que respeta el CACHE_STORE configurado.
        RateLimiter::for('rec02-login', function (Request $request) {
            return [
                Limit::perMinute(5)->by('cedula:'.sha1((string) $request->input('cedula'))),
                Limit::perMinute(30)->by('ip:'.$request->ip()),
            ];
        });

        RateLimiter::for('rec02-recuperar', function (Request $request) {
            return [
                Limit::perHour(3)->by('correo:'.sha1(strtolower((string) $request->input('correo')))),
                Limit::perHour(10)->by('ip:'.$request->ip()),
            ];
        });

        RateLimiter::for('rec02-verificar', function (Request $request) {
            // En este paso la sesión todavía NO tiene recuperar_cedula:
            // se guarda recién después de validar el código.
            $correo = strtolower((string) $request->session()->get('recuperar_correo'));

            return [
                Limit::perMinute(5)->by('correo:'.sha1($correo)),
                Limit::perMinute(30)->by('ip:'.$request->ip()),
            ];
        });
    }
}
