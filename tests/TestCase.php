<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    /**
     * REC-07: se revisa antes de parent::setUp() para detener RefreshDatabase
     * si la conexión apunta a una base real en lugar de una base _test.
     */
    protected function setUp(): void
    {
        $baseDatos = (string) env('DB_DATABASE', '');

        if (! str_ends_with($baseDatos, '_test')) {
            throw new RuntimeException("PRUEBAS BLOQUEADAS: la base configurada es '{$baseDatos}'. Las pruebas solo pueden usar bases terminadas en _test (prisma_test).");
        }

        parent::setUp();
    }
}
