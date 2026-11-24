<?php

namespace App\Http\Controllers;

use App\Models\Licencia;
use App\Models\Plan;
use App\Models\Usuario;
use App\Mail\LicenciaActivadaMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

/**
 * Empresas (IPS/EPS) que compraron una licencia.
 * El Superadmin las ve, corrige datos mal escritos y las suspende o reactiva.
 */
class SuperAdminEmpresaController extends Controller
{
    public function index(Request $request)
    {
        // Validar automáticamente las fechas: marcar como vencidas las que ya pasaron.
        Licencia::where('estado', 'activa')
            ->whereDate('fecha_fin', '<', now()->toDateString())
            ->update(['estado' => 'vencida']);

        [$busqueda, $estado] = $this->filtros($request);

        $empresas = $this->consulta($busqueda, $estado)->paginate(10)->withQueryString();

        // Cuántos administradores tiene cada empresa
        $admins = $this->contarAdmins($empresas->pluck('id'));

        return view('superadmin.empresas.index', [
            'empresas' => $empresas,
            'admins' => $admins,
            'busqueda' => $busqueda,
            'estado' => $estado,
        ]);
    }

    // Datos para descargar en PDF y Excel: respeta la búsqueda y el filtro que tengas puestos.
    // Devuelve JSON; el navegador arma el archivo (ver los botones en empresas/index.blade.php).
    public function exportar(Request $request)
    {
        [$busqueda, $estado] = $this->filtros($request);

        $licencias = $this->consulta($busqueda, $estado)->get();
        $admins = $this->contarAdmins($licencias->pluck('id'));

        $nombresEstado = ['activa' => 'Activas', 'por_vencer' => 'Por vencer', 'vencida' => 'Vencidas', 'suspendida' => 'Suspendidas'];
        $filtros = [];
        if ($busqueda !== '') { $filtros[] = 'Búsqueda: "' . $busqueda . '"'; }
        if (isset($nombresEstado[$estado])) { $filtros[] = 'Estado: ' . $nombresEstado[$estado]; }

        return response()->json([
            'filtros' => $filtros ? implode(' · ', $filtros) : 'Sin filtros (todas las empresas)',
            'total' => $licencias->count(),
            'columnas' => ['Empresa', 'NIT', 'Contacto', 'Correo', 'Teléfono', 'Plan', 'Inicio', 'Vence', 'Estado', 'Admins'],
            'filas' => $licencias->map(fn ($l) => [
                $l->nombre_ips,
                $l->nit,
                $l->contacto_nombre ?: '—',
                $l->contacto_email ?: '—',
                $l->contacto_telefono ?: '—',
                $l->nombrePlan(),
                $l->fecha_inicio->format('d/m/Y'),
                $l->fecha_fin->format('d/m/Y'),
                $this->etiquetaEstado($l),
                (int) ($admins[$l->id] ?? 0),
            ])->values(),
        ]);
    }

    public function create()
    {
        return view('superadmin.empresas.form', [
            'licencia' => null,
            'planes' => Plan::activos()->ordenados()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $datos = $this->validar($request);
        $plan = Plan::findOrFail($datos['plan_id']);

        $licencia = Licencia::create($datos + ['plan' => $plan->slug]);

        if ($licencia->estado === 'activa') {
            $this->suspenderLicenciasAnteriores($licencia);
        }

        return redirect()->route('superadmin.empresas.index')
            ->with('exito', 'Empresa creada correctamente.');
    }

    public function edit($id)
    {
        return view('superadmin.empresas.form', [
            'licencia' => Licencia::findOrFail($id),
            'planes' => Plan::ordenados()->get(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $licencia = Licencia::findOrFail($id);
        $datos = $this->validar($request);
        $plan = Plan::findOrFail($datos['plan_id']);

        $licencia->update($datos + ['plan' => $plan->slug]);

        if ($licencia->estado === 'activa') {
            $this->suspenderLicenciasAnteriores($licencia);
        }

        return redirect()->route('superadmin.empresas.index')
            ->with('exito', 'Los datos de "' . $licencia->nombre_ips . '" se guardaron.');
    }

    // Suspender / reactivar / activar (no se borra nada)
    public function cambiarEstado($id)
    {
        $licencia = Licencia::findOrFail($id);

        // Activar una licencia comprada que aun esta inactiva (en espera de superadmin)
        if ($licencia->estado === 'inactiva') {
            $this->suspenderLicenciasAnteriores($licencia);
            $licencia->update(['estado' => 'activa']);
            $admin = null;
            if ($licencia->usuario_cedula) {
                Usuario::where('cedula', $licencia->usuario_cedula)->update(['licencia_id' => $licencia->id]);
                $admin = Usuario::find($licencia->usuario_cedula);
            }

            // Enviar correo a la empresa y al administrador notificando la activacion.
            $destinatarios = array_values(array_filter(array_unique([
                $licencia->correo_ips,
                $admin?->correo,
            ])));

            if ($destinatarios) {
                try {
                    Mail::to($destinatarios)->send(new LicenciaActivadaMail($licencia, $admin?->nombres ?? ''));
                    $aviso = ' y se envio un correo a la empresa y al administrador.';
                } catch (\Throwable $e) {
                    $aviso = ' (no se pudo enviar el correo: ' . $e->getMessage() . ')';
                }
            } else {
                $aviso = ' (sin correo para notificar).';
            }

            return back()->with('exito', 'La licencia de "' . $licencia->nombre_ips . '" quedo activa' . $aviso);
        }

        $suspender = $licencia->estado !== 'suspendida';

        if (! $suspender) {
            $this->suspenderLicenciasAnteriores($licencia);

            if ($licencia->usuario_cedula) {
                Usuario::where('cedula', $licencia->usuario_cedula)
                    ->update(['licencia_id' => $licencia->id]);
            }
        }

        $licencia->update(['estado' => $suspender ? 'suspendida' : 'activa']);

        return back()->with('exito', $suspender
            ? '"' . $licencia->nombre_ips . '" quedó suspendida.'
            : '"' . $licencia->nombre_ips . '" quedó activa de nuevo.');
    }

    /**
     * Delega en el modelo para que la regla sea la misma al activar desde el
     * panel del superadmin y al renovar desde el panel del administrador.
     */
    private function suspenderLicenciasAnteriores(Licencia $licencia): void
    {
        Licencia::suspenderAnteriores($licencia);
    }

    // ---------- Ayudas ----------
    private function filtros(Request $request): array
    {
        return [trim((string) $request->query('q', '')), (string) $request->query('estado', '')];
    }

    private function consulta(string $busqueda, string $estado)
    {
        $consulta = Licencia::with('planModelo')->orderByDesc('created_at');

        if ($busqueda !== '') {
            $consulta->where(function ($q) use ($busqueda) {
                $q->where('nombre_ips', 'like', "%{$busqueda}%")
                  ->orWhere('nit', 'like', "%{$busqueda}%")
                  ->orWhere('contacto_nombre', 'like', "%{$busqueda}%")
                  ->orWhere('contacto_email', 'like', "%{$busqueda}%");
            });
        }

        $hoy = now()->toDateString();
        $limite = now()->addDays(15)->toDateString();

        match ($estado) {
            'activa' => $consulta->where('estado', '!=', 'suspendida')->whereDate('fecha_fin', '>=', $hoy),
            'por_vencer' => $consulta->where('estado', '!=', 'suspendida')->whereDate('fecha_fin', '>=', $hoy)->whereDate('fecha_fin', '<=', $limite),
            'vencida' => $consulta->whereDate('fecha_fin', '<', $hoy),
            'suspendida' => $consulta->where('estado', 'suspendida'),
            'pendiente' => $consulta->where('estado', 'inactiva'),
            default => null,
        };

        return $consulta;
    }

    private function contarAdmins($ids)
    {
        return DB::table('usuarios')
            ->whereIn('licencia_id', $ids)
            ->selectRaw('licencia_id, count(*) as total')
            ->groupBy('licencia_id')
            ->pluck('total', 'licencia_id');
    }

    private function etiquetaEstado(Licencia $l): string
    {
        if ($l->estado === 'inactiva') return 'Pendiente';
        if ($l->estado === 'suspendida') return 'Suspendida';
        if ($l->estaVencida()) return 'Vencida';
        if ($l->estaPorVencer()) return 'Por vencer';
        if ($l->estado === 'prueba') return 'En prueba';
        return 'Activa';
    }

    private function validar(Request $request): array
    {
        return $request->validate([
            'nombre_ips' => ['required', 'string', 'min:3', 'max:150'],
            'nit' => ['required', 'regex:/^[0-9]{6,12}(-[0-9])?$/'],
            'contacto_nombre' => ['nullable', 'string', 'max:150'],
            'contacto_email' => ['nullable', 'email:rfc', 'max:150'],
            'contacto_telefono' => ['nullable', 'regex:/^[0-9+\s-]{7,20}$/'],
            'plan_id' => ['required', 'exists:planes,id'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['required', 'date', 'after_or_equal:fecha_inicio'],
            'estado' => ['required', 'in:activa,prueba,suspendida,inactiva,vencida'],
            'notas' => ['nullable', 'string', 'max:1000'],
        ], [
            'nombre_ips.required' => 'Escribe el nombre de la empresa.',
            'nombre_ips.min' => 'El nombre debe tener mínimo 3 caracteres.',
            'nombre_ips.max' => 'El nombre no puede tener más de 150 caracteres.',
            'nit.required' => 'Escribe el NIT.',
            'nit.regex' => 'El NIT solo lleva números (6 a 12) y, si quieres, el dígito de verificación: 900123456-7.',
            'contacto_email.email' => 'Escribe un correo válido, por ejemplo nombre@dominio.com.',
            'contacto_telefono.regex' => 'El teléfono solo lleva números (7 a 20), puede tener + o espacios.',
            'plan_id.required' => 'Elige un plan.',
            'plan_id.exists' => 'Ese plan no existe.',
            'fecha_inicio.required' => 'Elige la fecha de inicio.',
            'fecha_inicio.date' => 'La fecha de inicio no es válida.',
            'fecha_fin.required' => 'Elige la fecha de vencimiento.',
            'fecha_fin.date' => 'La fecha de vencimiento no es válida.',
            'fecha_fin.after_or_equal' => 'El vencimiento no puede ser antes del inicio.',
            'estado.in' => 'El estado no es válido.',
            'notas.max' => 'Las notas no pueden tener más de 1000 caracteres.',
        ]);
    }
}