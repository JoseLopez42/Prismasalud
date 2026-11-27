<?php

namespace App\Http\Controllers;

use App\Models\Admin\Medico;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    private const MENSAJE_CREDENCIALES = 'Cédula o contraseña incorrectas.';

    /**
     * Muestra el formulario de inicio de sesión.
     */
    public function mostrarFormulario()
    {
        return view('login');
    }

    /**
     * Procesa el inicio de sesión: valida las credenciales contra
     * la tabla "usuarios" o, si no está allí, contra "medico", y guarda
     * los datos del rol en la sesión nativa de PHP (session_start).
     */
    public function iniciarSesion(Request $request)
    {
        $this->iniciarSesionPhp();

        // Validamos que llegaron los campos del formulario y que se
        // aceptaron los términos y condiciones antes de iniciar sesión.
        $credenciales = $request->validate([
            'cedula'     => 'required|integer',
            'contrasena' => 'nullable|string',
            'terminos'   => 'accepted',
        ], [
            'terminos.accepted' => 'Debes aceptar los términos y condiciones para iniciar sesión.',
        ]);

        $contrasena = $credenciales['contrasena'] ?? '';

        // Buscamos el usuario por cédula (la PK de la tabla).
        $usuario = Usuario::find($credenciales['cedula']);

        // Si no existe como usuario de la plataforma, puede tratarse
        // de un MÉDICO: su cuenta vive en la tabla "medico" (tiene su
        // propia contraseña) y accede a su propio panel.
        $medico = null;
        $contrasenaMedicoValida = false;

        if (!$usuario) {
            $medico = Medico::find($credenciales['cedula']);

            if ($medico) {
                $contrasenaMedicoValida = $contrasena !== ''
                    && password_verify($contrasena, $medico->contrasena);
            }
        }

        // REC-02: "medico.estado" se sigue exigiendo, y el error es el mismo
        // que para unas credenciales malas, tanto si el médico está inactivo
        // como si la contraseña era incorrecta.
        if (!$usuario && $medico && $medico->estado === 'activo' && $contrasenaMedicoValida) {
            $this->regenerarSesionNativa();

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

        // REC-02: se calcula la validez de la contraseña también para las
        // cuentas inactivas, pero "usuarios.activo" se revisa antes de crear
        // la sesión. El mensaje es idéntico en todos los casos para no
        // revelar si la contraseña era correcta.
        $contrasenaUsuarioValida = $usuario
            && $contrasena !== ''
            && password_verify($contrasena, $usuario->contrasena);

        if (!$usuario
            || !$usuario->activo
            || (!$usuario->debe_crear_contrasena && !$contrasenaUsuarioValida)) {
            return back()->withErrors(['login' => self::MENSAJE_CREDENCIALES]);
        }

        // Cuenta nueva (cotizante o beneficiario recién registrado) que
        // todavía no ha creado su contraseña: la llevamos a crearla.
        if ($usuario->debe_crear_contrasena) {
            return redirect()->route('primer_ingreso.form', ['cedula' => $usuario->cedula]);
        }

        $this->regenerarSesionNativa();

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
        $this->iniciarSesionPhp();

        session_unset();   // Borra todas las variables de $_SESSION
        session_destroy(); // Destruye la sesión por completo

        return redirect('/login');
    }

    /**
     * Asegura que la sesión nativa esté abierta (la usa también el
     * middleware de auditoría).
     */
    private function iniciarSesionPhp(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * REC-02: justo después de validar las credenciales se descarta
     * cualquier sesión anterior y se cambia el id. Después se escriben
     * las claves del rol que acaba de entrar.
     */
    private function regenerarSesionNativa(): void
    {
        $this->iniciarSesionPhp();

        $_SESSION = [];
        session_regenerate_id(true);
    }
}
