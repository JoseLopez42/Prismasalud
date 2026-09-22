<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\TipoDocumento;
use Illuminate\Http\Request;

class TipoDocumentoController extends Controller
{
    // Lista todos los tipos de documento
    public function index()
    {
        $tiposDocumento = TipoDocumento::all();
        $seccionActiva = 'usuarios';
        return view('admin.usuarios.tipos_documento.index', compact('tiposDocumento', 'seccionActiva'));
    }

    // Formulario para crear uno nuevo
    public function create()
    {
        $seccionActiva = 'usuarios';
        return view('admin.usuarios.tipos_documento.create', compact('seccionActiva'));
    }

    // Guarda el nuevo tipo de documento
    public function store(Request $request)
    {
        $datos = $request->validate([
            'tipo_documento' => 'required|string|max:100',
        ]);

        TipoDocumento::create($datos);

        return redirect()->route('tipos_documento.index')->with('exito', 'Tipo de documento creado correctamente.');
    }

    // Formulario para editar uno existente
    public function edit($id)
    {
        $tipoDocumento = TipoDocumento::findOrFail($id);
        $seccionActiva = 'usuarios';
        return view('admin.usuarios.tipos_documento.edit', compact('tipoDocumento', 'seccionActiva'));
    }

    // Actualiza el tipo de documento
    public function update(Request $request, $id)
    {
        $tipoDocumento = TipoDocumento::findOrFail($id);

        $datos = $request->validate([
            'tipo_documento' => 'required|string|max:100',
        ]);

        $tipoDocumento->update($datos);

        return redirect()->route('tipos_documento.index')->with('exito', 'Tipo de documento actualizado correctamente.');
    }

    // Elimina el tipo de documento
    public function destroy($id)
    {
        TipoDocumento::findOrFail($id)->delete();
        return redirect()->route('tipos_documento.index')->with('exito', 'Tipo de documento eliminado correctamente.');
    }
}