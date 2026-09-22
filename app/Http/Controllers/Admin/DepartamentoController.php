<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Departamento;
use Illuminate\Http\Request;

class DepartamentoController extends Controller
{
    // Lista todos los departamentos
    public function index()
    {
        $departamentos = Departamento::all();
        $seccionActiva = 'ubicaciones';
        return view('admin.ubicaciones.departamentos.index', compact('departamentos', 'seccionActiva'));
    }

    public function create()
    {
        $seccionActiva = 'ubicaciones';
        return view('admin.ubicaciones.departamentos.create', compact('seccionActiva'));
    }

    // Guarda el nuevo departamento
    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre_departamento' => 'required|string|max:255',
        ]);

        Departamento::create($datos);

        return redirect()->route('departamentos.index')->with('exito', 'Departamento creado correctamente.');
    }

    // Formulario para editar uno existente
    public function edit($id)
    {
        $departamento = Departamento::findOrFail($id);
        $seccionActiva = 'ubicaciones';
        return view('admin.ubicaciones.departamentos.edit', compact('departamento', 'seccionActiva'));
    }

    // Actualiza el departamento
    public function update(Request $request, $id)
    {
        $departamento = Departamento::findOrFail($id);

        $datos = $request->validate([
            'nombre_departamento' => 'required|string|max:255',
        ]);

        $departamento->update($datos);

        return redirect()->route('departamentos.index')->with('exito', 'Departamento actualizado correctamente.');
    }

    // Activa o desactiva el departamento (en vez de eliminarlo)
    public function cambiarEstado($id)
    {
        $departamento = Departamento::findOrFail($id);
        $departamento->activo = !$departamento->activo;
        $departamento->save();

        $mensaje = $departamento->activo ? 'Departamento activado correctamente.' : 'Departamento desactivado correctamente.';

        return redirect()->route('departamentos.index')->with('exito', $mensaje);
    }
}