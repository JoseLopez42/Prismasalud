<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Recuperar contraseña del Superadministrador (tabla "users").
 * Tres pasos: 1) correo -> 2) código de 6 números -> 3) contraseña nueva.
 * Es independiente de la recuperación de las IPS.
 */
class SuperAdminRecuperarController extends Controller
{
    private const MINUTOS_VALIDEZ = 10;
    private const MAX_INTENTOS = 5;

    // ---------- PASO 1: pedir el correo ----------
    public function paso1()
    {
        $this->iniciarSesionPhp();
        return view('superadmin.recuperar', ['paso' => 1]);
    }

    public function enviarCodigo(Request $request)
    {
        $this->iniciarSesionPhp();

        $datos = $request->validate([
            'email' => 'required|email:rfc|max:150',
        ], [
            'email.required' => 'Ingresa tu correo.',
            'email.email' => 'Ingresa un correo válido, por ejemplo nombre@dominio.com.',
            'email.max' => 'El correo no puede tener más de 150 caracteres.',
        ]);

        $superadmin = User::where('email', $datos['email'])->where('rol', 'superadmin')->first();

        $codigo = (string) random_int(100000, 999999);

        // Se guarda siempre (exista o no el correo) para responder igual
        // y no revelar qué correos están registrados.
        $_SESSION['sa_rec'] = [
            'email' => $datos['email'],
            'existe' => (bool) $superadmin,
            'hash' => Hash::make($codigo),
            'expira' => time() + self::MINUTOS_VALIDEZ * 60,
            'intentos' => 0,
            'verificado' => false,
        ];

        if ($superadmin) {
            try {
                $html = view('emails.codigo-superadmin', ['codigo' => $codigo, 'nombreAdmin' => $superadmin->name])->render();
                Mail::html(
                    $html,
                    function ($mensaje) use ($datos) {
                        $mensaje->to($datos['email'])->subject('Código de recuperación - PrismaSalud Superadmin');
                    }
                );
            } catch (\Throwable $e) {
                Log::error('No se pudo enviar el código de recuperación del superadmin: ' . $e->getMessage());
            }
        }

        return redirect()->route('superadmin.recuperar.codigo')
            ->with('info', 'Si el correo está registrado, te enviamos un código de 6 números. Revisa tu bandeja.');
    }

    // ---------- PASO 2: escribir el código ----------
    public function paso2()
    {
        $this->iniciarSesionPhp();

        if (! isset($_SESSION['sa_rec'])) {
            return redirect()->route('superadmin.recuperar');
        }

        return view('superadmin.recuperar', [
            'paso' => 2,
            'correoOculto' => $this->ocultarCorreo($_SESSION['sa_rec']['email']),
        ]);
    }

    public function verificarCodigo(Request $request)
    {
        $this->iniciarSesionPhp();

        $datos = $request->validate([
            'codigo' => ['required', 'regex:/^[0-9]{6}$/'],
        ], [
            'codigo.required' => 'Ingresa el código que te llegó al correo.',
            'codigo.regex' => 'El código tiene 6 números.',
        ]);

        $estado = $_SESSION['sa_rec'] ?? null;
        if (! $estado) {
            return redirect()->route('superadmin.recuperar');
        }

        if (time() > $estado['expira'] || $estado['intentos'] >= self::MAX_INTENTOS) {
            unset($_SESSION['sa_rec']);
            return redirect()->route('superadmin.recuperar')
                ->withErrors(['email' => 'El código venció o hiciste muchos intentos. Pide uno nuevo.']);
        }

        if (! $estado['existe'] || ! Hash::check($datos['codigo'], $estado['hash'])) {
            $_SESSION['sa_rec']['intentos']++;
            return back()->withErrors(['codigo' => 'El código no es correcto.']);
        }

        session_regenerate_id(true);
        $_SESSION['sa_rec']['verificado'] = true;

        return redirect()->route('superadmin.recuperar.nueva');
    }

    // ---------- PASO 3: contraseña nueva ----------
    public function paso3()
    {
        $this->iniciarSesionPhp();

        if (! ($_SESSION['sa_rec']['verificado'] ?? false)) {
            return redirect()->route('superadmin.recuperar');
        }

        return view('superadmin.recuperar', ['paso' => 3]);
    }

    public function guardarContrasena(Request $request)
    {
        $this->iniciarSesionPhp();

        $estado = $_SESSION['sa_rec'] ?? null;
        if (! $estado || ! ($estado['verificado'] ?? false) || time() > $estado['expira']) {
            unset($_SESSION['sa_rec']);
            return redirect()->route('superadmin.recuperar');
        }

        $request->validate([
            'password' => [
                'required', 'string', 'min:8', 'max:100', 'confirmed',
                'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[^A-Za-z0-9]/',
            ],
        ], [
            'password.required' => 'Ingresa la contraseña nueva.',
            'password.min' => 'La contraseña debe tener mínimo 8 caracteres.',
            'password.max' => 'La contraseña no puede tener más de 100 caracteres.',
            'password.confirmed' => 'Las dos contraseñas no coinciden.',
            'password.regex' => 'La contraseña debe tener una mayúscula, una minúscula, un número y un carácter especial (por ejemplo * # ! $).',
        ]);

        $actualizado = DB::table('users')
            ->where('email', $estado['email'])
            ->where('rol', 'superadmin')
            ->update([
                'password' => Hash::make($request->input('password')),
                'updated_at' => now(),
            ]);

        unset($_SESSION['sa_rec']);
        session_regenerate_id(true);

        if (! $actualizado) {
            return redirect()->route('superadmin.recuperar');
        }

        return redirect()->route('superadmin.login')
            ->with('exito', 'Contraseña actualizada. Ya puedes iniciar sesión.');
    }

    // ---------- Ayudas ----------
    private function iniciarSesionPhp(): void
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
    }

    private function ocultarCorreo(string $correo): string
    {
        [$usuario, $dominio] = array_pad(explode('@', $correo, 2), 2, '');
        return substr($usuario, 0, 2) . str_repeat('*', max(strlen($usuario) - 2, 3)) . '@' . $dominio;
    }
}