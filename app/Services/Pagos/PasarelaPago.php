<?php

namespace App\Services\Pagos;

interface PasarelaPago
{
    /**
     * Inicia el pago y devuelve la referencia interna y el estado inicial.
     */
    public function iniciarPago(array $datos): array;

    /**
     * Consulta el estado actual de un pago por su referencia.
     */
    public function consultarEstado(string $referencia): string;

    /**
     * Nombre de la pasarela tal como se guarda en transacciones.pasarela.
     */
    public function nombre(): string;
}
