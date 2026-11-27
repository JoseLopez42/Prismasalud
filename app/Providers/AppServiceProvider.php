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
        //
    }
}
