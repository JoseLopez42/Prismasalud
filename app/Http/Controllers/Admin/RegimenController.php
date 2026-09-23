<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Regimen;
use Illuminate\Http\Request;

class RegimenController extends Controller
{
    // Lista todos los regímenes de afiliación
    public function index()
    {
        $regimenes = Regimen::all();
        $seccionActiva = 'eps';
        return view('admin.eps.regimen.index', compact('regimenes', 'seccionActiva'));
    }

    public function create()
    {
        $seccionActiva = 'eps';
        return view('admin.eps.regimen.create', compact('seccionActiva'));
    }

    // Guarda el nuevo régimen
    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre_regimen' => 'required|string|max:150',
        ]);

        Regimen::create($datos);

        return redirect()->route('regimen.index')->with('exito', 'Régimen creado correctamente.');
    }

    // Formulario para editar uno existente
    public function edit($id)
    {
        $regimen = Regimen::findOrFail($id);
        $seccionActiva = 'eps';
        return view('admin.eps.regimen.edit', compact('regimen', 'seccionActiva'));
    }

    // Actualiza el régimen
    public function update(Request $request, $id)
    {
        $regimen = Regimen::findOrFail($id);

        $datos = $request->validate([
            'nombre_regimen' => 'required|string|max:150',
        ]);

        $regimen->update($datos);

        return redirect()->route('regimen.index')->with('exito', 'Régimen actualizado correctamente.');
    }

    // Activa o desactiva el régimen (en vez de eliminarlo)
    public function cambiarEstado($id)
    {
        $regimen = Regimen::findOrFail($id);
        $regimen->activo = !$regimen->activo;
        $regimen->save();

        $mensaje = $regimen->activo ? 'Régimen activado correctamente.' : 'Régimen desactivado correctamente.';

        return redirect()->route('regimen.index')->with('exito', $mensaje);
    }
}