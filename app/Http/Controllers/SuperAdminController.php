<?php

namespace App\Http\Controllers;

use App\Models\Licencia;
use App\Models\Transaccion;
use Illuminate\Support\Facades\DB;

class SuperAdminController extends Controller
{
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }

        $licencias = Licencia::with('planModelo')->orderByDesc('created_at')->get();

        $tipoAdmin = DB::table('tipos_usuario')->where('nombre', 'Administrador')->value('id') ?? 1;

        $suspendida = fn ($l) => $l->estado === 'suspendida';

        $resumen = [
            'total' => $licencias->count(),
            'activas' => $licencias->filter(fn ($l) => ! $suspendida($l) && ! $l->estaVencida())->count(),
            'por_vencer' => $licencias->filter(fn ($l) => ! $suspendida($l) && $l->estaPorVencer())->count(),
            'vencidas' => $licencias->filter(fn ($l) => $l->estaVencida())->count(),
            'ingresos' => Transaccion::where('estado', 'aprobado')->sum('monto'),
            'administradores' => DB::table('usuarios')->where('tipo_usuario_id', $tipoAdmin)->count(),
        ];

        // Reparto para la barra de estados (cada empresa cae en un solo grupo)
        $reparto = [
            'suspendidas' => $licencias->filter(fn ($l) => $suspendida($l))->count(),
            'vencidas' => $licencias->filter(fn ($l) => ! $suspendida($l) && $l->estaVencida())->count(),
            'por_vencer' => $licencias->filter(fn ($l) => ! $suspendida($l) && $l->estaPorVencer())->count(),
        ];
        $reparto['al_dia'] = $resumen['total'] - array_sum($reparto);

        return view('superadmin.dashboard', [
            'nombre' => $_SESSION['superadmin_nombre'] ?? 'Superadministrador',
            'resumen' => $resumen,
            'reparto' => $reparto,
            'recientes' => $licencias->take(8),
            // Hasta 30 días: la pantalla deja elegir 7, 15 o 30
            'porVencer' => $licencias
                ->filter(fn ($l) => ! $suspendida($l) && ! $l->estaVencida() && $l->diasRestantes() <= 30)
                ->sortBy('fecha_fin')
                ->take(12),
        ]);
    }
}