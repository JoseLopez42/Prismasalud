<?php

namespace App\Http\Controllers\Medico;

use App\Http\Controllers\Controller;
use App\Models\Admin\Medico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PerfilController extends Controller
{
    private function cedulaMedico(): int
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return (int) $_SESSION['medico_cedula'];
    }

    /**
     * Muestra el perfil del médico con sus datos y el formulario
     * para actualizar la información básica y la foto.
     */
    public function editar()
    {
        $medico = Medico::with('especialidad')->findOrFail($this->cedulaMedico());

        $seccionActiva = 'perfil';

        return view('medico.perfil', compact('medico', 'seccionActiva'));
    }

    /**
     * Actualiza los datos básicos y la foto del médico autenticado.
     * La cédula y la especialidad las administra la IPS, no el médico.
     */
    public function actualizar(Request $request)
    {
        $medico = Medico::findOrFail($this->cedulaMedico());

        $datos = $request->validate([
            'nombres'    => 'required|string|max:100',
            'apellidos'  => 'required|string|max:100',
            'correo'     => 'required|email|max:150|unique:medico,correo,' . $medico->cedula . ',cedula',
            'telefono'   => 'nullable|string|max:20',
            'direccion'  => 'nullable|string|max:200',
            'contrasena' => 'nullable|string|min:6',
            'foto'       => 'nullable|image|max:2048',
        ], [
            'foto.image'   => 'La foto debe ser una imagen (JPG o PNG).',
            'foto.max'     => 'La foto no puede superar los 2MB.',
            'correo.unique'=> 'Ese correo ya está registrado por otro médico.',
        ]);

        if ($request->hasFile('foto')) {
            $datos['foto'] = $request->file('foto')->store('medicos', 'public');
        }

        if (!empty($datos['contrasena'])) {
            $datos['contrasena'] = Hash::make($datos['contrasena']);
        } else {
            unset($datos['contrasena']);
        }

        $medico->update($datos);

        // Mantenemos la sesión sincronizada para el saludo y el avatar.
        $_SESSION['medico_nombres']  = $medico->nombres;
        $_SESSION['medico_apellidos'] = $medico->apellidos;
        $_SESSION['medico_foto']     = $medico->foto;

        return redirect()->route('medico.inicio')->with('exito', 'Perfil actualizado correctamente.');
    }
}
