<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;

class PanelUsuarioController extends Controller
{
    /**
     * Muestra el index del panel del Usuario (lo que ve una persona
     * con rol distinto a Administrador al iniciar sesión).
     */
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $usuario = Usuario::find($_SESSION['usuario_cedula']);

        // Resumen rápido del usuario (datos de ejemplo/placeholder por ahora).
        $resumen = [
            ['icono' => 'citas',        'numero' => '2',   'texto' => 'Citas programadas'],
            ['icono' => 'medicamentos', 'numero' => '3',   'texto' => 'Medicamentos activos'],
            ['icono' => 'historia',     'numero' => '1',   'texto' => 'Historia clínica'],
            ['icono' => 'eps',          'numero' => 'Activa', 'texto' => 'Mi afiliación'],
        ];

        // Servicios disponibles para el usuario.
        $servicios = [
            ['icono' => 'citas',         'titulo' => 'Agendar cita',       'descripcion' => 'Solicita una cita médica en la sede de tu preferencia.'],
            ['icono' => 'lista',         'titulo' => 'Ver mis citas',      'descripcion' => 'Consulta el historial y el estado de tus citas.'],
            ['icono' => 'historia',      'titulo' => 'Historia clínica',   'descripcion' => 'Accede a tus diagnósticos y a tu historia clínica.'],
            ['icono' => 'medicamentos',  'titulo' => 'Mis medicamentos',   'descripcion' => 'Revisa tus prescripciones y entregas de medicamentos.'],
            ['icono' => 'afiliaciones',  'titulo' => 'Mi afiliación',      'descripcion' => 'Consulta los datos de tu afiliación y tus beneficiarios.'],
            ['icono' => 'perfil',        'titulo' => 'Mi perfil',          'descripcion' => 'Actualiza tus datos personales y de contacto.'],
        ];

        return view('usuario.inicio', compact('usuario', 'resumen', 'servicios'))->with('seccionActiva', 'inicio');
    }
}