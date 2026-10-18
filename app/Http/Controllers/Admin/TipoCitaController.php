<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Especialidad;
use App\Models\Admin\TipoCita;
use Illuminate\Http\Request;

class TipoCitaController extends Controller
{
    // Lista todos los tipos de cita
    public function index()
    {
        $tipos = TipoCita::with('especialidad')->withCount('citas')->orderBy('nombre')->get();
        $seccionActiva = 'citas';
        return view('admin.citas.tipo_cita.index', compact('tipos', 'seccionActiva'));
    }

    // Formulario para crear un tipo de cita
    public function create()
    {
        $especialidades = Especialidad::where('activo', true)->orderBy('nombre')->get();
        $seccionActiva = 'citas';
        return view('admin.citas.tipo_cita.create', compact('especialidades', 'seccionActiva'));
    }

    // Guarda el nuevo tipo de cita
    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre'            => 'required|string|max:100|unique:tipo_cita,nombre',
            'descripcion'       => 'nullable|string|max:255',
            'duracion_minutos'  => 'required|integer|min:5|max:120',
            'id_especialidad'   => 'nullable|exists:especialidad,id',
        ]);

        $datos['activo'] = true;

        TipoCita::create($datos);

        return redirect()->route('tipo_cita.index')->with('exito', 'Tipo de cita creado correctamente.');
    }

    // Formulario para editar un tipo de cita
    public function edit($id)
    {
        $tipo = TipoCita::findOrFail($id);
        $especialidades = Especialidad::where('activo', true)->orderBy('nombre')->get();
        $seccionActiva = 'citas';
        return view('admin.citas.tipo_cita.edit', compact('tipo', 'especialidades', 'seccionActiva'));
    }

    // Actualiza el tipo de cita
    public function update(Request $request, $id)
    {
        $tipo = TipoCita::findOrFail($id);

        $datos = $request->validate([
            'nombre'            => 'required|string|max:100|unique:tipo_cita,nombre,' . $id . ',id',
            'descripcion'       => 'nullable|string|max:255',
            'duracion_minutos'  => 'required|integer|min:5|max:120',
            'id_especialidad'   => 'nullable|exists:especialidad,id',
        ]);

        $tipo->update($datos);

        return redirect()->route('tipo_cita.index')->with('exito', 'Tipo de cita actualizado correctamente.');
    }

    // Activa o desactiva el tipo de cita (en vez de eliminarlo)
    public function cambiarEstado($id)
    {
        $tipo = TipoCita::findOrFail($id);
        $tipo->activo = !$tipo->activo;
        $tipo->save();

        $mensaje = $tipo->activo
            ? 'Tipo de cita activado correctamente.'
            : 'Tipo de cita desactivado correctamente.';

        return redirect()->route('tipo_cita.index')->with('exito', $mensaje);
    }
}