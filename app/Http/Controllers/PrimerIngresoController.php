<?php

namespace App\Http\Controllers;

use App\Models\Admin\Beneficiario;
use App\Models\Usuario;
use Illuminate\Http\Request;

/**
 * Primer ingreso de un cotizante o beneficiario: la persona entra con su
 * cedata y crea SU contrasena (no es temporal).
 */
class PrimerIngresoController extends Controller
{
    public function mostrarFormulario(Request $request)
    {
        return view('primer-ingreso', ['cedula' => $request->query('cedula')]);
    }

    public function guardar(Request $request)
    {
        $datos = $request->validate([
            'cedula'     => ['required', 'integer'],
            'contrasena' => ['required', 'string', 'max:50', 'confirmed'],
        ], [
            'cedula.required'     => 'Escribe tu cedula.',
            'cedula.integer'      => 'La cedula debe ser un numero.',
            'contrasena.required' => 'Escribe la contrasena que quieres usar.',
            'contrasena.confirmed' => 'Las dos contrasenas no coinciden.',
        ]);

        $usuario = Usuario::where('cedula', $datos['cedula'])
            ->where('debe_crear_contrasena', true)
            ->first();
        $persona = $usuario ? Beneficiario::find($datos['cedula']) : null;

        $mensajeGeneral = 'Los datos no coinciden o esta cuenta ya tiene contrasena. Si ya la creaste, inicia sesion normalmente.';

        if (!$usuario || !$persona) {
            return back()->withErrors(['primer' => $mensajeGeneral])->withInput($request->except('contrasena', 'contrasena_confirmation'));
        }

        // Cotizante: contrasena propia y segura. Beneficiario: corta, facil de recordar.
        $clave = $datos['contrasena'];
        if ($persona->esCotizante()) {
            if (strlen($clave) < 8 || !preg_match('/[A-Za-z]/', $clave) || !preg_match('/\d/', $clave)) {
                return back()->withErrors(['contrasena' => 'La contrasena debe tener minimo 8 caracteres, con letras y numeros.'])
                    ->withInput($request->except('contrasena', 'contrasena_confirmation'));
            }
        } elseif (strlen($clave) < 4 || strlen($clave) > 12) {
            return back()->withErrors(['contrasena' => 'La contrasena debe tener entre 4 y 12 caracteres.'])
                ->withInput($request->except('contrasena', 'contrasena_confirmation'));
        }

        $usuario->contrasena = bcrypt($clave);
        $usuario->debe_crear_contrasena = false;
        $usuario->save();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['usuario_cedula']  = $usuario->cedula;
        $_SESSION['usuario_nombres'] = $usuario->nombres;
        $_SESSION['tipo_usuario_id'] = $usuario->tipo_usuario_id;

        return redirect()->route('usuario.inicio');
    }
}