<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Ciudad;
use App\Models\Admin\Departamento;
use Illuminate\Http\Request;

class CiudadController extends Controller
{
    // Lista todas las ciudades, mostrando el nombre del departamento
    public function index()
    {
        $ciudades = Ciudad::with('departamento')->get();
        $seccionActiva = 'ubicaciones';
        return view('admin.ubicaciones.ciudades.index', compact('ciudades', 'seccionActiva'));
    }

    // Formulario para crear una nueva ciudad
    public function create()
    {
        $departamentos = Departamento::all();
        $seccionActiva = 'ubicaciones';
        return view('admin.ubicaciones.ciudades.create', compact('departamentos', 'seccionActiva'));
    }

    // Guarda la nueva ciudad
    public function store(Request $request)
    {
        $datos = $request->validate([
            'ciudad'          => 'required|string|max:255',
            'id_departamento' => 'required|exists:departamentos,id_departamento',
        ]);

        Ciudad::create($datos);

        return redirect()->route('ciudades.index')->with('exito', 'Ciudad creada correctamente.');
    }

    // Formulario para editar una ciudad existente
    public function edit($id)
    {
        $ciudad = Ciudad::findOrFail($id);
        $departamentos = Departamento::all();
        $seccionActiva = 'ubicaciones';
        return view('admin.ubicaciones.ciudades.edit', compact('ciudad', 'departamentos', 'seccionActiva'));
    }

    // Actualiza la ciudad
    public function update(Request $request, $id)
    {
        $ciudad = Ciudad::findOrFail($id);

        $datos = $request->validate([
            'ciudad'          => 'required|string|max:255',
            'id_departamento' => 'required|exists:departamentos,id_departamento',
        ]);

        $ciudad->update($datos);

        return redirect()->route('ciudades.index')->with('exito', 'Ciudad actualizada correctamente.');
    }

    // Elimina la ciudad
    // Activa o desactiva la ciudad (en vez de eliminarla)
    public function cambiarEstado($id)
    {
        $ciudad = Ciudad::findOrFail($id);
        $ciudad->activo = !$ciudad->activo; // invierte el valor actual
        $ciudad->save();

        $mensaje = $ciudad->activo ? 'Ciudad activada correctamente.' : 'Ciudad desactivada correctamente.';

        return redirect()->route('ciudades.index')->with('exito', $mensaje);
    }
}