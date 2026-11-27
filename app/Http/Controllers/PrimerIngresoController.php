<?php

namespace App\Http\Controllers;

use App\Models\Admin\Beneficiario;
use App\Models\Usuario;
use App\Mail\CodigoPrimerIngresoMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * Primer ingreso de un cotizante: fluido en 4 pasos dentro de una
 * misma vista (wizard):
 *
 *   Paso 1: cédula  -> se valida que exista en usuarios, que
 *                     debe_crear_contrasena = 1 y que sea cotizante.
 *   Paso 2: correo  -> se guarda el correo, se genera un código de
 *                     6 dígitos, se guarda en cache (hasheado) y se
 *                     envía por correo.
 *   Paso 3: código  -> se verifica el código contra el cache.
 *   Paso 4: contraseña -> se crea la contraseña (8+ caracteres, con
 *                     letras y números), se marca debe_crear_contrasena = 0,
 *                     se inicia sesión y se redirige al dashboard.
 *
 * Todo se guarda en sesión de Laravel y en cache (clave:
 * primer_ingreso:{cedula}). No hace falta migración nueva.
 */
class PrimerIngresoController extends Controller
{
    const CACHE_TTL_MINUTOS = 10; // 10 minutos de expiración del código
    const MAX_INTENTOS      = 5;  // máximo 5 intentos por código

    /**
     * Muestra la vista del wizard. El paso actual se lee de la sesión
     * (por defecto es el paso 1: cédula).
     */
    public function mostrarFormulario(Request $request)
        {
        $cedula = $request->query('cedula');

        // Si llega la cédula por query (desde el login) y aún no hay
        // sesión de primer ingreso, intentamos validarla directamente
        // para saltar al paso 2 (correo).
        if ($cedula && !session('primer_ingreso_cedula')) {
            $usuario = $this->buscarUsuarioValido($cedula);
            if ($usuario) {
                session(['primer_ingreso_cedula' => $cedula]);
                session(['primer_ingreso_paso' => 2]);
                return redirect()->route('primer_ingreso.form');
            }
        }

        $paso = session('primer_ingreso_paso', 1);

        return view('primer-ingreso', [
            'paso'      => $paso,
            'cedula'    => session('primer_ingreso_cedula', ''),
            'correo'    => session('primer_ingreso_correo', ''),
            'cedulaValida' => $cedula && session('primer_ingreso_cedula'),
        ]);
    }

    /**
     * Paso 1: valida la cédula. Debe existir en usuarios, tener
     * debe_crear_contrasena = 1 y ser tipo_persona = 'cotizante'.
     */
    public function validarCedula(Request $request)
    {
        $request->validate([
            'cedula' => 'required|integer|exists:usuarios,cedula',
        ]);

        $cedula = (int) $request->cedula;
        $usuario = $this->buscarUsuarioValido($cedula);

        if (!$usuario) {
            return back()->withErrors([
                'cedula' => 'No se puede iniciar el primer ingreso con esta cédula.',
            ])->withInput();
        }

        session(['primer_ingreso_cedula' => $cedula]);
        session(['primer_ingreso_paso' => 2]);
        session(['primer_ingreso_correo' => '']);

        return redirect()->route('primer_ingreso.form');
    }

    /**
     * Paso 2: guarda el correo, genera el código de 6 dígitos, lo
     * almacena en cache (hasheado) y lo envía por correo.
     */
    public function enviarCodigo(Request $request)
    {
        $cedula = session('primer_ingreso_cedula');
        if (!$cedula) {
            return redirect()->route('primer_ingreso.form');
        }

        $request->validate([
            'correo' => 'required|email|max:100',
        ]);

        $correo = $request->correo;

        // Código de 6 dígitos
        $codigo = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Almacena en cache: código hasheado, correo, intentos y expiración
        $datos = [
            'codigo_hash' => Hash::make($codigo),
            'correo'      => $correo,
            'intentos'    => 0,
            'expira_en'   => now()->addMinutes(self::CACHE_TTL_MINUTOS)->timestamp,
        ];
        Cache::put('primer_ingreso:' . $cedula, $datos, now()->addMinutes(self::CACHE_TTL_MINUTOS));

        // Guarda el correo en sesión para no pedirlo de nuevo
        session(['primer_ingreso_correo' => $correo]);
        session(['primer_ingreso_paso' => 3]);

        // Envía el correo (en local con MAIL_MAILER=log se escribe a storage/logs/laravel.log)
        $usuario = Usuario::find($cedula);
        try {
            Mail::to($correo)->send(new CodigoPrimerIngresoMail($codigo, $usuario->nombres ?? 'Paciente'));
        } catch (\Throwable $e) {
            Log::error('No se pudo enviar el código de primer ingreso: ' . $e->getMessage());
        }

        return redirect()->route('primer_ingreso.form');
    }

    /**
     * Paso 3: verifica el código de 6 dígitos contra el cache.
     * Máximo 5 intentos. Si falla, se queda en este paso.
     */
    public function verificarCodigo(Request $request)
    {
        $cedula = session('primer_ingreso_cedula');
        if (!$cedula) {
            return redirect()->route('primer_ingreso.form');
        }

        $request->validate([
            'codigo' => 'required|string|size:6',
        ]);

        $codigoIngresado = $request->codigo;
        $datos = Cache::get('primer_ingreso:' . $cedula);

        if (!$datos || !isset($datos['codigo_hash'])) {
            return back()->withErrors([
                'codigo' => 'El código expiró o ya no es válido. Solicita uno nuevo.',
            ])->withInput();
        }

        // Verifica si ya expiró
        if (now()->timestamp > $datos['expira_en']) {
            Cache::forget('primer_ingreso:' . $cedula);
            return back()->withErrors([
                'codigo' => 'El código expiró. Solicita uno nuevo.',
            ])->withInput();
        }

        // Máximo 5 intentos
        if ($datos['intentos'] >= self::MAX_INTENTOS) {
            Cache::forget('primer_ingreso:' . $cedula);
            return back()->withErrors([
                'codigo' => 'Demasiados intentos fallidos. El código fue invalidado. Solicita uno nuevo.',
            ])->withInput();
        }

        // Verifica el código (hasheado)
        if (!Hash::check($codigoIngresado, $datos['codigo_hash'])) {
            $datos['intentos']++;
            Cache::put('primer_ingreso:' . $cedula, $datos, now()->addMinutes(self::CACHE_TTL_MINUTOS));

            return back()->withErrors([
                'codigo' => 'El código es incorrecto. Intento ' . $datos['intentos'] . ' de ' . self::MAX_INTENTOS . '.',
            ])->withInput();
        }

        // Código válido: pasa al paso 4 (contraseña)
        session(['primer_ingreso_paso' => 4]);

        return redirect()->route('primer_ingreso.form');
    }

    /**
     * Paso 4: guarda la nueva contraseña, marca debe_crear_contrasena = 0,
     * borra el código del cache, inicia sesión y redirige al dashboard.
     */
    public function guardarContrasena(Request $request)
    {
        $cedula = session('primer_ingreso_cedula');
        if (!$cedula) {
            return redirect()->route('primer_ingreso.form');
        }

        $request->validate([
            'contrasena' => [
                'required',
                'string',
                'min:8',
                'regex:/[A-Za-z]/', // al menos una letra
                'regex:/\d/',       // al menos un número
                'confirmed',
            ],
            'contrasena_confirmation' => 'required|string',
        ], [
            'contrasena.required'      => 'Escribe la contraseña que quieres usar.',
            'contrasena.min'           => 'La contrasena debe tener minimo 8 caracteres.',
            'contrasena.regex'         => 'La contrasena debe tener letras y numeros.',
            'contrasena.confirmed'     => 'Las dos contrasenas no coinciden.',
            'contrasena_confirmation.required' => 'Debes confirmar tu contrasena.',
        ]);

        $usuario = Usuario::find($cedula);
        if (!$usuario || !$usuario->debe_crear_contrasena) {
            return redirect()->route('primer_ingreso.form');
        }

        // Actualiza el usuario
        $usuario->contrasena = bcrypt($request->contrasena);
        $usuario->debe_crear_contrasena = false;
        $usuario->save();

        // Borra el código del cache
        Cache::forget('primer_ingreso:' . $cedula);

        // Inicia sesión automática: REC-02 exige cambiar el id de la
        // sesión nativa antes de escribir las claves del usuario.
        $this->regenerarSesionNativa();
        $_SESSION['usuario_cedula']  = $usuario->cedula;
        $_SESSION['usuario_nombres'] = $usuario->nombres;
        $_SESSION['tipo_usuario_id'] = $usuario->tipo_usuario_id;

        // LOG DE INGRESOS: dispara el trigger trg_log_ingreso_usuarios
        DB::table('usuarios')->where('cedula', $usuario->cedula)
            ->update(['ultimo_ingreso' => now()]);

        // Limpia la sesión del wizard
        session()->forget(['primer_ingreso_cedula', 'primer_ingreso_paso', 'primer_ingreso_correo']);

        return redirect()->route('usuario.inicio')
            ->with('exito', '¡Bienvenido a PrismaSalud! Tu contraseña fue creada y ya puedes acceder a tu panel.');
    }

    /**
     * REC-02: al completar el primer ingreso también hay un login real,
     * así que se descarta la sesión previa y se regenera su id antes
     * de escribir las claves del usuario.
     */
    private function regenerarSesionNativa(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];
        session_regenerate_id(true);
    }

    /**
     * Busca un usuario que cumpla las 3 condiciones para primer ingreso:
     * 1) existe en usuarios
     * 2) debe_crear_contrasena = 1
     * 3) tipo_persona = 'cotizante' (en la tabla beneficiarios)
     *
     * Retorna el Usuario o null. El mensaje de error es siempre el mismo
     * para no revelar qué cédulas existen.
     */
    private function buscarUsuarioValido(int $cedula): ?Usuario
    {
        $usuario = Usuario::find($cedula);
        if (!$usuario || !$usuario->debe_crear_contrasena) {
            return null;
        }

        $persona = Beneficiario::find($cedula);
        if (!$persona || $persona->tipo_persona !== 'cotizante') {
            return null;
        }

        return $usuario;
    }
}