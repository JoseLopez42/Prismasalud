<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Consultorio;
use App\Models\Admin\Sede;
use Illuminate\Http\Request;

class ConsultorioController extends Controller
{
    // Lista todos los consultorios
    public function index()
    {
        $consultorios = Consultorio::with('sede')->orderBy('nombre')->get();
        $seccionActiva = 'medicos';
        return view('admin.medicos.consultorio.index', compact('consultorios', 'seccionActiva'));
    }

    // Formulario para crear un consultorio
    public function create()
    {
        $sedes = Sede::orderBy('nombre_sede')->get();
        $seccionActiva = 'medicos';
        return view('admin.medicos.consultorio.create', compact('sedes', 'seccionActiva'));
    }

    // Guarda el nuevo consultorio
    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre'      => 'required|string|max:100|unique:consultorio,nombre',
            'descripcion' => 'nullable|string|max:255',
            'ubicacion'   => 'nullable|string|max:150',
            'id_sede'     => 'nullable|exists:sedes,id_sede',
        ]);

        Consultorio::create($datos);

        return redirect()->route('consultorio.index')->with('exito', 'Consultorio creado correctamente.');
    }

    // Formulario para editar un consultorio
    public function edit($id)
    {
        $consultorio = Consultorio::findOrFail($id);
        $sedes = Sede::orderBy('nombre_sede')->get();
        $seccionActiva = 'medicos';
        return view('admin.medicos.consultorio.edit', compact('consultorio', 'sedes', 'seccionActiva'));
    }

    // Actualiza el consultorio
    public function update(Request $request, $id)
    {
        $consultorio = Consultorio::findOrFail($id);

        $datos = $request->validate([
            'nombre'      => 'required|string|max:100|unique:consultorio,nombre,' . $id . ',id',
            'descripcion' => 'nullable|string|max:255',
            'ubicacion'   => 'nullable|string|max:150',
            'id_sede'     => 'nullable|exists:sedes,id_sede',
        ]);

        $consultorio->update($datos);

        return redirect()->route('consultorio.index')->with('exito', 'Consultorio actualizado correctamente.');
    }

    // Activa o desactiva el consultorio (en vez de eliminarlo)
    public function cambiarEstado($id)
    {
        $consultorio = Consultorio::findOrFail($id);
        $consultorio->activo = !$consultorio->activo;
        $consultorio->save();

        $mensaje = $consultorio->activo
            ? 'Consultorio activado correctamente.'
            : 'Consultorio desactivado correctamente.';

        return redirect()->route('consultorio.index')->with('exito', $mensaje);
    }
}
