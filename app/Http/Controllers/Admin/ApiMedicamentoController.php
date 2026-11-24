<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * API de medicamentos de Colombia.
 *
 * Fuente: datos abiertos del Invima (datos.gov.co), conjunto
 * "Código Único de Medicamentos" (CUM).
 */
class ApiMedicamentoController extends Controller
{
    // Dirección de la API (conjunto de datos CUM del Invima)
    private const API_URL = 'https://www.datos.gov.co/resource/i7cb-raxc.json';

    // Clave de la API (App Token de datos.gov.co). Es opcional:
    // si se deja vacía, la API funciona igual.
    private $apiKey = "";

    // Cuántas presentaciones pedirle a la API (de las más comunes a las menos)
    private const FILAS_API = 2000;

    // Cuántos medicamentos mostrar en total (los más comunes)
    private const LIMITE = 300;

    // Cuántos mostrar por página
    private const POR_PAGINA = 20;

    public function index(Request $request)
    {
        // La clave está definida arriba, en la propiedad $apiKey
        $apiKey = $this->apiKey;

        $buscar = trim((string) $request->query('buscar', ''));
        $pagina = max(1, (int) $request->query('page', 1));

        // Armamos la URL con los parámetros de la consulta (lenguaje SoQL)
        $url = self::API_URL . '?' . http_build_query([
            '$select' => 'principioactivo, formafarmaceutica, cantidad, unidadmedida, '
                       . 'max(descripcionatc) as descripcionatc, '
                       . "min(expedientecum || '-' || consecutivocum) as codigo, "
                       . 'count(*) as registros',
            '$where'  => $this->condicion($buscar),
            '$group'  => 'principioactivo, formafarmaceutica, cantidad, unidadmedida',
            '$order'  => 'count(*) DESC',
            '$limit'  => self::FILAS_API,
        ], '', '&', PHP_QUERY_RFC3986);

        // Consultamos la API
        $resultado = $this->consultarApi($url, $apiKey);
        $datos = $resultado['datos'];
        $error = $resultado['error'];

        // 1) Agrupamos por medicamento, para que todas las presentaciones
        //    de ACETAMINOFEN queden juntas (una debajo de la otra).
        $grupos = [];

        if (is_array($datos)) {
            foreach ($datos as $med) {
                $nombre = $med['principioactivo'] ?? 'N/A';

                // Como la API viene ordenada de más común a menos común,
                // el primer medicamento que aparece es el más común.
                $grupos[$nombre][] = [
                    'codigo'       => $med['codigo'] ?? '',
                    'nombre'       => $nombre,
                    'presentacion' => trim(($med['formafarmaceutica'] ?? '') . ' ' . ($med['cantidad'] ?? '') . ' ' . ($med['unidadmedida'] ?? '')),
                    'descripcion'  => $med['descripcionatc'] ?? '',
                ];
            }
        }

        // 2) Nos quedamos con los 300 medicamentos más comunes
        $grupos = array_slice($grupos, 0, self::LIMITE);

        // 3) Una fila por presentación: cada una con su propio código
        $medicamentos = [];
        foreach ($grupos as $presentaciones) {
            foreach ($presentaciones as $fila) {
                $medicamentos[] = $fila;
            }
        }

        // Paginamos aquí mismo: de los 300, mostramos 20 por página
        $coleccion = collect($medicamentos);
        $paginador = new LengthAwarePaginator(
            $coleccion->forPage($pagina, self::POR_PAGINA)->values(),
            $coleccion->count(),
            self::POR_PAGINA,
            $pagina,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $posicionInicial = ($pagina - 1) * self::POR_PAGINA + 1;
        $limite = self::LIMITE;
        $seccionActiva = 'api';

        return view('admin.api.medicamentos', compact(
            'paginador', 'buscar', 'error', 'posicionInicial', 'limite', 'seccionActiva'
        ));
    }

    /**
     * Consulta la API con cURL y devuelve ['datos' => ..., 'error' => ...].
     */
    private function consultarApi($url, $key)
    {
        // 1. INICIALIZAR
        $ch = curl_init($url);

        // 2. CONFIGURAR
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_USERAGENT, "PrismaSalud-PHP-Client/1.0");

        // Encabezados: datos.gov.co recibe la clave en "X-App-Token"
        // (si no hay clave, la API funciona igual, pero con límite de consultas)
        $encabezados = ["Accept: application/json"];
        if (!empty($key)) {
            $encabezados[] = "X-App-Token: " . $key;
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $encabezados);

        // 3. EJECUTAR
        $respuesta = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_errno($ch) ? curl_error($ch) : null;

        // 4. CERRAR
        curl_close($ch);

        // DEPURACION: si falla, devolvemos el motivo para mostrarlo en la vista
        if ($error || $httpCode !== 200) {
            return [
                'datos' => null,
                'error' => [
                    'codigo'  => $httpCode,
                    'detalle' => $error ?: 'Sin error especifico',
                    'url'     => $url,
                ],
            ];
        }

        return [
            'datos' => json_decode($respuesta, true),
            'error' => null,
        ];
    }

    /**
     * Filtro: solo CUM activos y, si se escribió algo en el buscador,
     * que coincida con el principio activo o con el nombre comercial.
     */
    private function condicion($buscar)
    {
        $where = "estadocum = 'Activo'";

        if ($buscar !== '') {
            // La API guarda todo en MAYÚSCULAS; escapamos comillas simples
            $texto = str_replace("'", "''", mb_strtoupper($buscar, 'UTF-8'));
            $where .= " AND (upper(principioactivo) like '%{$texto}%' OR upper(producto) like '%{$texto}%')";
        }

        return $where;
    }
}