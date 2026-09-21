<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Mail\CodigoRecuperacionMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class RecuperarPasswordController extends Controller
{
    /**
     * Muestra el formulario donde el usuario escribe su correo
     * para pedir el código de recuperación.
     */
    public function mostrarFormularioSolicitud()
    {
        return view('recuperar.solicitar');
    }

    /**
     * Genera un código de 6 dígitos, lo guarda en el usuario junto
     * con su fecha de expiración (15 minutos), y se lo envía por
     * correo usando el Gmail configurado en el .env.
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

        return redirect()->route('recuperar.restablecer.form')
                          ->with('exito', 'Si el correo está registrado, te enviamos un código de recuperación.');
    }

    /**
     * Muestra el formulario donde el usuario escribe el código que
     * le llegó por correo, junto con su nueva contraseña.
     */
    public function mostrarFormularioRestablecer()
    {
        return view('recuperar.restablecer');
    }

    /**
     * Valida el código (que exista, sea del correo indicado y no
     * haya expirado) y, si todo está bien, actualiza la contraseña.
     */
    public function restablecer(Request $request)
    {
        $request->validate([
            'correo'                 => 'required|email',
            'codigo'                 => 'required|string|size:6',
            'contrasena'             => 'required|string|min:6|confirmed',
            // "confirmed" exige que también venga un campo
            // "contrasena_confirmation" igual a "contrasena".
        ]);

        $usuario = Usuario::where('correo', $request->correo)
                           ->where('codigo', $request->codigo)
                           ->first();

        // Si no se encontró el usuario con ese correo+código, o el
        // código ya expiró, no se permite continuar.
        if (!$usuario || now()->greaterThan($usuario->codigo_expira_en)) {
            return back()->withErrors(['codigo' => 'El código es inválido o ya expiró.']);
        }

        // Todo correcto: se actualiza la contraseña (encriptada) y
        // se limpia el código para que no se pueda volver a usar.
        $usuario->contrasena = bcrypt($request->contrasena);
        $usuario->codigo = null;
        $usuario->codigo_expira_en = null;
        $usuario->save();

        return redirect()->route('login')->with('exito', 'Tu contraseña fue actualizada. Ya puedes iniciar sesión.');
    }
}