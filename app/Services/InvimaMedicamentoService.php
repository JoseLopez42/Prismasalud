<?php

namespace App\Services;

class InvimaMedicamentoService
{
    public const LIMITE = 300;

    private const API_URL = 'https://www.datos.gov.co/resource/i7cb-raxc.json';
    private const FILAS_API = 2000;

    public function buscar(string $buscar = ''): array
    {
        $buscar = trim($buscar);

        $url = self::API_URL . '?' . http_build_query([
            '$select' => 'principioactivo, formafarmaceutica, cantidad, unidadmedida, '
                       . 'max(descripcionatc) as descripcionatc, '
                       . "min(expedientecum || '-' || consecutivocum) as codigo, "
                       . 'count(*) as registros',
            '$where' => $this->condicion($buscar),
            '$group' => 'principioactivo, formafarmaceutica, cantidad, unidadmedida',
            '$order' => 'count(*) DESC',
            '$limit' => self::FILAS_API,
        ], '', '&', PHP_QUERY_RFC3986);

        $respuesta = $this->consultarApi($url);

        if ($respuesta['error'] !== null) {
            return ['medicamentos' => [], 'error' => $respuesta['error']];
        }

        $grupos = [];

        foreach ($respuesta['datos'] ?? [] as $med) {
            if (!is_array($med)) {
                continue;
            }

            $nombre = $med['principioactivo'] ?? 'N/A';

            $grupos[$nombre][] = [
                'codigo' => $med['codigo'] ?? '',
                'nombre' => $nombre,
                'presentacion' => trim(($med['formafarmaceutica'] ?? '') . ' ' . ($med['cantidad'] ?? '') . ' ' . ($med['unidadmedida'] ?? '')),
                'descripcion' => $med['descripcionatc'] ?? '',
            ];
        }

        $grupos = array_slice($grupos, 0, self::LIMITE);

        $medicamentos = [];
        $codigosVistos = [];

        foreach ($grupos as $presentaciones) {
            foreach ($presentaciones as $fila) {
                $fila = $this->normalizar($fila);

                if (isset($codigosVistos[$fila['codigo']])) {
                    continue;
                }

                $codigosVistos[$fila['codigo']] = true;
                $medicamentos[] = $fila;
            }
        }

        return ['medicamentos' => $medicamentos, 'error' => null];
    }

    private function normalizar(array $fila): array
    {
        return [
            'codigo' => mb_substr(trim((string) $fila['codigo']), 0, 50),
            'nombre' => mb_substr(trim((string) $fila['nombre']), 0, 150),
            'presentacion' => mb_substr(trim((string) $fila['presentacion']), 0, 100),
            'descripcion' => trim((string) $fila['descripcion']),
        ];
    }

    private function consultarApi(string $url): array
    {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_USERAGENT, 'PrismaSalud-PHP-Client/1.0');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);

        $respuesta = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_errno($ch) ? curl_error($ch) : null;

        curl_close($ch);

        if ($error || $httpCode !== 200) {
            return [
                'datos' => null,
                'error' => [
                    'codigo' => $httpCode,
                    'detalle' => $error ?: 'Sin error especifico',
                    'url' => $url,
                ],
            ];
        }

        return [
            'datos' => json_decode($respuesta, true),
            'error' => null,
        ];
    }

    private function condicion(string $buscar): string
    {
        $where = "estadocum = 'Activo'";

        if ($buscar !== '') {
            $texto = str_replace("'", "''", mb_strtoupper($buscar, 'UTF-8'));
            $where .= " AND (upper(principioactivo) like '%{$texto}%' OR upper(producto) like '%{$texto}%')";
        }

        return $where;
    }
}
