<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Licencia;
use App\Models\Plan;
use App\Models\Transaccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CompraController extends Controller
{
    const TIPO_ADMINISTRADOR = 1;

    public function mostrarFormulario(string $plan)
    {
        $planSeleccionado = Plan::porSlug($plan);
        abort_unless($planSeleccionado, 404);

        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $usuarioLogueado = isset($_SESSION['usuario_cedula']) ? Usuario::find($_SESSION['usuario_cedula']) : null;

        return view('comprar', ['plan' => $planSeleccionado, 'usuarioLogueado' => $usuarioLogueado]);
    }

    public function procesar(Request $request, string $plan)
    {
        $planSeleccionado = Plan::porSlug($plan);
        abort_unless($planSeleccionado, 404);

        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $usuarioLogueado = isset($_SESSION['usuario_cedula']) ? Usuario::find($_SESSION['usuario_cedula']) : null;

        // Caso 1: ya tiene cuenta (sesion activa) -> solo IPS + pago
        if ($usuarioLogueado) {
            $datos = $request->validate([
                'nombre_ips' => 'required|string|max:150',
                'nit' => ['required', 'string', 'max:30', 'regex:/^\d{5,15}(-\d)?$/'],
                'tipo_entidad' => 'required|in:IPS,Clinica,Consultorio,EPS,Otro',
                'representante_legal' => 'required|string|max:150',
                'correo_ips' => 'required|email|max:150',
                'telefono_ips' => ['required', 'string', 'max:20', 'regex:/^[0-9 +()-]{7,20}$/'],
                'departamento' => 'required|string|max:80',
                'ciudad' => 'required|string|max:80',
                'direccion' => 'required|string|max:200',
                'nombre_tarjeta' => 'required|string|max:100',
                'numero_tarjeta' => 'required|string|min:13|max:19',
                'vencimiento_tarjeta' => 'required|string|max:5',
                'cvv' => 'required|string|min:3|max:4',
            ], [
                'nit.regex' => 'El NIT debe tener solo números (puede terminar en -dígito de verificación).',
                'telefono_ips.regex' => 'El teléfono de la IPS solo puede tener números, espacios, + ( ) y guion.',
            ]);

            return $this->crearLicenciaParaUsuario($usuarioLogueado, $planSeleccionado, $datos);
        }

        // Sin sesion: solo pueden comprar cuentas que ya existen.
        // No se crea ninguna cuenta nueva desde este formulario.
        $datos = $request->validate([
            'nombre_ips' => 'required|string|max:150',
            'nit' => ['required', 'string', 'max:30', 'regex:/^\d{5,15}(-\d)?$/'],
            'tipo_entidad' => 'required|in:IPS,Clinica,Consultorio,EPS,Otro',
            'representante_legal' => 'required|string|max:150',
            'correo_ips' => 'required|email|max:150',
            'telefono_ips' => ['required', 'string', 'max:20', 'regex:/^[0-9 +()-]{7,20}$/'],
            'departamento' => 'required|string|max:80',
            'ciudad' => 'required|string|max:80',
            'direccion' => 'required|string|max:200',
            'cedula' => ['required', 'integer', 'digits_between:6,10'],
            'correo' => 'nullable|email',
            'contrasena' => 'required|string',
            'nombre_tarjeta' => 'required|string|max:100',
            'numero_tarjeta' => 'required|string|min:13|max:19',
            'vencimiento_tarjeta' => 'required|string|max:5',
            'cvv' => 'required|string|min:3|max:4',
        ], [
            'nit.regex' => 'El NIT debe tener solo números (puede terminar en -dígito de verificación).',
            'telefono_ips.regex' => 'El teléfono de la IPS solo puede tener números, espacios, + ( ) y guion.',
            'cedula.digits_between' => 'La cédula debe tener entre 6 y 10 dígitos.',
        ]);

        $usuarioExistente = Usuario::find($datos['cedula']);
        if (! $usuarioExistente && ! empty($datos['correo'])) {
            $usuarioExistente = Usuario::where('correo', $datos['correo'])->first();
        }

        if (! $usuarioExistente) {
            return back()
                ->withErrors(['cedula' => 'Esa cuenta no existe. Solo las cuentas ya registradas pueden comprar un plan.'])
                ->withInput($request->except(['contrasena', 'numero_tarjeta', 'cvv']));
        }

        if (! Hash::check($datos['contrasena'], $usuarioExistente->contrasena)) {
            return back()
                ->withErrors(['contrasena' => 'La contraseña no coincide con esa cuenta.'])
                ->withInput($request->except(['contrasena', 'numero_tarjeta', 'cvv']));
        }

        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $_SESSION['usuario_cedula'] = $usuarioExistente->cedula;
        $_SESSION['usuario_nombres'] = $usuarioExistente->nombres;
        $_SESSION['tipo_usuario_id'] = $usuarioExistente->tipo_usuario_id;

        return $this->crearLicenciaParaUsuario($usuarioExistente, $planSeleccionado, $datos);
    }

    /**
     * Crea la licencia y la transaccion para un usuario que ya existe
     * (ya sea porque tenia sesion activa, o porque acaba de validar su
     * contrasena dentro del mismo formulario de compra) y lo deja
     * logueado con el plan activo.
     */
    private function crearLicenciaParaUsuario(Usuario $usuario, array $planSeleccionado, array $datos)
    {
        $duracionDias = $this->duracionEnDias($planSeleccionado['id']);

        $licencia = null;
        DB::transaction(function () use ($usuario, $planSeleccionado, $datos, $duracionDias, &$licencia) {
            $licencia = Licencia::create([
                'nombre_ips' => $datos['nombre_ips'], 'nit' => $datos['nit'],
                'tipo_entidad' => $datos['tipo_entidad'],
                'representante_legal' => $datos['representante_legal'],
                'correo_ips' => $datos['correo_ips'],
                'telefono_ips' => $datos['telefono_ips'],
                'departamento' => $datos['departamento'],
                'ciudad' => $datos['ciudad'],
                'direccion' => $datos['direccion'],
                'contacto_nombre' => trim($usuario->nombres . ' ' . $usuario->apellidos),
                'contacto_email' => $usuario->correo, 'contacto_telefono' => $usuario->telefono,
                'plan' => $planSeleccionado['id'], 'plan_id' => $planSeleccionado['plan_id'],
                'fecha_inicio' => now()->toDateString(),
                'fecha_fin' => now()->addDays($duracionDias)->toDateString(), 'estado' => 'activa',
                'notas' => 'Compra realizada desde el panel de administración.',
            ]);
            $usuario->update(['licencia_id' => $licencia->id]);

            Transaccion::create([
                'licencia_id' => $licencia->id,
                'usuario_cedula' => $usuario->cedula,
                'plan_id' => $planSeleccionado['plan_id'],
                'monto' => $planSeleccionado['precio_total'] ?? 0,
                'tipo' => 'compra',
                'metodo_pago' => 'tarjeta_simulada',
                'referencia_pago' => $this->referenciaTarjeta($datos['numero_tarjeta']),
                'estado' => 'aprobado',
            ]);
        });

        return redirect()->route('admin.plan')->with('exito', '¡Compra exitosa! Ya tienes tu plan activo.');
    }

    private function duracionEnDias(string $planId): int
    {
        return match ($planId) {
            'gratis' => 30,
            'un-anio', 'un-anio-farmacia' => 365,
            'dos-anios', 'dos-anios-farmacia' => 730,
            default => 30,
        };
    }

    /**
     * Nunca guardamos el numero de tarjeta completo: solo dejamos una
     * referencia enmascarada con los ultimos 4 digitos, como haria
     * cualquier pasarela de pago real.
     */
    private function referenciaTarjeta(string $numero): string
    {
        $limpio = preg_replace('/\D/', '', $numero);
        $ultimos4 = substr($limpio, -4) ?: '0000';

        return '**** **** **** ' . $ultimos4;
    }
}