<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Pagos (tabla "transacciones"): solo consulta. De aquí sale el total de "Ingresos aprobados" del inicio.
 */
class SuperAdminPagoController extends Controller
{
    public function index(Request $request)
    {
        $f = [
            'q' => trim((string) $request->query('q', '')),
            'estado' => (string) $request->query('estado', ''),
            'tipo' => (string) $request->query('tipo', ''),
            'plan' => (string) $request->query('plan', ''),
            'desde' => $this->fecha($request->query('desde')),
            'hasta' => $this->fecha($request->query('hasta')),
        ];

        $base = DB::table('transacciones as t')
            ->leftJoin('licencias as l', 'l.id', '=', 't.licencia_id')
            ->leftJoin('planes as p', 'p.id', '=', 't.plan_id');

        if ($f['q'] !== '') {
            $base->where(function ($q) use ($f) {
                $q->where('l.nombre_ips', 'like', "%{$f['q']}%")
                  ->orWhere('l.nit', 'like', "%{$f['q']}%")
                  ->orWhere('t.referencia_pago', 'like', "%{$f['q']}%");
            });
        }
        if ($f['estado'] !== '') { $base->where('t.estado', $f['estado']); }
        if ($f['tipo'] !== '') { $base->where('t.tipo', $f['tipo']); }
        if ($f['plan'] !== '' && ctype_digit($f['plan'])) { $base->where('t.plan_id', (int) $f['plan']); }
        if ($f['desde'] !== '') { $base->whereDate('t.created_at', '>=', $f['desde']); }
        if ($f['hasta'] !== '') { $base->whereDate('t.created_at', '<=', $f['hasta']); }

        $totales = (clone $base)->selectRaw(
            "COUNT(*) as n, COALESCE(SUM(CASE WHEN t.estado = 'aprobado' THEN t.monto ELSE 0 END), 0) as aprobado"
        )->first();

        $pagos = (clone $base)
            ->select('t.id', 't.monto', 't.tipo', 't.metodo_pago', 't.referencia_pago', 't.estado', 't.created_at',
                     'l.id as licencia_id', 'l.nombre_ips', 'l.nit', 'p.nombre as plan_nombre')
            ->orderByDesc('t.created_at')->orderByDesc('t.id')
            ->paginate(15)->withQueryString();

        return view('superadmin.pagos.index', [
            'pagos' => $pagos,
            'f' => $f,
            'n' => (int) $totales->n,
            'aprobado' => (int) $totales->aprobado,
            'estados' => DB::table('transacciones')->distinct()->orderBy('estado')->pluck('estado'),
            'tipos' => DB::table('transacciones')->distinct()->orderBy('tipo')->pluck('tipo'),
            'planes' => Plan::ordenados()->get(['id', 'nombre']),
        ]);
    }

    private function fecha($valor): string
    {
        $valor = (string) $valor;
        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $valor) && strtotime($valor) !== false ? $valor : '';
    }
}