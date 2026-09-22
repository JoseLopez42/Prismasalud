<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\TipoUsuario;
use App\Models\Admin\Ciudad;
use App\Models\Admin\TipoDocumento;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    const TIPO_ADMINISTRADOR = 1;

    // Lista todos los usuarios
    public function index()
    {
        $usuarios = Usuario::with('tipoUsuario')->get();
        $seccionActiva = 'usuarios';
        return view('admin.usuarios.index', compact('usuarios', 'seccionActiva'));
    }

    // Formulario para crear un nuevo usuario
    public function create()
    {
        $tiposUsuario = TipoUsuario::all();
        $ciudades = Ciudad::all();
        $tiposDocumento = TipoDocumento::all();
        $seccionActiva = 'usuarios';
        return view('admin.usuarios.create', compact('tiposUsuario', 'ciudades', 'tiposDocumento', 'seccionActiva'));
    }

    // Guarda el nuevo usuario
    public function store(Request $request)
    {
        $datos = $request->validate([
            'cedula'            => 'required|integer|unique:usuarios,cedula',
            'nombres'           => 'required|string|max:100',
            'apellidos'         => 'required|string|max:100',
            'edad'              => 'nullable|integer|min:0|max:120',
            'telefono'          => 'nullable|string|max:20',
            'correo'            => 'required|email|unique:usuarios,correo',
            'contrasena'        => 'required|string|min:6',
            'tipo_usuario_id'   => 'required|exists:tipos_usuario,id',
            'id_ciudad'         => 'nullable|exists:ciudades,id_ciudad',
            'id_tipo_documento' => 'nullable|exists:tipo_documento,id_tipo_documento',
            'foto'              => 'nullable|image|max:2048', // máx 2MB
        ]);

        // Si subieron una foto, la guardamos en storage/app/public/usuarios
        // (necesita "php artisan storage:link" para que sea accesible desde el navegador)
        if ($request->hasFile('foto')) {
            $datos['foto'] = $request->file('foto')->store('usuarios', 'public');
        }

        $datos['contrasena'] = bcrypt($datos['contrasena']);

        Usuario::create($datos);

        return redirect()->route('usuarios.index')->with('exito', 'Usuario creado correctamente.');
    }

    // Formulario para editar un usuario existente
    public function edit($cedula)
    {
        $usuario = Usuario::findOrFail($cedula);
        $tiposUsuario = TipoUsuario::all();
        $ciudades = Ciudad::all();
        $tiposDocumento = TipoDocumento::all();
        $seccionActiva = 'usuarios';
        return view('admin.usuarios.edit', compact('usuario', 'tiposUsuario', 'ciudades', 'tiposDocumento', 'seccionActiva'));
    }

    // Actualiza el usuario. La contraseña y la foto son opcionales:
    // si llegan vacías, se dejan las que ya tenía.
    public function update(Request $request, $cedula)
    {
        $usuario = Usuario::findOrFail($cedula);

        $datos = $request->validate([
            'nombres'           => 'required|string|max:100',
            'apellidos'         => 'required|string|max:100',
            'edad'              => 'nullable|integer|min:0|max:120',
            'telefono'          => 'nullable|string|max:20',
            'correo'            => 'required|email|unique:usuarios,correo,' . $cedula . ',cedula',
            'contrasena'        => 'nullable|string|min:6',
            'tipo_usuario_id'   => 'required|exists:tipos_usuario,id',
            'id_ciudad'         => 'nullable|exists:ciudades,id_ciudad',
            'id_tipo_documento' => 'nullable|exists:tipo_documento,id_tipo_documento',
            'foto'              => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $datos['foto'] = $request->file('foto')->store('usuarios', 'public');
        }

        if (!empty($datos['contrasena'])) {
            $datos['contrasena'] = bcrypt($datos['contrasena']);
        } else {
            unset($datos['contrasena']);
        }

        $usuario->update($datos);

        return redirect()->route('usuarios.index')->with('exito', 'Usuario actualizado correctamente.');
    }

    // Activa o desactiva el usuario (en vez de eliminarlo)
    public function cambiarEstado($cedula)
    {
        $usuario = Usuario::findOrFail($cedula);
        $usuario->activo = !$usuario->activo;
        $usuario->save();

        $mensaje = $usuario->activo ? 'Usuario activado correctamente.' : 'Usuario desactivado correctamente.';

        return redirect()->route('usuarios.index')->with('exito', $mensaje);
    }
}