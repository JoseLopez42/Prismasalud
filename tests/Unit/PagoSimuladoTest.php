<?php

namespace Tests\Unit;

use App\Services\Pagos\PagoSimulado;
use PHPUnit\Framework\TestCase;

class PagoSimuladoTest extends TestCase
{
    /**
     * Para tarjeta el servidor genera la referencia; nunca recibe datos de la tarjeta.
     */
    public function test_tarjeta_credito_genera_referencia_sim_y_estado_aprobado(): void
    {
        $resultado = (new PagoSimulado)->iniciarPago([
            'metodo_pago' => 'tarjeta_credito',
        ]);

        $this->assertStringStartsWith('SIM-', $resultado['referencia']);
        $this->assertSame('aprobado', $resultado['estado']);
    }

    /**
     * En PSE el cliente escribe la referencia y la pasarela la conserva.
     */
    public function test_pse_conserva_la_referencia_del_cliente(): void
    {
        $resultado = (new PagoSimulado)->iniciarPago([
            'metodo_pago' => 'pse',
            'referencia' => 'PSE-2026-0001',
        ]);

        $this->assertSame('PSE-2026-0001', $resultado['referencia']);
    }

    /**
     * El nombre guardado en transacciones.pasarela debe ser exacto.
     */
    public function test_nombre_devuelve_simulada(): void
    {
        $this->assertSame('simulada', (new PagoSimulado)->nombre());
    }
}
