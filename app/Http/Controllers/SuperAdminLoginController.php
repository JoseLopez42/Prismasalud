<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
        session_regenerate_id(true);

        $_SESSION['superadmin_id'] = $superadmin->id;
        $_SESSION['superadmin_nombre'] = $superadmin->name;

        return redirect()->route('superadmin.dashboard');
    }

    public function cerrarSesion()
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }

        unset($_SESSION['superadmin_id'], $_SESSION['superadmin_nombre']);
        session_regenerate_id(true);

        return redirect()->route('superadmin.login');
    }
}