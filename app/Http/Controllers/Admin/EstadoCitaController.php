<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\EstadoCita;
use Illuminate\Http\Request;

class EstadoCitaController extends Controller
{
    // Lista todos los estados de cita
    public function index()
    {
        $estados = EstadoCita::withCount('citas')->orderBy('nombre')->get();
        $seccionActiva = 'citas';
        return view('admin.citas.estado_cita.index', compact('estados', 'seccionActiva'));
    }

    // Formulario para crear un estado de cita
    public function create()
    {
        $seccionActiva = 'citas';
        return view('admin.citas.estado_cita.create', compact('seccionActiva'));
    }

    // Guarda el nuevo estado de cita
    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre'       => 'required|string|max:50|unique:estado_cita,nombre',
            'descripcion'  => 'nullable|string|max:255',
        ]);

        EstadoCita::create($datos);

        return redirect()->route('estado_cita.index')->with('exito', 'Estado de cita creado correctamente.');
    }

    // Formulario para editar un estado de cita
    public function edit($id)
    {
        $estado = EstadoCita::findOrFail($id);
        $seccionActiva = 'citas';
        return view('admin.citas.estado_cita.edit', compact('estado', 'seccionActiva'));
    }

    // Actualiza el estado de cita
    public function update(Request $request, $id)
    {
        $estado = EstadoCita::findOrFail($id);

        $datos = $request->validate([
            'nombre'       => 'required|string|max:50|unique:estado_cita,nombre,' . $id . ',id',
            'descripcion'  => 'nullable|string|max:255',
        ]);

        $estado->update($datos);

        return redirect()->route('estado_cita.index')->with('exito', 'Estado de cita actualizado correctamente.');
    }

    // Activa o desactiva el estado de cita (en vez de eliminarlo)
    public function cambiarEstado($id)
    {
        $estado = EstadoCita::findOrFail($id);
        $estado->activo = !$estado->activo;
        $estado->save();

        $mensaje = $estado->activo
            ? 'Estado de cita activado correctamente.'
            : 'Estado de cita desactivado correctamente.';

        return redirect()->route('estado_cita.index')->with('exito', $mensaje);
    }
}