<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Sede;
use App\Models\Admin\Ciudad;
use Illuminate\Http\Request;

class SedeController extends Controller
{
    // Lista todas las sedes, mostrando el nombre de la ciudad
    public function index()
    {
        $sedes = Sede::with('ciudad')->get();
        $seccionActiva = 'ubicaciones';
        return view('admin.ubicaciones.sedes.index', compact('sedes', 'seccionActiva'));
    }

    // Formulario para crear una nueva sede
    public function create()
    {
        $ciudades = Ciudad::all();
        $seccionActiva = 'ubicaciones';
        return view('admin.ubicaciones.sedes.create', compact('ciudades', 'seccionActiva'));
    }

    // Guarda la nueva sede
    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre_sede' => 'required|string|max:150',
            'direccion'   => 'required|string|max:255',
            'telefono'    => 'required|string|max:20',
            'id_ciudad'   => 'required|exists:ciudades,id_ciudad',
        ]);

        Sede::create($datos);

        return redirect()->route('sedes.index')->with('exito', 'Sede creada correctamente.');
    }

    // Formulario para editar una sede existente
    public function edit($id)
    {
        $sede = Sede::findOrFail($id);
        $ciudades = Ciudad::all();
        $seccionActiva = 'ubicaciones';
        return view('admin.ubicaciones.sedes.edit', compact('sede', 'ciudades', 'seccionActiva'));
    }

    // Actualiza la sede
    public function update(Request $request, $id)
    {
        $sede = Sede::findOrFail($id);

        $datos = $request->validate([
            'nombre_sede' => 'required|string|max:150',
            'direccion'   => 'required|string|max:255',
            'telefono'    => 'required|string|max:20',
            'id_ciudad'   => 'required|exists:ciudades,id_ciudad',
        ]);

        $sede->update($datos);

        return redirect()->route('sedes.index')->with('exito', 'Sede actualizada correctamente.');
    }

    // Elimina la sede
    public function destroy($id)
    {
        Sede::findOrFail($id)->delete();
        return redirect()->route('sedes.index')->with('exito', 'Sede eliminada correctamente.');
    }
}