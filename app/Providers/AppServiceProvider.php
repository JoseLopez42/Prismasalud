<?php

namespace App\Providers;

use App\Services\Pagos\PagoSimulado;
use App\Services\Pagos\PasarelaPago;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // REC-05: sin fallback: una pasarela no integrada no debe aprobar compras sin cobrar.
        // La configuración se lee al resolver el servicio, no al registrar el provider,
        // para que un cambio de pasarela en la prueba se refleje sin re-registrar el provider.
        $this->app->bind(PasarelaPago::class, function () {
            $pasarela = config('services.pagos.pasarela', 'simulada');

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
        //
    }
}
