<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Empresa;
use App\Models\Licencia;
use App\Models\Plan;
use App\Models\Transaccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CompraController extends Controller
{
    const TIPO_ADMINISTRADOR = 1;

    public function mostrarFormulario(string $plan)
    {
        $planSeleccionado = Plan::porSlug($plan);
        abort_unless($planSeleccionado, 404);

        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $usuarioLogueado = isset($_SESSION['usuario_cedula']) ? Usuario::find($_SESSION['usuario_cedula']) : null;

        $licenciaExistente = $usuarioLogueado?->licencia;
        $esRenovacion = (bool) $licenciaExistente;

        // Departamentos y ciudades desde la base de datos para los selectores dinámicos.
        $departamentos = \App\Models\Admin\Departamento::orderBy('nombre_departamento')->get();
        $ciudades = \App\Models\Admin\Ciudad::with('departamento')->orderBy('ciudad')->get();

        $ciudadesPorDepartamento = [];
        foreach ($ciudades as $ciudad) {
            $nombreDepto = $ciudad->departamento->nombre_departamento ?? null;
            if ($nombreDepto) {
                $ciudadesPorDepartamento[$nombreDepto][] = $ciudad->ciudad;
            }
        }

        return view('comprar', [
            'plan' => $planSeleccionado,
            'usuarioLogueado' => $usuarioLogueado,
            'licenciaExistente' => $licenciaExistente,
            'esRenovacion' => $esRenovacion,
            'departamentos' => $departamentos,
            'ciudadesPorDepartamento' => $ciudadesPorDepartamento,
        ]);
    }

    public function procesar(Request $request, string $plan)
    {
        $planSeleccionado = Plan::porSlug($plan);
        abort_unless($planSeleccionado, 404);

        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $usuarioLogueado = isset($_SESSION['usuario_cedula']) ? Usuario::find($_SESSION['usuario_cedula']) : null;

        // Caso 1: ya tiene cuenta (sesion activa) -> solo IPS + pago
        if ($usuarioLogueado) {
            $licenciaExistente = $usuarioLogueado->licencia;

            // Renovación: los datos de la IPS ya están registrados, solo se
            // valida y guarda el método de pago.
            if ($licenciaExistente) {
                $datosPago = $request->validate([
                    'metodo_pago' => 'required|in:tarjeta_credito,tarjeta_debito,pse,nequi,daviplata,transferencia,efecty',
                    'nombre_tarjeta' => [Rule::when(in_array($request->input('metodo_pago'), ['tarjeta_credito', 'tarjeta_debito']), ['required', 'string', 'max:100'], ['nullable', 'string', 'max:100'])],
                    'numero_tarjeta' => [Rule::when(in_array($request->input('metodo_pago'), ['tarjeta_credito', 'tarjeta_debito']), ['required', 'string', 'min:13', 'max:19'], ['nullable', 'string', 'min:13', 'max:19'])],
                    'vencimiento_tarjeta' => [Rule::when(in_array($request->input('metodo_pago'), ['tarjeta_credito', 'tarjeta_debito']), ['required', 'string', 'max:5'], ['nullable', 'string', 'max:5'])],
                    'cvv' => [Rule::when(in_array($request->input('metodo_pago'), ['tarjeta_credito', 'tarjeta_debito']), ['required', 'string', 'min:3', 'max:4'], ['nullable', 'string', 'min:3', 'max:4'])],
                    'referencia' => [Rule::when(!in_array($request->input('metodo_pago'), ['tarjeta_credito', 'tarjeta_debito']), ['required', 'string', 'max:100'], ['nullable', 'string', 'max:100'])],
                ], [
                    'metodo_pago.required' => 'Selecciona un método de pago.',
                ]);

                $datos = array_merge([
                    'nombre_ips' => $licenciaExistente->nombre_ips,
                    'nit' => $licenciaExistente->nit,
                    'tipo_entidad' => $licenciaExistente->tipo_entidad,
                    'representante_legal' => $licenciaExistente->representante_legal,
                    'correo_ips' => $licenciaExistente->correo_ips,
                    'telefono_ips' => $licenciaExistente->telefono_ips,
                    'departamento' => $licenciaExistente->departamento,
                    'ciudad' => $licenciaExistente->ciudad,
                    'direccion' => $licenciaExistente->direccion,
                ], $datosPago);

                return $this->crearLicenciaParaUsuario($usuarioLogueado, $planSeleccionado, $datos, 'renovacion');
            }

            $datos = $request->validate([
                'nombre_ips' => 'required|string|max:150',
                'nit' => ['required', 'string', 'max:30', 'regex:/^\d{5,15}(-\d)?$/'],
                'tipo_entidad' => 'required|in:IPS,Clinica,EPS',
                'representante_legal' => 'required|string|max:150',
                'correo_ips' => 'required|email|max:150',
                'telefono_ips' => ['required', 'string', 'max:20', 'regex:/^[0-9 +()-]{7,20}$/'],
                'departamento' => 'required|string|max:80',
                'ciudad' => 'required|string|max:80',
                'direccion' => 'required|string|max:200',
                'metodo_pago' => 'required|in:tarjeta_credito,tarjeta_debito,pse,nequi,daviplata,transferencia,efecty',
                'nombre_tarjeta' => [Rule::when(in_array($request->input('metodo_pago'), ['tarjeta_credito', 'tarjeta_debito']), ['required', 'string', 'max:100'], ['nullable', 'string', 'max:100'])],
                'numero_tarjeta' => [Rule::when(in_array($request->input('metodo_pago'), ['tarjeta_credito', 'tarjeta_debito']), ['required', 'string', 'min:13', 'max:19'], ['nullable', 'string', 'min:13', 'max:19'])],
                'vencimiento_tarjeta' => [Rule::when(in_array($request->input('metodo_pago'), ['tarjeta_credito', 'tarjeta_debito']), ['required', 'string', 'max:5'], ['nullable', 'string', 'max:5'])],
                'cvv' => [Rule::when(in_array($request->input('metodo_pago'), ['tarjeta_credito', 'tarjeta_debito']), ['required', 'string', 'min:3', 'max:4'], ['nullable', 'string', 'min:3', 'max:4'])],
                'referencia' => [Rule::when(!in_array($request->input('metodo_pago'), ['tarjeta_credito', 'tarjeta_debito']), ['required', 'string', 'max:100'], ['nullable', 'string', 'max:100'])],
            ], [
                'nit.regex' => 'El NIT debe tener solo números (puede terminar en -dígito de verificación).',
                'telefono_ips.regex' => 'El teléfono de la IPS solo puede tener números, espacios, + ( ) y guion.',
            ]);

            return $this->crearLicenciaParaUsuario($usuarioLogueado, $planSeleccionado, $datos);
        }

        // Sin sesion: se crea la cuenta de administrador durante la compra (si no existe).
        // Si ya existe, se valida la contraseña y se usa la cuenta existente.
        $datos = $request->validate([
            'nombre_ips' => 'required|string|max:150',
            'nit' => ['required', 'string', 'max:30', 'regex:/^\d{5,15}(-\d)?$/'],
            'tipo_entidad' => 'required|in:IPS,Clinica,EPS',
            'representante_legal' => 'required|string|max:150',
            'correo_ips' => 'required|email|max:150',
            'telefono_ips' => ['required', 'string', 'max:20', 'regex:/^[0-9 +()-]{7,20}$/'],
            'departamento' => 'required|string|max:80',
            'ciudad' => 'required|string|max:80',
            'direccion' => 'required|string|max:200',
            'cedula' => ['required', 'integer', 'digits_between:6,10'],
            'telefono' => 'nullable|string|max:20',
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'correo' => ['required', 'email', 'max:150'],
            'contrasena' => ['required', 'string', 'min:6', 'max:100', 'confirmed', 'regex:/^(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{6,}$/'],
            'contrasena_confirmation' => ['required', 'string', 'max:100'],
            'metodo_pago' => 'required|in:tarjeta_credito,tarjeta_debito,pse,nequi,daviplata,transferencia,efecty',
            'nombre_tarjeta' => [Rule::when(in_array($request->input('metodo_pago'), ['tarjeta_credito', 'tarjeta_debito']), ['required', 'string', 'max:100'], ['nullable', 'string', 'max:100'])],
            'numero_tarjeta' => [Rule::when(in_array($request->input('metodo_pago'), ['tarjeta_credito', 'tarjeta_debito']), ['required', 'string', 'min:13', 'max:19'], ['nullable', 'string', 'min:13', 'max:19'])],
            'vencimiento_tarjeta' => [Rule::when(in_array($request->input('metodo_pago'), ['tarjeta_credito', 'tarjeta_debito']), ['required', 'string', 'max:5'], ['nullable', 'string', 'max:5'])],
            'cvv' => [Rule::when(in_array($request->input('metodo_pago'), ['tarjeta_credito', 'tarjeta_debito']), ['required', 'string', 'min:3', 'max:4'], ['nullable', 'string', 'min:3', 'max:4'])],
            'referencia' => [Rule::when(!in_array($request->input('metodo_pago'), ['tarjeta_credito', 'tarjeta_debito']), ['required', 'string', 'max:100'], ['nullable', 'string', 'max:100'])],
        ], [
            'nit.regex' => 'El NIT debe tener solo números (puede terminar en -dígito de verificación).',
            'telefono_ips.regex' => 'El teléfono de la IPS solo puede tener números, espacios, + ( ) y guion.',
            'cedula.digits_between' => 'La cédula debe tener entre 6 y 10 dígitos.',
            'correo.email' => 'El correo de la cuenta no es válido.',
            'correo.required' => 'Escribe el correo de tu cuenta.',
            'contrasena.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'contrasena.regex' => 'La contraseña debe incluir al menos una letra mayúscula y un carácter especial (por ejemplo @, #, !).',
            'contrasena.confirmed' => 'Las dos contraseñas de la cuenta no coinciden.',
            'contrasena_confirmation.required' => 'Confirma la contraseña.',
            'nombres.required' => 'Escribe tus nombres.',
            'apellidos.required' => 'Escribe tus apellidos.',
        ]);


        $usuario = Usuario::find($datos['cedula']);
        if (! $usuario && ! empty($datos['correo'])) {
            $usuario = Usuario::where('correo', $datos['correo'])->first();
        }

        // Si ya existe, valida la contraseña. Si no, crea la cuenta de administrador.
        if ($usuario) {
            if (! Hash::check($datos['contrasena'], $usuario->contrasena)) {
                return back()
                    ->withErrors(['contrasena' => 'La contraseña no coincide con esa cuenta.'])
                    ->withInput($request->except(['contrasena', 'contrasena_confirmation', 'numero_tarjeta', 'cvv']));
            }
        } else {
            if (Usuario::where('cedula', $datos['cedula'])->exists()) {
                return back()
                    ->withErrors(['cedula' => 'Esa cédula ya está registrada.'])
                    ->withInput($request->except(['contrasena', 'contrasena_confirmation', 'numero_tarjeta', 'cvv']));
            }
            if (Usuario::where('correo', $datos['correo'])->exists()) {
                return back()
                    ->withErrors(['correo' => 'Ese correo ya está registrado.'])
                    ->withInput($request->except(['contrasena', 'contrasena_confirmation', 'numero_tarjeta', 'cvv']));
            }

            $usuario = Usuario::create([
                'cedula' => (int) $datos['cedula'],
                'nombres' => $datos['nombres'],
                'apellidos' => $datos['apellidos'],
                'telefono' => $datos['telefono'] ?? null,
                'correo' => $datos['correo'],
                'contrasena' => Hash::make($datos['contrasena']),
                'tipo_usuario_id' => self::TIPO_ADMINISTRADOR,
            ]);
        }

        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $_SESSION['usuario_cedula'] = $usuario->cedula;
        $_SESSION['usuario_nombres'] = $usuario->nombres;
        $_SESSION['tipo_usuario_id'] = $usuario->tipo_usuario_id;

        // LOG DE INGRESOS: dispara el trigger trg_log_ingreso_usuarios
        DB::table('usuarios')->where('cedula', $usuario->cedula)
            ->update(['ultimo_ingreso' => now()]);

        return $this->crearLicenciaParaUsuario($usuario, $planSeleccionado, $datos);
    }

    /**
     * Crea la licencia y la transaccion para un usuario que ya existe
     * (ya sea porque tenia sesion activa, o porque acaba de validar su
     * contrasena dentro del mismo formulario de compra) y lo deja
     * logueado con el plan activo.
     */
    private function crearLicenciaParaUsuario(Usuario $usuario, array $planSeleccionado, array $datos, string $tipo = 'compra')
    {
        $duracionDias = $this->duracionEnDias($planSeleccionado['id']);

        $licencia = null;
        DB::transaction(function () use ($usuario, $planSeleccionado, $datos, $duracionDias, $tipo, &$licencia) {
            $licencia = Licencia::create([
                'usuario_cedula' => $usuario->cedula,
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
                'fecha_fin' => now()->addDays($duracionDias)->toDateString(), 'estado' => 'inactiva',
                'notas' => 'Compra realizada desde el panel de administración.',
            ]);

            // Al renovar, la licencia nueva nace inactiva pero ya deja
            // suspendidas las anteriores: el admin no puede usar el panel
            // hasta que el superadmin active esta licencia.
            Licencia::suspenderAnteriores($licencia);
            
            Empresa::create([
                'usuario_cedula' => $usuario->cedula,
                'licencia_id' => $licencia->id,
                'nit' => $datos['nit'],
                'nombre' => $datos['nombre_ips'],
                'tipo_entidad' => $datos['tipo_entidad'],
                'representante_legal' => $datos['representante_legal'],
                'correo' => $datos['correo_ips'],
                'telefono' => $datos['telefono_ips'],
                'departamento' => $datos['departamento'],
                'ciudad' => $datos['ciudad'],
                'direccion' => $datos['direccion'],
                'plan' => $planSeleccionado['id'],
                'fecha_compra' => now()->toDateString(),
                'fecha_expiracion' => $licencia->fecha_fin?->toDateString() ?? now()->addDays($duracionDias)->toDateString(),
                'estado' => 'inactiva',
            ]);

            Transaccion::create([
                'licencia_id' => $licencia->id,
                'usuario_cedula' => $usuario->cedula,
                'plan_id' => $planSeleccionado['plan_id'],
                'monto' => $planSeleccionado['precio_total'] ?? 0,
                'tipo' => $tipo,
                'metodo_pago' => $datos['metodo_pago'] ?? 'tarjeta_credito',
                'referencia_pago' => $this->referenciaPago($datos),
                'estado' => 'aprobado',
            ]);
        });

        $mensaje = $tipo === 'renovacion'
            ? '¡Renovación registrada! Tu plan quedó inactivo y será activado por el superadministrador.'
            : '¡Compra registrada! Tu plan quedó inactivo y será activado por el superadministrador.';

        return redirect()->route('admin.pago_aceptado')->with('exito', $mensaje);
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
     * Genera una referencia enmascarada según el método de pago elegido.
     */
    private function referenciaPago(array $datos): string
    {
        $metodo = $datos['metodo_pago'] ?? 'tarjeta_credito';

        if (in_array($metodo, ['tarjeta_credito', 'tarjeta_debito'], true)) {
            return $this->referenciaTarjeta($datos['numero_tarjeta'] ?? '');
        }

        return trim((string) ($datos['referencia'] ?? ''));
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