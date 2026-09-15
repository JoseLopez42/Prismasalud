<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\TipoUsuario;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    /**
     * Lista todos los usuarios registrados (vista del administrador).
     * La validación de que quien entra es Administrador ya la hace
     * el middleware EsAdministrador, asignado en las rutas.
     */
    public function index()
    {
        $usuarios = Usuario::with('tipoUsuario')->get();
        return view('admin.usuarios.index', compact('usuarios'));
    }

    /**
     * Muestra el formulario para crear un nuevo usuario.
     */
    public function create()
    {
        $tiposUsuario = TipoUsuario::all();
        return view('admin.usuarios.create', compact('tiposUsuario'));
    }

    /**
     * Guarda el nuevo usuario en la base de datos.
     */
    public function store(Request $request)
    {
        // Validamos los datos que llegan del formulario.
        $datos = $request->validate([
            'cedula'           => 'required|integer|unique:usuarios,cedula',
            'nombres'          => 'required|string|max:100',
            'apellidos'        => 'required|string|max:100',
            'correo'           => 'required|email|unique:usuarios,correo',
            'contrasena'       => 'required|string|min:6',
            'tipo_usuario_id'  => 'required|exists:tipos_usuario,id',
        ]);

        // La contraseña nunca se guarda en texto plano: se encripta.
        $datos['contrasena'] = bcrypt($datos['contrasena']);

        Usuario::create($datos);

        return redirect()->route('usuarios.index')->with('exito', 'Usuario creado correctamente.');
    }
}