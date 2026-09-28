<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Mail\CodigoRecuperacionMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

/**
 * Recuperación de contraseña en tres pasos independientes:
 *
 *   1. /recuperar         -> el usuario escribe su correo y recibe un código.
 *   2. /verificar-codigo  -> el usuario escribe el código. Si es válido, se
 *                            guarda su cédula en la sesión.
 *   3. /nueva-contrasena  -> solo se muestra si el paso 2 fue exitoso; aquí
 *                            se escribe y guarda la nueva contraseña.
 *
 * La sesión es la que ata los pasos entre sí, así que el usuario nunca
 * tiene que volver a escribir su correo ni el código en el paso 3.
 */
class RecuperarPasswordController extends Controller
{
    /**
     * Paso 1: formulario donde el usuario escribe su correo.
     */
    public function mostrarFormularioSolicitud()
    {
        // Si ya hay un flujo en curso, se reinicia para no arrastrar
        // un código validado de una recuperación anterior.
        session()->forget(['recuperar_correo', 'recuperar_cedula']);

        return view('recuperar.solicitar');
    }

    /**
     * Paso 1 (envío): genera un código de 6 dígitos, lo guarda en el
     * usuario junto con su fecha de expiración (15 minutos) y se lo
     * envía por correo usando el Gmail configurado en el .env.
     */
    public function enviarCodigo(Request $request)
    {
        $request->validate([
            'correo' => 'required|email',
        ]);

        $usuario = Usuario::where('correo', $request->correo)->first();

        // Por seguridad, no le decimos al usuario si el correo existe
        // o no en la base de datos (evita que alguien "adivine" correos
        // registrados). Siempre mostramos el mismo mensaje.
        if ($usuario) {
            // Código de 6 dígitos, ej: "482913"
            $codigo = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            $usuario->codigo = $codigo;
            $usuario->codigo_expira_en = now()->addMinutes(15);
            $usuario->save();

            // Envía el correo con el código (ver app/Mail/CodigoRecuperacionMail.php)
            Mail::to($usuario->correo)->send(new CodigoRecuperacionMail($codigo, $usuario->nombres));
        }

        // El correo queda en sesión para que el paso 2 no lo vuelva a
        // pedir, y se descarta cualquier verificación de una recuperación
        // anterior.
        $request->session()->put('recuperar_correo', $request->correo);
        $request->session()->forget('recuperar_cedula');

        return redirect()->route('recuperar.verificar.form')
                          ->with('exito', 'Si el correo está registrado, te enviamos un código de recuperación.');
    }

    /**
     * Paso 2: formulario donde el usuario escribe únicamente el código
     * que le llegó por correo.
     */
    public function mostrarFormularioVerificar()
    {
        // Sin correo en sesión no hay código pendiente de validar.
        if (!session('recuperar_correo')) {
            return redirect()->route('recuperar.solicitar.form');
        }

        return view('recuperar.verificar');
    }

    /**
     * Paso 2 (validación): comprueba que el código exista, sea del correo
     * indicado y no haya expirado. Si todo está bien, marca al usuario
     * en la sesión y lo envía a la vista de nueva contraseña.
     */
    public function verificarCodigo(Request $request)
    {
        if (!session('recuperar_correo')) {
            return redirect()->route('recuperar.solicitar.form');
        }

        $request->validate([
            'codigo' => 'required|string|size:6',
        ]);

        $usuario = Usuario::where('correo', session('recuperar_correo'))
                          ->where('codigo', $request->codigo)
                          ->where('codigo_expira_en', '>=', now())
                          ->first();

        // Código equivocado, de otro usuario o vencido: se permanece
        // en este paso y no se revela cuál de los tres casos fue.
        if (!$usuario) {
            return back()->withErrors(['codigo' => 'El código es inválido o ya expiró.'])->withInput();
        }

        // Código validado: desde aquí en adelante solo falta la contraseña.
        $request->session()->put('recuperar_cedula', $usuario->cedula);

        return redirect()->route('recuperar.nueva.form');
    }

    /**
     * Paso 3: formulario para escribir la nueva contraseña. Solo es
     * accesible si el código ya fue validado.
     */
    public function mostrarFormularioNuevaContrasena()
    {
        if (!$this->usuarioConCodigoValidado()) {
            return redirect()->route('recuperar.solicitar.form');
        }

        return view('recuperar.nueva-contrasena');
    }

    /**
     * Paso 3 (guardado): actualiza la contraseña (encriptada) y limpia
     * el código para que no se pueda volver a usar.
     */
    public function guardarNuevaContrasena(Request $request)
    {
        $usuario = $this->usuarioConCodigoValidado();

        // El código se validó en un paso anterior (o en otro navegador,
        // o la sesión expiró), así que se vuelve al inicio del flujo.
        if (!$usuario) {
            return redirect()->route('recuperar.solicitar.form');
        }

        $request->validate([
            'contrasena' => 'required|string|min:6|confirmed',
            // "confirmed" exige que también venga un campo
            // "contrasena_confirmation" igual a "contrasena".
        ]);

        $usuario->contrasena = bcrypt($request->contrasena);
        $usuario->codigo = null;
        $usuario->codigo_expira_en = null;
        $usuario->save();

        $request->session()->forget(['recuperar_correo', 'recuperar_cedula']);

        return redirect()->route('login')->with('exito', 'Tu contraseña fue actualizada. Ya puedes iniciar sesión.');
    }

    /**
     * Usuario que ya validó su código en el paso 2, o null si todavía
     * no llegó a esa etapa (o el código ya venció).
     */
    private function usuarioConCodigoValidado()
    {
        $cedula = session('recuperar_cedula');

        if (!$cedula) {
            return null;
        }

        return Usuario::where('cedula', $cedula)
                      ->where('codigo_expira_en', '>=', now())
                      ->first();
    }
}
