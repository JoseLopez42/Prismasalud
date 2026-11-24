<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\CatalogoProcedimiento;
use App\Models\Admin\CitaMedica;
use App\Models\Admin\Medico;
use App\Models\Admin\Procedimiento;
use App\Models\Admin\TipoProcedimiento;
use App\Models\Usuario;
use Illuminate\Http\Request;

class ProcedimientoController extends Controller
{
    /**
     * Catálogos compartidos por los formularios de procedimientos.
     */
    private function catalogos(): array
    {
        return [
            'pacientes' => Usuario::whereHas('tipoUsuario', function ($consulta) {
                    $consulta->where('nombre', 'Paciente');
                })
                ->orderBy('nombres')
                ->get(['cedula', 'nombres', 'apellidos']),

            'medicos' => Medico::where('estado', 'activo')
                ->orderBy('nombres')
                ->get(['cedula', 'nombres', 'apellidos', 'id_especialidad']),

            'tipos' => TipoProcedimiento::where('activo', true)
                ->orderBy('nombre')
                ->get(['id', 'nombre']),

            'citas' => CitaMedica::with(['paciente', 'medico'])
                ->orderBy('fecha', 'desc')
                ->get(),

            // Procedimientos que se cargan en el formulario desde la BD
            'catalogo' => CatalogoProcedimiento::with('tipoProcedimiento')
                ->where('activo', true)
                ->orderBy('nombre')
                ->get(),
        ];
    }

    /**
     * Reglas de validación para crear y editar un procedimiento.
     * Al crear, la descripción debe venir del catálogo de la BD.
     * Al editar se permite conservar descripciones antiguas.
     */
    private function reglas(bool $conCatalogo = true): array
    {
        return [
            'cedula_paciente'       => 'required|exists:usuarios,cedula',
            'cedula_medico'         => 'required|exists:medico,cedula',
            'id_cita'               => 'nullable|exists:cita,id',
            'id_tipo_procedimiento' => 'required|exists:tipo_procedimiento,id',
            'fecha'                 => 'required|date',
            'descripcion'           => $conCatalogo
                ? 'required|string|max:255|exists:catalogo_procedimiento,nombre'
                : 'required|string|max:255',
            'observaciones'         => 'nullable|string',
            'activo'                => 'boolean',
        ];
    }

    /**
     * Listado de procedimientos realizados a los pacientes.
     */
    public function index()
    {
        $procedimientos = Procedimiento::with(['paciente', 'medico.especialidad', 'tipoProcedimiento', 'detalles'])
            ->orderBy('fecha', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $seccionActiva = 'procedimientos';

        return view('admin.procedimientos.procedimiento.index', compact('procedimientos', 'seccionActiva'));
    }

    /**
     * Formulario para registrar un procedimiento.
     */
    public function create()
    {
        $seccionActiva = 'procedimientos';

        return view('admin.procedimientos.procedimiento.create',
            array_merge($this->catalogos(), compact('seccionActiva')));
    }

    /**
     * Guarda un procedimiento nuevo.
     */
    public function store(Request $request)
    {
        $datos = $request->validate($this->reglas());
        $datos['activo'] = $request->boolean('activo');

        Procedimiento::create($datos);

        return redirect()->route('procedimiento.index')
            ->with('exito', 'Procedimiento creado correctamente.');
    }

    /**
     * Formulario para editar un procedimiento.
     */
    public function edit($id)
    {
        $procedimiento = Procedimiento::findOrFail($id);
        $seccionActiva = 'procedimientos';

        return view('admin.procedimientos.procedimiento.edit',
            array_merge($this->catalogos(), compact('procedimiento', 'seccionActiva')));
    }

    /**
     * Actualiza un procedimiento existente.
     */
    public function update(Request $request, $id)
    {
        $procedimiento = Procedimiento::findOrFail($id);

        $datos = $request->validate($this->reglas(false));
        $datos['activo'] = $request->boolean('activo');

        $procedimiento->update($datos);

        return redirect()->route('procedimiento.index')
            ->with('exito', 'Procedimiento actualizado correctamente.');
    }

    /**
     * Activa o desactiva el procedimiento en vez de eliminarlo.
     */
    public function cambiarEstado($id)
    {
        $procedimiento = Procedimiento::findOrFail($id);
        $procedimiento->activo = ! $procedimiento->activo;
        $procedimiento->save();

        $mensaje = $procedimiento->activo
            ? 'Procedimiento activado correctamente.'
            : 'Procedimiento desactivado correctamente.';

        return redirect()->route('procedimiento.index')->with('exito', $mensaje);
    }
}
