<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /**
     * Muestra el formulario de inicio de sesión.
     */
    public function mostrarFormulario()
    {
        return view('login');
    }

    /**
     * Procesa el inicio de sesión: valida las credenciales contra
     * la tabla "usuarios" y, si son correctas, guarda los datos
     * del usuario en la sesión nativa de PHP (session_start).
     */
    public function iniciarSesion(Request $request)
    {
        // Arrancamos la sesión nativa si aún no existe.
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Validamos que llegaron los campos del formulario.
        $credenciales = $request->validate([
            'cedula'     => 'required|integer',
            'contrasena' => 'required|string',
        ]);

        // Buscamos el usuario por cédula (la PK de la tabla).
        $usuario = Usuario::find($credenciales['cedula']);

        // Si no existe el usuario, o la contraseña no coincide con
        // el hash guardado (bcrypt), la sesión no se abre.
        if (!$usuario || !password_verify($credenciales['contrasena'], $usuario->contrasena)) {
            return back()->withErrors(['login' => 'Cédula o contraseña incorrectas.']);
        }

        // Credenciales correctas: guardamos los datos clave del
        // usuario logueado en $_SESSION, para usarlos en cualquier
        // parte del sistema (por ejemplo en UsuarioController).
        $_SESSION['usuario_cedula']   = $usuario->cedula;
        $_SESSION['usuario_nombres']  = $usuario->nombres;
        $_SESSION['tipo_usuario_id']  = $usuario->tipo_usuario_id;

        // Redirigimos según el tipo de usuario. Por ahora solo
        // tenemos el panel de Administrador (tipo_usuario_id = 1);
        // los demás roles se agregan cuando construyamos sus vistas.
        if ($usuario->tipo_usuario_id == 1) {
            return redirect()->route('admin.dashboard');
        }

        return redirect('/')->withErrors(['login' => 'Tu rol todavía no tiene un panel asignado.']);
    }

    /**
     * Cierra la sesión: destruye todas las variables de $_SESSION
     * y termina la sesión nativa de PHP.
     */
    public function cerrarSesion()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_unset();   // Borra todas las variables de $_SESSION
        session_destroy(); // Destruye la sesión por completo

        return redirect('/login');
    }
}