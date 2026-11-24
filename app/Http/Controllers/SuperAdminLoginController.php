<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Login SEPARADO del Superadministrador (dueños de PrismaSalud).
 * Usa la tabla "users" de Laravel, no la tabla "usuarios" de las IPS,
 * y tiene su propia URL: /super-admin/login
 */
class SuperAdminLoginController extends Controller
{
    public function mostrarFormulario()
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }

        // Si ya inició sesión, lo mandamos directo a su panel
        if (isset($_SESSION['superadmin_id'])) {
            return redirect()->route('superadmin.dashboard');
        }

        return view('superadmin.login');
    }

    public function iniciarSesion(Request $request)
    {
        $datos = $request->validate([
            'email' => 'required|email:rfc|max:150',
            'password' => 'required|string|min:8|max:100',
        ], [
            'email.required' => 'Ingresa tu correo.',
            'email.email' => 'Ingresa un correo válido, por ejemplo nombre@dominio.com.',
            'email.max' => 'El correo no puede tener más de 150 caracteres.',
            'password.required' => 'Ingresa tu contraseña.',
            'password.min' => 'La contraseña debe tener mínimo 8 caracteres.',
            'password.max' => 'La contraseña no puede tener más de 100 caracteres.',
        ]);

        $superadmin = User::where('email', $datos['email'])
            ->where('rol', 'superadmin')
            ->first();

        // Mismo mensaje si el correo no existe o la contraseña está mal,
        // para no revelar qué correos están registrados.
        if (! $superadmin || ! Hash::check($datos['password'], $superadmin->password)) {
            return back()
                ->withErrors(['email' => 'Correo o contraseña incorrectos.'])
                ->withInput($request->only('email'));
        }

        if (session_status() === PHP_SESSION_NONE) { session_start(); }

        // Credenciales correctas: NO se abre sesión todavía.
        // Se envía un código de verificación al correo del Superadmin.
        $codigo = (string) random_int(100000, 999999);

        $_SESSION['sa_login'] = [
            'id' => $superadmin->id,
            'nombre' => $superadmin->name,
            'email' => $superadmin->email,
            'hash' => Hash::make($codigo),
            'expira' => time() + 600, // 10 minutos
            'intentos' => 0,
        ];

        try {
            $html = view('emails.codigo-login-superadmin', ['codigo' => $codigo, 'nombreAdmin' => $superadmin->name])->render();
            Mail::html(
                $html,
                function ($mensaje) use ($superadmin) {
                    $mensaje->to($superadmin->email)->subject('Código de verificación - PrismaSalud Superadmin');
                }
            );
        } catch (\Throwable $e) {
            Log::error('No se pudo enviar el código de verificación del superadmin: ' . $e->getMessage());
        }

        return redirect()->route('superadmin.login.codigo')
            ->with('info', 'Te enviamos un código de 6 números a tu correo. Escríbelo para entrar.');
    }

    // ---------- Paso 2: código de verificación ----------
    public function mostrarCodigo()
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }

        if (isset($_SESSION['superadmin_id'])) {
            return redirect()->route('superadmin.dashboard');
        }

        if (! isset($_SESSION['sa_login'])) {
            return redirect()->route('superadmin.login');
        }

        return view('superadmin.login-codigo', [
            'correoOculto' => $this->ocultarCorreo($_SESSION['sa_login']['email']),
        ]);
    }

    public function verificarCodigo(Request $request)
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }

        $datos = $request->validate([
            'codigo' => ['required', 'regex:/^[0-9]{6}$/'],
        ], [
            'codigo.required' => 'Ingresa el código que te llegó al correo.',
            'codigo.regex' => 'El código tiene 6 números.',
        ]);

        $estado = $_SESSION['sa_login'] ?? null;
        if (! $estado) {
            return redirect()->route('superadmin.login');
        }

        if (time() > $estado['expira'] || $estado['intentos'] >= 5) {
            unset($_SESSION['sa_login']);
            return redirect()->route('superadmin.login')
                ->withErrors(['email' => 'El código venció o hiciste muchos intentos. Inicia sesión de nuevo.']);
        }

        if (! Hash::check($datos['codigo'], $estado['hash'])) {
            $_SESSION['sa_login']['intentos']++;
            return back()->withErrors(['codigo' => 'El código no es correcto.']);
        }

        session_regenerate_id(true);
        $_SESSION['superadmin_id'] = $estado['id'];
        $_SESSION['superadmin_nombre'] = $estado['nombre'];
        unset($_SESSION['sa_login']);

        // LOG DE INGRESOS: dispara el trigger trg_log_ingreso_users
        DB::table('users')->where('id', $estado['id'])
            ->update(['ultimo_ingreso' => now()]);

        return redirect()->route('superadmin.dashboard');
    }

    public function reenviarCodigo()
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }

        $estado = $_SESSION['sa_login'] ?? null;
        if (! $estado) {
            return redirect()->route('superadmin.login');
        }

        $codigo = (string) random_int(100000, 999999);
        $_SESSION['sa_login']['hash'] = Hash::make($codigo);
        $_SESSION['sa_login']['expira'] = time() + 600;
        $_SESSION['sa_login']['intentos'] = 0;

        try {
            $html = view('emails.codigo-login-superadmin', ['codigo' => $codigo, 'nombreAdmin' => $estado['nombre']])->render();
            Mail::html(
                $html,
                function ($mensaje) use ($estado) {
                    $mensaje->to($estado['email'])->subject('Código de verificación - PrismaSalud Superadmin');
                }
            );
        } catch (\Throwable $e) {
            Log::error('No se pudo reenviar el código de verificación del superadmin: ' . $e->getMessage());
        }

        return back()->with('info', 'Te enviamos un código nuevo. Revisa tu bandeja.');
    }

    private function ocultarCorreo(string $correo): string
    {
        [$usuario, $dominio] = array_pad(explode('@', $correo, 2), 2, '');
        return substr($usuario, 0, 2) . str_repeat('*', max(strlen($usuario) - 2, 3)) . '@' . $dominio;
    }

    public function cerrarSesion()
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }

        unset($_SESSION['superadmin_id'], $_SESSION['superadmin_nombre'], $_SESSION['sa_login']);
        session_regenerate_id(true);

        return redirect()->route('superadmin.login');
    }
}