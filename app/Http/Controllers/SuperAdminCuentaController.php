<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * Mi cuenta: el Superadmin cambia su nombre, correo y contraseña (tabla "users").
 */
class SuperAdminCuentaController extends Controller
{
    public function edit()
    {
        return view('superadmin.cuenta.form', ['cuenta' => $this->cuenta()]);
    }

    public function update(Request $request)
    {
        $cuenta = $this->cuenta();

        $datos = $request->validate([
            'name' => ['required', 'string', 'max:100', "regex:/^[\p{L}][\p{L}\s'.\-]*$/u"],
            'email' => ['required', 'email:rfc', 'max:150', Rule::unique('users', 'email')->ignore($cuenta->id)],
            'password_actual' => ['required_with:password', 'nullable', 'string'],
            'password' => [
                'nullable', 'string', 'min:8', 'max:100', 'confirmed',
                'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[^A-Za-z0-9]/',
            ],
        ], [
            'name.required' => 'Escribe tu nombre.',
            'name.regex' => 'El nombre solo puede tener letras.',
            'name.max' => 'El nombre no puede tener más de 100 caracteres.',
            'email.required' => 'Escribe tu correo.',
            'email.email' => 'Escribe un correo válido, por ejemplo nombre@dominio.com.',
            'email.max' => 'El correo no puede tener más de 150 caracteres.',
            'email.unique' => 'Ese correo ya lo usa otra cuenta.',
            'password_actual.required_with' => 'Escribe tu contraseña actual para poder cambiarla.',
            'password.min' => 'La contraseña debe tener mínimo 8 caracteres.',
            'password.max' => 'La contraseña no puede tener más de 100 caracteres.',
            'password.confirmed' => 'Las dos contraseñas no coinciden.',
            'password.regex' => 'La contraseña debe tener una mayúscula, una minúscula, un número y un carácter especial (por ejemplo * # ! $).',
        ]);

        $cambiaClave = ! empty($datos['password']);

        if ($cambiaClave && ! Hash::check($datos['password_actual'], $cuenta->password)) {
            return back()->withInput($request->except(['password_actual', 'password', 'password_confirmation']))
                ->withErrors(['password_actual' => 'La contraseña actual no es correcta.']);
        }

        $cuenta->name = trim($datos['name']);
        $cuenta->email = trim($datos['email']);
        if ($cambiaClave) { $cuenta->password = Hash::make($datos['password']); }
        $cuenta->save();

        $_SESSION['superadmin_nombre'] = $cuenta->name;

        return redirect()->route('superadmin.cuenta.edit')
            ->with('exito', $cambiaClave ? 'Tus datos y tu contraseña se guardaron.' : 'Tus datos se guardaron.');
    }

    private function cuenta(): User
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }

        return User::where('rol', 'superadmin')->findOrFail($_SESSION['superadmin_id'] ?? 0);
    }
}