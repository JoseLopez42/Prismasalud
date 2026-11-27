<?php

namespace App\Services\Pagos;

class PagoSimulado implements PasarelaPago
{
    private const METODOS_TARJETA = ['tarjeta_credito', 'tarjeta_debito'];

    public function iniciarPago(array $datos): array
    {
        $metodo = (string) ($datos['metodo_pago'] ?? '');
        $referencia = trim((string) ($datos['referencia'] ?? ''));

        // REC-05: para tarjeta no hay datos del cliente; el servidor genera la referencia.
        if ($referencia === '' || in_array($metodo, self::METODOS_TARJETA, true)) {
            $referencia = 'SIM-'.strtoupper(bin2hex(random_bytes(6)));
        }

        return [
            'referencia' => $referencia,
            'estado' => 'aprobado',
        ];
    }

    public function consultarEstado(string $referencia): string
    {
        return 'aprobado';
    }

    public function nombre(): string
    {
        return 'simulada';
    }
}
