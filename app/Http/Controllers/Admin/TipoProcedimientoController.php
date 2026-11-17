<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\TipoProcedimiento;
use Illuminate\Http\Request;

class TipoProcedimientoController extends Controller
{
    /**
     * Catálogo de tipos de procedimiento.
     */
    public function index()
    {
        $tipos = TipoProcedimiento::withCount('procedimientos')
            ->orderBy('nombre')
            ->get();

        $seccionActiva = 'procedimientos';

        return view('admin.procedimientos.tipo_procedimiento.index', compact('tipos', 'seccionActiva'));
    }

    /**
     * Formulario para crear un tipo de procedimiento.
     */
    public function create()
    {
        $seccionActiva = 'procedimientos';

        return view('admin.procedimientos.tipo_procedimiento.create', compact('seccionActiva'));
    }

    /**
     * Guarda un tipo de procedimiento nuevo.
     */
    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre'      => 'required|string|max:150|unique:tipo_procedimiento,nombre',
            'descripcion' => 'nullable|string|max:1000',
        ]);

        $datos['nombre'] = trim($datos['nombre']);
        $datos['activo'] = true;

        TipoProcedimiento::create($datos);

        return redirect()->route('tipo_procedimiento.index')
            ->with('exito', 'Tipo de procedimiento creado correctamente.');
    }

    /**
     * Formulario para editar un tipo de procedimiento.
     */
    public function edit($id)
    {
        $tipo = TipoProcedimiento::findOrFail($id);
        $seccionActiva = 'procedimientos';

        return view('admin.procedimientos.tipo_procedimiento.edit', compact('tipo', 'seccionActiva'));
    }

    /**
     * Actualiza un tipo de procedimiento.
     */
    public function update(Request $request, $id)
    {
        $tipo = TipoProcedimiento::findOrFail($id);

        $datos = $request->validate([
            'nombre'      => 'required|string|max:150|unique:tipo_procedimiento,nombre,' . $id . ',id',
            'descripcion' => 'nullable|string|max:1000',
            'activo'      => 'boolean',
        ]);

        $datos['nombre'] = trim($datos['nombre']);
        $datos['activo'] = $request->boolean('activo');

        $tipo->update($datos);

        return redirect()->route('tipo_procedimiento.index')
            ->with('exito', 'Tipo de procedimiento actualizado correctamente.');
    }

    /**
     * Activa o desactiva el tipo en vez de eliminarlo.
     */
    public function cambiarEstado($id)
    {
        $tipo = TipoProcedimiento::findOrFail($id);
        $tipo->activo = ! $tipo->activo;
        $tipo->save();

        $mensaje = $tipo->activo
            ? 'Tipo de procedimiento activado correctamente.'
            : 'Tipo de procedimiento desactivado correctamente.';

        return redirect()->route('tipo_procedimiento.index')->with('exito', $mensaje);
    }
}
