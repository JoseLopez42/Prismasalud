<?php

namespace App\Http\Controllers;

use App\Models\Admin\Beneficiario;
use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Primer ingreso de un cotizante o beneficiario: la persona entra con su
 * cédula + fecha de nacimiento y crea SU contraseña (no es temporal).
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
            'cedula'           => ['required', 'integer'],
            'fecha_nacimiento' => ['required', 'date'],
            'contrasena'       => ['required', 'string', 'max:50', 'confirmed'],
        ], [
            'cedula.required'           => 'Escribe tu cédula.',
            'cedula.integer'            => 'La cédula debe ser un número.',
            'fecha_nacimiento.required' => 'Escribe tu fecha de nacimiento.',
            'fecha_nacimiento.date'     => 'La fecha de nacimiento no es válida.',
            'contrasena.required'       => 'Escribe la contraseña que quieres usar.',
            'contrasena.confirmed'      => 'Las dos contraseñas no coinciden.',
        ]);

        $usuario = Usuario::where('cedula', $datos['cedula'])
            ->where('debe_crear_contrasena', true)
            ->first();
        $persona = $usuario ? Beneficiario::find($datos['cedula']) : null;

        // Mismo mensaje para cualquier fallo, para no revelar qué cédulas existen.
        $mensajeGeneral = 'Los datos no coinciden o esta cuenta ya tiene contraseña. Si ya la creaste, inicia sesión normalmente.';

        if (!$usuario || !$persona
            || Carbon::parse($persona->fecha_nacimiento)->toDateString()
               !== Carbon::parse($datos['fecha_nacimiento'])->toDateString()) {
            return back()->withErrors(['primer' => $mensajeGeneral])->withInput($request->except('contrasena', 'contrasena_confirmation'));
        }

        // Cotizante: contraseña propia y segura. Beneficiario: corta, fácil de recordar.
        $clave = $datos['contrasena'];
        if ($persona->esCotizante()) {
            if (strlen($clave) < 8 || !preg_match('/[A-Za-z]/', $clave) || !preg_match('/\d/', $clave)) {
                return back()->withErrors(['contrasena' => 'La contraseña debe tener mínimo 8 caracteres, con letras y números.'])
                    ->withInput($request->except('contrasena', 'contrasena_confirmation'));
            }
        } elseif (strlen($clave) < 4 || strlen($clave) > 12) {
            return back()->withErrors(['contrasena' => 'La contraseña debe tener entre 4 y 12 caracteres.'])
                ->withInput($request->except('contrasena', 'contrasena_confirmation'));
        }

        $usuario->contrasena = bcrypt($clave);
        $usuario->debe_crear_contrasena = false;
        $usuario->save();

        // Inicia sesión de una vez (igual que LoginController)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['usuario_cedula']  = $usuario->cedula;
        $_SESSION['usuario_nombres'] = $usuario->nombres;
        $_SESSION['tipo_usuario_id'] = $usuario->tipo_usuario_id;

        return redirect()->route('usuario.inicio');
    }
}