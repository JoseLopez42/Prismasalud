<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Estado;
use Illuminate\Http\Request;

class EstadoController extends Controller
{
    public function index()
    {
        $estados = Estado::all();
        $seccionActiva = 'afiliaciones';
        return view('admin.afiliaciones.estado.index', compact('estados', 'seccionActiva'));
    }

    public function create()
    {
        $seccionActiva = 'afiliaciones';
        return view('admin.afiliaciones.estado.create', compact('seccionActiva'));
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'estado' => 'required|string|max:50',
        ]);

        Estado::create($datos);

        return redirect()->route('estados.index')->with('exito', 'Estado creado correctamente.');
    }

    public function edit($id)
    {
        $estado = Estado::findOrFail($id);
        $seccionActiva = 'afiliaciones';
        return view('admin.afiliaciones.estado.edit', compact('estado', 'seccionActiva'));
    }

    public function update(Request $request, $id)
    {
        $estado = Estado::findOrFail($id);

        $datos = $request->validate([
            'estado' => 'required|string|max:50',
        ]);

        $estado->update($datos);

        return redirect()->route('estados.index')->with('exito', 'Estado actualizado correctamente.');
    }
}
