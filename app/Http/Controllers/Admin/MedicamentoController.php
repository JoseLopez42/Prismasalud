<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Medicamento;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MedicamentoController extends Controller
{
    // Lista todos los medicamentos del catálogo
    public function index()
    {
        $medicamentos = Medicamento::orderBy('nombre')->get();
        $seccionActiva = 'farmacia';
        return view('admin.farmacia.medicamento.index', compact('medicamentos', 'seccionActiva'));
    }

    // Formulario para crear un medicamento
    public function create()
    {
        $seccionActiva = 'farmacia';
        return view('admin.farmacia.medicamento.create', compact('seccionActiva'));
    }

    // Guarda el nuevo medicamento
    public function store(Request $request)
    {
        $datos = $request->validate([
            'codigo'       => ['required', 'string', 'max:50', Rule::unique('medicamento', 'codigo')],
            'nombre'       => ['required', 'string', 'max:150', Rule::unique('medicamento', 'nombre')],
            'presentacion' => 'nullable|string|max:100',
            'descripcion'  => 'nullable|string',
        ]);

        $datos['codigo'] = trim($datos['codigo']);
        $datos['activo'] = true;

        Medicamento::create($datos);

        return redirect()->route('medicamento.index')->with('exito', 'Medicamento creado correctamente.');
    }

    // Formulario para editar un medicamento (por su código)
    public function edit($codigo)
    {
        $medicamento = Medicamento::findOrFail($codigo);
        $seccionActiva = 'farmacia';
        return view('admin.farmacia.medicamento.edit', compact('medicamento', 'seccionActiva'));
    }

    // Actualiza el medicamento (el código es la llave, no se cambia)
    public function update(Request $request, $codigo)
    {
        $medicamento = Medicamento::findOrFail($codigo);

        $datos = $request->validate([
            'nombre'       => ['required', 'string', 'max:150', Rule::unique('medicamento', 'nombre')->ignore($medicamento->codigo, 'codigo')],
            'presentacion' => 'nullable|string|max:100',
            'descripcion'  => 'nullable|string',
        ]);

        $medicamento->update($datos);

        return redirect()->route('medicamento.index')->with('exito', 'Medicamento actualizado correctamente.');
    }

    // Activa o desactiva el medicamento (en vez de eliminarlo)
    public function cambiarEstado($codigo)
    {
        $medicamento = Medicamento::findOrFail($codigo);
        $medicamento->activo = !$medicamento->activo;
        $medicamento->save();

        $mensaje = $medicamento->activo
            ? 'Medicamento activado correctamente.'
            : 'Medicamento desactivado correctamente.';

        return redirect()->route('medicamento.index')->with('exito', $mensaje);
    }
}
