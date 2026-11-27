<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    /**
     * Candado de seguridad: los tests NUNCA pueden escribir en la base real
     * "unoa", donde están los datos cifrados de los beneficiarios.
     *
     * Va en setUpTraits() y no en beforeRefreshingDatabase() porque el trait
     * RefreshDatabase define su propio beforeRefreshingDatabase() vacío y,
     * en PHP, el método del trait le gana al heredado de TestCase: aquí nunca
     * se ejecutaría. Como segundo motivo, setUpTraits() corre en TODOS los
     * tests, incluso en los que no usan RefreshDatabase.
     */
    protected function setUpTraits()
    {
        $baseDatos = DB::connection()->getDatabaseName();

        if ($baseDatos !== 'unoa_test') {
            throw new RuntimeException(
                'Los tests solo pueden ejecutarse contra la base unoa_test. Conexión actual: ' . $baseDatos . '.'
            );
        }

        return parent::setUpTraits();
    }
}
