<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\InvimaMedicamentoService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ApiMedicamentoController extends Controller
{
    private const POR_PAGINA = 20;

    public function index(Request $request, InvimaMedicamentoService $servicio)
    {
        $buscar = trim((string) $request->query('buscar', ''));
        $pagina = max(1, (int) $request->query('page', 1));

        $resultado = $servicio->buscar($buscar);
        $medicamentos = $resultado['medicamentos'];
        $error = $resultado['error'];

        $coleccion = collect($medicamentos);
        $paginador = new LengthAwarePaginator(
            $coleccion->forPage($pagina, self::POR_PAGINA)->values(),
            $coleccion->count(),
            self::POR_PAGINA,
            $pagina,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $posicionInicial = ($pagina - 1) * self::POR_PAGINA + 1;
        $limite = InvimaMedicamentoService::LIMITE;
        $seccionActiva = 'api';

        return view('admin.api.medicamentos', compact(
            'paginador', 'buscar', 'error', 'posicionInicial', 'limite', 'seccionActiva'
        ));
    }
}
