<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Mail\CodigoRecuperacionMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;

/**
 * Recuperación de contraseña en tres pasos independientes:
 *
 *   1. /recuperar         -> el usuario escribe su correo y recibe un código.
 *   2. /verificar-codigo  -> el usuario escribe el código. Si es válido, se
 *                            guarda su cédula en la sesión.
 *   3. /nueva-contrasena  -> solo se muestra si el paso 2 fue exitoso; aquí
 *                            se escribe y guarda la nueva contraseña.
 *
 * REC-02: el código viaja en claro SOLO dentro del correo. En la base se
 * guarda un hash (Hash::make) y se valida con Hash::check, con un máximo
 * de 5 intentos y expiración de 15 minutos.
 */
class RecuperarPasswordController extends Controller
{
    private const MAX_INTENTOS = 5;

    private const MENSAJE_CODIGO_INVALIDO = 'El código es inválido o ya expiró.';

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
     * Paso 1 (envío): genera un código de 6 dígitos, guarda su hash en el
     * usuario junto con la fecha de expiración (15 minutos) y se lo envía
     * por correo usando el Gmail configurado en el .env.
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
            // Código de 6 dígitos, ej: "482913". El texto plano solo se
            // usa para el correo; en la BD queda el hash.
            $codigo = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            $usuario->codigo = Hash::make($codigo);
            $usuario->codigo_expira_en = now()->addMinutes(15);
            $usuario->codigo_intentos = 0;
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
     * Paso 2 (validación): busca al usuario por el correo de la sesión y
     * compara el código con Hash::check. El código NO se busca en SQL
     * porque cada hash es distinto. Máximo 5 intentos; al llegar al
     * límite se invalida y hay que pedir uno nuevo.
     */
    public function verificarCodigo(Request $request)
    {
        if (!session('recuperar_correo')) {
            return redirect()->route('recuperar.solicitar.form');
        }

        $request->validate([
            'codigo' => ['required', 'regex:/^[0-9]{6}$/'],
        ], [
            'codigo.required' => 'Ingresa el código que te llegó al correo.',
            'codigo.regex' => 'El código tiene 6 números.',
        ]);

        $usuario = Usuario::where('correo', session('recuperar_correo'))->first();

        // Código ausente, vencido o ya agotado: se limpia y se devuelve
        // siempre el mismo mensaje, sin contar el caso concreto.
        if (!$usuario
            || !$usuario->codigo
            || !$usuario->codigo_expira_en
            || $usuario->codigo_expira_en->lt(now())
            || $usuario->codigo_intentos >= self::MAX_INTENTOS) {
            if ($usuario) {
                $this->invalidarCodigo($usuario);
            }

            return back()->withErrors(['codigo' => self::MENSAJE_CODIGO_INVALIDO]);
        }

        if (!Hash::check($request->codigo, $usuario->codigo)) {
            // En el quinto fallo se invalida de una vez; no se muestra
            // un mensaje distinto para no revelar el estado de la cuenta.
            if ($usuario->codigo_intentos + 1 >= self::MAX_INTENTOS) {
                $this->invalidarCodigo($usuario);
            } else {
                $usuario->codigo_intentos++;
                $usuario->save();
            }

            return back()->withErrors(['codigo' => self::MENSAJE_CODIGO_INVALIDO]);
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
            'contrasena' => [
                'required',
                'string',
                // Mínimo 8 caracteres, con al menos una mayúscula, una
                // minúscula, un número y un símbolo.
                Password::min(8)->mixedCase()->numbers()->symbols(),
                // "confirmed" exige que también venga un campo
                // "contrasena_confirmation" igual a "contrasena".
                'confirmed',
            ],
            'contrasena_confirmation' => 'required|string',
        ], [
            // El rule Password reporta con las claves "password.*" (no
            // "contrasena.*"), por eso los mensajes van con ese prefijo.
            'password.mixed'      => 'La contraseña debe tener al menos una letra mayúscula y una minúscula.',
            'password.numbers'    => 'La contraseña debe tener al menos un número.',
            'password.symbols'    => 'La contraseña debe tener al menos un signo especial (por ejemplo ! @ # $ % & * ?).',
            'password.min'        => 'La contraseña debe tener al menos 8 caracteres.',
            'contrasena.confirmed' => 'Las contraseñas no coinciden.',
            'contrasena_confirmation.required' => 'Debes confirmar tu nueva contraseña.',
        ]);

        $usuario->contrasena = bcrypt($request->contrasena);
        $this->invalidarCodigo($usuario);

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
                      ->whereNotNull('codigo')
                      ->where('codigo_expira_en', '>=', now())
                      ->first();
    }

    /**
     * REC-02: borra el código hasheado y reinicia su contador para que
     * no pueda reutilizarse.
     */
    private function invalidarCodigo(Usuario $usuario): void
    {
        $usuario->codigo = null;
        $usuario->codigo_expira_en = null;
        $usuario->codigo_intentos = 0;
        $usuario->save();
    }
}
