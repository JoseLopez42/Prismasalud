<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    /**
     * Primer candado (José): revisa la base declarada antes de parent::setUp().
     * Así se detiene RefreshDatabase antes de que pueda tocar una base real.
     */
    protected function setUp(): void
    {
        $baseConfigurada = (string) env('DB_DATABASE', '');

        if (! str_ends_with($baseConfigurada, '_test')) {
            throw new RuntimeException("PRUEBAS BLOQUEADAS: la base configurada es '{$baseConfigurada}'. Las pruebas solo pueden usar bases terminadas en _test (prisma_test).");
        }

        parent::setUp();
    }

    /**
     * Segundo candado (Paula): revisa la conexión real que Laravel ya resolvió.
     * Sirve para detectar config:cache: env() puede decir prisma_test mientras
     * la configuración cacheada apunta a otra base.
     *
     * Va en setUpTraits() y no en beforeRefreshingDatabase() porque el trait
     * RefreshDatabase define su propio beforeRefreshingDatabase() vacío y,
     * en PHP, el método del trait le gana al heredado de TestCase: aquí nunca
     * se ejecutaría. Como segundo motivo, setUpTraits() corre en TODOS los
     * tests, incluso en los que no usan RefreshDatabase.
     */
    protected function setUpTraits()
    {
        $baseReal = (string) DB::connection()->getDatabaseName();

        if (! str_ends_with($baseReal, '_test')) {
            throw new RuntimeException("PRUEBAS BLOQUEADAS: la conexión real apunta a '{$baseReal}'. Corre php artisan config:clear antes de las pruebas: la configuración en caché puede apuntar a la base real.");
        }

        return parent::setUpTraits();
    }
}
