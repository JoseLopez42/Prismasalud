<?php

namespace App\Http\Controllers\Medico;

use App\Http\Controllers\Controller;
use App\Models\Admin\DetalleProcedimiento;
use App\Models\Admin\Procedimiento;
use App\Models\Admin\TipoProcedimiento;
use App\Models\Usuario;
use Illuminate\Http\Request;

class ProcedimientoController extends Controller
{
    /**
     * Búsqueda de procedimientos de un paciente por cédula, junto con
     * el catálogo de tipos y los detalles de sus procedimientos.
     */
    public function index(Request $request)
    {
        $cedulaBuscada = trim((string) $request->query('cedula', ''));
        $idCita = $request->query('cita');

        $paciente = null;
        $procedimientos = collect();
        $detalles = collect();

        if ($cedulaBuscada !== '') {
            $paciente = Usuario::with(['tipoDocumento', 'tipoUsuario'])->find($cedulaBuscada);

            if ($paciente) {
                $procedimientos = Procedimiento::with(['medico', 'tipoProcedimiento', 'detalles'])
                    ->where('cedula_paciente', $paciente->cedula)
                    ->orderBy('fecha', 'desc')
                    ->get();

                $detalles = DetalleProcedimiento::with('procedimiento')
                    ->whereIn('id_procedimiento', $procedimientos->pluck('id'))
                    ->orderBy('id_procedimiento')
                    ->get();
            }
        }

        // Catálogo de tipos de procedimiento (compartido)
        $tipos = TipoProcedimiento::orderBy('nombre')->get();

        $seccionActiva = 'procedimientos';

        return view('medico.procedimiento.index', compact(
            'cedulaBuscada', 'paciente', 'procedimientos', 'detalles', 'tipos', 'idCita', 'seccionActiva'
        ));
    }

    // Guarda un nuevo procedimiento del paciente
    public function storeProcedimiento(Request $request)
    {
        $datos = $request->validate([
            'cedula_paciente'       => 'required|exists:usuarios,cedula',
            'id_cita'               => 'nullable|exists:cita,id',
            'id_tipo_procedimiento' => 'nullable|exists:tipo_procedimiento,id',
            'fecha'                 => 'required|date',
            'descripcion'           => 'required|string|max:255',
            'observaciones'         => 'nullable|string',
        ]);

        $this->abortarSiCitaAtendida($datos['id_cita'] ?? null);

        $datos['cedula_medico'] = $this->cedulaMedico();
        $datos['activo'] = true;

        Procedimiento::create($datos);

        return redirect()
            ->route('medico.procedimiento.index', ['cedula' => $datos['cedula_paciente']])
            ->with('exito', 'Procedimiento registrado correctamente.');
    }

    // Crea un tipo de procedimiento en el catálogo (compartido)
    public function storeTipoProcedimiento(Request $request)
    {
        $datos = $request->validate([
            'nombre'      => 'required|string|max:150|unique:tipo_procedimiento,nombre',
            'descripcion' => 'nullable|string',
            'cedula'      => 'nullable|integer',
        ]);

        TipoProcedimiento::create([
            'nombre'      => $datos['nombre'],
            'descripcion' => $datos['descripcion'] ?? null,
            'activo'      => true,
        ]);

        $destino = $datos['cedula']
            ? ['cedula' => $datos['cedula']]
            : [];

        return redirect()
            ->route('medico.procedimiento.index', $destino)
            ->with('exito', 'Tipo de procedimiento creado correctamente.');
    }

    // Guarda un detalle de un procedimiento existente del paciente
    public function storeDetalleProcedimiento(Request $request)
    {
        $datos = $request->validate([
            'id_procedimiento' => 'required|exists:procedimiento,id',
            'descripcion'      => 'required|string|max:255',
            'cantidad'         => 'required|integer|min:1',
            'resultado'        => 'nullable|string',
            'cedula'           => 'nullable|integer',
        ]);

        $procedimiento = Procedimiento::findOrFail($datos['id_procedimiento']);

        // El detalle solo lo puede agregar el médico dueño del procedimiento
        abort_unless($procedimiento->cedula_medico === $this->cedulaMedico(), 403);
        $this->abortarSiRegistroEnCitaAtendida($procedimiento);

        DetalleProcedimiento::create([
            'id_procedimiento' => $datos['id_procedimiento'],
            'descripcion'      => $datos['descripcion'],
            'cantidad'         => $datos['cantidad'],
            'resultado'        => $datos['resultado'] ?? null,
            'activo'           => true,
        ]);

        $destino = $datos['cedula']
            ? ['cedula' => $datos['cedula']]
            : [];

        return redirect()
            ->route('medico.procedimiento.index', $destino)
            ->with('exito', 'Detalle de procedimiento registrado correctamente.');
    }

    // Actualiza un procedimiento existente (solo su autor)
    public function updateProcedimiento(Request $request, $id)
    {
        $procedimiento = Procedimiento::findOrFail($id);

        abort_unless((int) $procedimiento->cedula_medico === $this->cedulaMedico(), 403);
        $this->abortarSiRegistroEnCitaAtendida($procedimiento);

        $datos = $request->validate([
            'id_tipo_procedimiento' => 'nullable|exists:tipo_procedimiento,id',
            'fecha'                 => 'required|date',
            'descripcion'           => 'required|string|max:255',
            'observaciones'         => 'nullable|string',
        ]);

        $procedimiento->update($datos);

        return back()->with('exito', 'Procedimiento actualizado correctamente.');
    }

    // Actualiza un detalle de procedimiento existente (solo el autor del procedimiento)
    public function updateDetalleProcedimiento(Request $request, $id)
    {
        $detalle = DetalleProcedimiento::with('procedimiento')->findOrFail($id);

        abort_unless((int) $detalle->procedimiento->cedula_medico === $this->cedulaMedico(), 403);
        $this->abortarSiRegistroEnCitaAtendida($detalle);

        $datos = $request->validate([
            'descripcion' => 'required|string|max:255',
            'cantidad'    => 'required|integer|min:1',
            'resultado'   => 'nullable|string',
        ]);

        $detalle->update($datos);

        return back()->with('exito', 'Detalle de procedimiento actualizado correctamente.');
    }

    // ---------- Ayudas ----------

    private function cedulaMedico(): int
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return (int) $_SESSION['medico_cedula'];
    }
}
