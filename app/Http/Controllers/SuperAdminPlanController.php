<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Planes que se venden en la página (Gratis, 1 año, 2 años...).
 * El Superadmin corrige textos y precios, los activa o desactiva.
 * No se crean ni se borran desde aquí: cada plan está amarrado a su enlace de compra (/comprar/{slug}).
 */
class SuperAdminPlanController extends Controller
{
    public function index()
    {
        return view('superadmin.planes.index', [
            'planes' => Plan::ordenados()->get(),
            'empresas' => DB::table('licencias')->select('plan_id', DB::raw('COUNT(*) as total'))->groupBy('plan_id')->pluck('total', 'plan_id'),
        ]);
    }

    public function edit($id)
    {
        return view('superadmin.planes.form', ['plan' => Plan::findOrFail($id)]);
    }

    public function update(Request $request, $id)
    {
        $plan = Plan::findOrFail($id);

        // El precio puede llegar con puntos o espacios: se dejan solo los números
        $request->merge(['precio_total' => preg_replace('/[^0-9]/', '', (string) $request->input('precio_total'))]);

        $lineas = function (string $nombre, string $etiqueta) {
            return function ($atributo, $valor, $fallo) use ($etiqueta) {
                $items = $this->aLineas($valor);
                if (count($items) > 20) { $fallo("En «{$etiqueta}» pon máximo 20 líneas."); }
                foreach ($items as $linea) {
                    if (mb_strlen($linea) > 150) { $fallo("En «{$etiqueta}» cada línea puede tener máximo 150 caracteres."); break; }
                }
            };
        };

        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:60'],
            'etiqueta' => ['nullable', 'string', 'max:60'],
            'descripcion' => ['nullable', 'string', 'max:500'],
            'precio_total' => ['required', 'integer', 'min:0', 'max:999999999'],
            'periodo' => ['nullable', 'string', 'max:40'],
            'ideal' => ['nullable', 'string', 'max:500'],
            'beneficios_texto' => ['nullable', 'string', $lineas('beneficios_texto', 'Lo que incluye')],
            'no_incluye_texto' => ['nullable', 'string', $lineas('no_incluye_texto', 'Lo que no incluye')],
            'orden' => ['required', 'integer', 'min:0', 'max:99'],
        ], [
            'nombre.required' => 'Escribe el nombre del plan.',
            'nombre.max' => 'El nombre no puede tener más de 60 caracteres.',
            'etiqueta.max' => 'La etiqueta no puede tener más de 60 caracteres.',
            'descripcion.max' => 'La descripción no puede tener más de 500 caracteres.',
            'precio_total.required' => 'Escribe el precio (si es gratis, pon 0).',
            'precio_total.integer' => 'El precio solo lleva números.',
            'precio_total.min' => 'El precio no puede ser negativo.',
            'precio_total.max' => 'El precio es demasiado grande.',
            'periodo.max' => 'El periodo no puede tener más de 40 caracteres.',
            'ideal.max' => 'El texto "Ideal para" no puede tener más de 500 caracteres.',
            'orden.required' => 'Escribe el orden (0 a 99).',
            'orden.integer' => 'El orden es un número entero.',
            'orden.min' => 'El orden va de 0 a 99.',
            'orden.max' => 'El orden va de 0 a 99.',
        ]);

        $activo = $request->boolean('activo');
        if (! $activo && ! $this->quedaOtroActivo($plan->id)) {
            return back()->withInput()->withErrors(['activo' => 'Debe quedar al menos un plan activo para poder vender.']);
        }

        $plan->update([
            'nombre' => trim($datos['nombre']),
            'etiqueta' => $this->limpio($datos['etiqueta'] ?? null),
            'descripcion' => $this->limpio($datos['descripcion'] ?? null),
            'precio_total' => (int) $datos['precio_total'],
            'periodo' => $this->limpio($datos['periodo'] ?? null),
            'ideal' => $this->limpio($datos['ideal'] ?? null),
            'beneficios' => $this->aLineas($datos['beneficios_texto'] ?? ''),
            'no_incluye' => $this->aLineas($datos['no_incluye_texto'] ?? ''),
            'orden' => (int) $datos['orden'],
            'activo' => $activo,
            'destacada' => $request->boolean('destacada'),
        ]);

        return redirect()->route('superadmin.planes.index')
            ->with('exito', 'El plan "' . $plan->nombre . '" se guardó. Los cambios aplican a las compras nuevas.');
    }

    public function cambiarActivo($id)
    {
        $plan = Plan::findOrFail($id);

        if ($plan->activo && ! $this->quedaOtroActivo($plan->id)) {
            return back()->with('error', 'Debe quedar al menos un plan activo para poder vender.');
        }

        $plan->update(['activo' => ! $plan->activo]);

        return back()->with('exito', 'El plan "' . $plan->nombre . '" quedó ' . ($plan->activo ? 'activo.' : 'desactivado. Ya no se muestra para comprar.'));
    }

    private function quedaOtroActivo(int $idActual): bool
    {
        return Plan::where('activo', true)->where('id', '!=', $idActual)->exists();
    }

    private function limpio($valor): ?string
    {
        $valor = trim((string) $valor);
        return $valor === '' ? null : $valor;
    }

    /** Un texto con una línea por ítem -> arreglo sin líneas vacías. */
    private function aLineas($texto): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R/', (string) $texto)), fn ($l) => $l !== ''));
    }
}