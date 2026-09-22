<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TipoUsuario;
use Illuminate\Http\Request;

class TipoUsuarioController extends Controller
{
    // Lista todos los roles
    public function index()
    {
        $roles = TipoUsuario::withCount('usuarios')->get();
        $seccionActiva = 'usuarios';
        return view('admin.usuarios.roles.index', compact('roles', 'seccionActiva'));
    }

    // Formulario para crear un nuevo rol
    public function create()
    {
        $seccionActiva = 'usuarios';
        return view('admin.usuarios.roles.create', compact('seccionActiva'));
    }

    // Guarda el nuevo rol
    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:50|unique:tipos_usuario,nombre',
        ]);

        TipoUsuario::create($datos);

        return redirect()->route('roles.index')->with('exito', 'Rol creado correctamente.');
    }

    // Formulario para editar un rol existente
    public function edit($id)
    {
        $rol = TipoUsuario::findOrFail($id);
        $seccionActiva = 'usuarios';
        return view('admin.usuarios.roles.edit', compact('rol', 'seccionActiva'));
    }

    // Actualiza el rol
    public function update(Request $request, $id)
    {
        $rol = TipoUsuario::findOrFail($id);

        $datos = $request->validate([
            'nombre' => 'required|string|max:50|unique:tipos_usuario,nombre,' . $id,
        ]);

        $rol->update($datos);

        return redirect()->route('roles.index')->with('exito', 'Rol actualizado correctamente.');
    }

    // Elimina el rol, siempre que no tenga usuarios asociados
    // (si se dejara borrar, esos usuarios quedarían sin rol válido).
    public function destroy($id)
    {
        $rol = TipoUsuario::withCount('usuarios')->findOrFail($id);

        if ($rol->usuarios_count > 0) {
            return redirect()->route('roles.index')
                ->with('error', 'No se puede eliminar: hay usuarios con este rol.');
        }

        $rol->delete();

        return redirect()->route('roles.index')->with('exito', 'Rol eliminado correctamente.');
    }
}