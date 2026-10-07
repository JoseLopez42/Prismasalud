<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Especialidad;
use Illuminate\Http\Request;

class EspecialidadController extends Controller
{
    // Lista todas las especialidades
    public function index()
    {
        $especialidades = Especialidad::withCount('medicos')->orderBy('nombre')->get();
        $seccionActiva = 'medicos';
        return view('admin.medicos.especialidad.index', compact('especialidades', 'seccionActiva'));
    }

    // Formulario para crear una especialidad
    public function create()
    {
        $seccionActiva = 'medicos';
        return view('admin.medicos.especialidad.create', compact('seccionActiva'));
    }

    // Guarda la nueva especialidad
    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre'       => 'required|string|max:100|unique:especialidad,nombre',
            'descripcion'  => 'nullable|string|max:255',
        ]);

        Especialidad::create($datos);

        return redirect()->route('especialidad.index')->with('exito', 'Especialidad creada correctamente.');
    }

    // Formulario para editar una especialidad
    public function edit($id)
    {
        $especialidad = Especialidad::findOrFail($id);
        $seccionActiva = 'medicos';
        return view('admin.medicos.especialidad.edit', compact('especialidad', 'seccionActiva'));
    }

    // Actualiza la especialidad
    public function update(Request $request, $id)
    {
        $especialidad = Especialidad::findOrFail($id);

        $datos = $request->validate([
            'nombre'       => 'required|string|max:100|unique:especialidad,nombre,' . $id . ',id',
            'descripcion'  => 'nullable|string|max:255',
        ]);

        $especialidad->update($datos);

        return redirect()->route('especialidad.index')->with('exito', 'Especialidad actualizada correctamente.');
    }

    // Activa o desactiva la especialidad (en vez de eliminarla)
    public function cambiarEstado($id)
    {
        $especialidad = Especialidad::findOrFail($id);
        $especialidad->activo = !$especialidad->activo;
        $especialidad->save();

        $mensaje = $especialidad->activo
            ? 'Especialidad activada correctamente.'
            : 'Especialidad desactivada correctamente.';

        return redirect()->route('especialidad.index')->with('exito', $mensaje);
    }
}
