<?php

namespace App\Http\Controllers\Farmacia;

use App\Http\Controllers\Controller;
use App\Models\Admin\Medicamento;
use App\Models\Admin\Prescripcion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FarmaciaController extends Controller
{
    /**
     * Inicio del panel del farmacéutico: prescripciones pendientes de
     * entrega, entregas realizadas y estado del inventario.
     */
    public function inicio()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $hoy = now()->toDateString();

        $resumen = [
            ['icono' => 'medicamentos', 'numero' => Prescripcion::disponibleEnFarmacia()->where('estado', 'pendiente')->where('activo', true)->count(), 'texto' => 'Pendientes de entrega'],
            ['icono' => 'entrega',      'numero' => Prescripcion::where('estado', 'entregada')->whereDate('fecha_entrega', $hoy)->count(), 'texto' => 'Entregadas hoy'],
            ['icono' => 'lista',        'numero' => Prescripcion::where('estado', 'entregada')->count(), 'texto' => 'Entregadas (total)'],
            ['icono' => 'inventario',   'numero' => Medicamento::where('activo', true)->where('stock', 0)->count(), 'texto' => 'Medicamentos sin stock'],
        ];

        // Prescripciones que esperan entrega (las más recientes primero)
        $pendientes = Prescripcion::disponibleEnFarmacia()
            ->with(['paciente', 'medico', 'medicamentoCatalogo'])
            ->where('estado', 'pendiente')
            ->where('activo', true)
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        // Últimas entregas registradas
        $ultimasEntregas = Prescripcion::disponibleEnFarmacia()
            ->with(['paciente', 'farmaceutico'])
            ->where('estado', 'entregada')
            ->where('activo', true)
            ->orderByDesc('fecha_entrega')
            ->limit(8)
            ->get();

        $seccionActiva = 'inicio';

        return view('farmacia.inicio', compact('resumen', 'pendientes', 'ultimasEntregas', 'seccionActiva'));
    }

    /**
     * Listado de prescripciones con filtro por estado (pendiente,
     * entregada o todas) y búsqueda por paciente o medicamento.
     */
    public function prescripciones(Request $request)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $estado = $request->query('estado', 'pendiente');
        if (!in_array($estado, ['pendiente', 'entregada', 'todas'])) {
            $estado = 'pendiente';
        }

        $buscar = trim((string) $request->query('buscar', ''));

        $consulta = Prescripcion::disponibleEnFarmacia()
            ->with(['paciente', 'medico', 'medicamentoCatalogo'])
            ->where('activo', true);

        if ($estado !== 'todas') {
            $consulta->where('estado', $estado);
        }

        if ($buscar !== '') {
            $consulta->where(function ($q) use ($buscar) {
                $q->where('cedula_paciente', 'like', "%{$buscar}%")
                  ->orWhere('medicamento', 'like', "%{$buscar}%")
                  ->orWhere('id_medicamento', 'like', "%{$buscar}%")
                  ->orWhereHas('paciente', function ($paciente) use ($buscar) {
                      $paciente->where('nombres', 'like', "%{$buscar}%")
                               ->orWhere('apellidos', 'like', "%{$buscar}%");
                  });
            });
        }

        $prescripciones = $consulta
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        $seccionActiva = 'prescripciones';

        return view('farmacia.prescripciones.index', compact('prescripciones', 'estado', 'buscar', 'seccionActiva'));
    }

    /**
     * Detalle de una prescripción: datos del paciente, del médico y
     * del medicamento, con el formulario de entrega si aún está
     * pendiente (o la información de la entrega si ya se hizo).
     */
    public function detalle($id)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $prescripcion = Prescripcion::with(['paciente', 'medico', 'medicamentoCatalogo', 'farmaceutico', 'cita'])
            ->where('activo', true)
            ->findOrFail($id);

        if ($prescripcion->vencida()) {
            return redirect()
                ->route('farmacia.prescripciones.index', ['estado' => 'pendiente'])
                ->with('error', 'El plazo para reclamar esta prescripción venció y ya no está disponible en farmacia.');
        }

        $seccionActiva = 'prescripciones';

        return view('farmacia.prescripciones.detalle', compact('prescripcion', 'seccionActiva'));
    }

    /**
     * Registra la entrega del medicamento al paciente: marca la
     * prescripción como entregada, descuenta el stock del inventario
     * y guarda quién la entregó, cuántas unidades y una observación.
     */
    public function entregar(Request $request, $id)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $prescripcion = Prescripcion::with('medicamentoCatalogo')
            ->where('activo', true)
            ->findOrFail($id);

        if ($prescripcion->vencida()) {
            return back()->with('error', 'El plazo para reclamar esta prescripción venció y ya no se puede entregar.');
        }

        if ($prescripcion->estado !== 'pendiente') {
            return back()->with('error', 'Esta prescripción ya fue entregada.');
        }

        $datos = $request->validate([
            'cantidad_entregada'  => 'required|integer|min:1|max:1000',
            'observacion_entrega' => 'nullable|string|max:255',
        ]);

        $medicamento = $prescripcion->medicamentoCatalogo;

        // Control de stock: si el medicamento está en el catálogo, la
        // entrega no puede superar las unidades registradas.
        if ($medicamento && $datos['cantidad_entregada'] > $medicamento->stock) {
            return back()->withErrors([
                'cantidad_entregada' => "Stock insuficiente: solo hay {$medicamento->stock} unidades de {$medicamento->nombre}. Registra primero el inventario.",
            ])->withInput();
        }

        DB::transaction(function () use ($prescripcion, $medicamento, $datos) {
            $prescripcion->update([
                'estado'              => 'entregada',
                'fecha_entrega'       => now(),
                'cedula_farmaceuta'   => $_SESSION['usuario_cedula'],
                'cantidad_entregada'  => $datos['cantidad_entregada'],
                'observacion_entrega' => $datos['observacion_entrega'] ?? null,
            ]);

            if ($medicamento) {
                $medicamento->decrement('stock', $datos['cantidad_entregada']);
            }
        });

        $nombrePaciente = $prescripcion->paciente
            ? trim($prescripcion->paciente->nombres . ' ' . $prescripcion->paciente->apellidos)
            : 'el paciente';

        return redirect()
            ->route('farmacia.prescripciones.index', ['estado' => 'pendiente'])
            ->with('exito', "Entrega registrada: {$prescripcion->medicamento} para {$nombrePaciente}.");
    }

    /**
     * Inventario: listado de medicamentos con su stock actual y
     * buscador por código, nombre o presentación.
     */
    public function inventario(Request $request)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $buscar = trim((string) $request->query('buscar', ''));

        $consulta = Medicamento::query()->where('activo', true);

        if ($buscar !== '') {
            $consulta->where(function ($q) use ($buscar) {
                $q->where('codigo', 'like', "%{$buscar}%")
                  ->orWhere('nombre', 'like', "%{$buscar}%")
                  ->orWhere('presentacion', 'like', "%{$buscar}%");
            });
        }

        $medicamentos = $consulta->orderBy('nombre')->paginate(15)->withQueryString();

        $seccionActiva = 'inventario';

        return view('farmacia.inventario.index', compact('medicamentos', 'buscar', 'seccionActiva'));
    }

    /**
     * Actualiza el stock de un medicamento (el farmacéutico registra
     * cuántas unidades hay: el administrador los crea con stock 0).
     */
    public function actualizarStock(Request $request, $codigo)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $datos = $request->validate([
            'stock' => 'required|integer|min:0|max:1000000',
        ]);

        $medicamento = Medicamento::where('activo', true)->findOrFail($codigo);

        $medicamento->stock = $datos['stock'];
        $medicamento->save();

        return back()->with('exito', "Stock actualizado: {$medicamento->nombre} → {$medicamento->stock} unidades.");
    }
}
