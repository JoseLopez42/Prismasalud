<?php

namespace App\Http\Controllers;

use App\Models\Admin\Medico;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        // Validamos que llegaron los campos del formulario y que se
        // aceptaron los términos y condiciones antes de iniciar sesión.
        $credenciales = $request->validate([
            'cedula'     => 'required|integer',
            'contrasena' => 'nullable|string',
            'terminos'   => 'accepted',
        ], [
            'terminos.accepted' => 'Debes aceptar los términos y condiciones para iniciar sesión.',
        ]);

        // Buscamos el usuario por cédula (la PK de la tabla).
        $usuario = Usuario::find($credenciales['cedula']);

        // Cuenta nueva (cotizante o beneficiario recién registrado) que
        // todavía no ha creado su contraseña: la llevamos a crearla.
        if ($usuario && $usuario->debe_crear_contrasena) {
            return redirect()->route('primer_ingreso.form', ['cedula' => $usuario->cedula]);
        }

        // Si no existe como usuario de la plataforma, puede tratarse
        // de un MÉDICO: su cuenta vive en la tabla "medico" (tiene su
        // propia contraseña) y accede a su propio panel.
        if (!$usuario) {
            $medico = Medico::find($credenciales['cedula']);

            if ($medico && $medico->estado === 'activo'
                && password_verify($credenciales['contrasena'], $medico->contrasena)) {
                $_SESSION['medico_cedula']    = $medico->cedula;
                $_SESSION['medico_nombres']   = $medico->nombres;
                $_SESSION['medico_apellidos'] = $medico->apellidos;
                $_SESSION['medico_foto']      = $medico->foto;
                $_SESSION['tipo_usuario_id']  = 3;

                // LOG DE INGRESOS: dispara el trigger trg_log_ingreso_medico
                DB::table('medico')->where('cedula', $medico->cedula)
                    ->update(['ultimo_ingreso' => now()]);

                return redirect()->route('medico.inicio');
            }
        }

        // Si no existe el usuario, o la contraseña no coincide con
        // el hash guardado (bcrypt), la sesión no se abre.
        if (!$usuario || empty($credenciales['contrasena']) || !password_verify($credenciales['contrasena'], $usuario->contrasena)) {
            return back()->withErrors(['login' => 'Cédula o contraseña incorrectas.']);
        }

        // Credenciales correctas: guardamos los datos clave del
        // usuario logueado en $_SESSION, para usarlos en cualquier
        // parte del sistema (por ejemplo en UsuarioController).
        $_SESSION['usuario_cedula']   = $usuario->cedula;
        $_SESSION['usuario_nombres']  = $usuario->nombres;
        $_SESSION['tipo_usuario_id']  = $usuario->tipo_usuario_id;

        // LOG DE INGRESOS: dispara el trigger trg_log_ingreso_usuarios
        DB::table('usuarios')->where('cedula', $usuario->cedula)
            ->update(['ultimo_ingreso' => now()]);

        // Redirigimos según el tipo de usuario: el Administrador va a
        // su panel (/admin), el Farmacéutico al de farmacia y los
        // demás roles al panel del Usuario.
        if ($usuario->tipo_usuario_id == 1) {
            return redirect()->route('admin.dashboard');
        }

        if ($usuario->tipo_usuario_id == 4) {
            return redirect()->route('farmacia.inicio');
        }

        return redirect()->route('usuario.inicio');
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