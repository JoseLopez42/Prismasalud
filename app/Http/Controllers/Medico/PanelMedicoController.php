<?php

namespace App\Http\Controllers\Medico;

use App\Http\Controllers\Controller;
use App\Models\Admin\CitaMedica;
use App\Models\Admin\HistoriaClinica;
use App\Models\Admin\Medico;

class PanelMedicoController extends Controller
{
    /**
     * Inicio del panel del médico: sus citas programadas, desde donde
     * puede abrir la historia médica del paciente que va a atender.
     */
    public function inicio()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $cedulaMedico = $_SESSION['medico_cedula'];

        $medico = Medico::with('especialidad')->find($cedulaMedico);

        $hoy = now()->toDateString();

        // Citas próximas (hoy en adelante) y anteriores (ya pasadas)
        $citasProximas = CitaMedica::with(['paciente', 'consultorio', 'tipoCita', 'estadoCita'])
            ->where('cedula_medico', $cedulaMedico)
            ->where('activo', true)
            ->whereDate('fecha', '>=', $hoy)
            ->orderBy('fecha')
            ->orderBy('hora_inicio')
            ->get();

        $citasAnteriores = CitaMedica::with(['paciente', 'consultorio', 'tipoCita', 'estadoCita'])
            ->where('cedula_medico', $cedulaMedico)
            ->where('activo', true)
            ->whereDate('fecha', '<', $hoy)
            ->orderBy('fecha', 'desc')
            ->orderBy('hora_inicio', 'desc')
            ->limit(10)
            ->get();

        $citasHoy = CitaMedica::where('cedula_medico', $cedulaMedico)
            ->where('activo', true)
            ->whereDate('fecha', $hoy)
            ->count();

        // Resumen para las tarjetas superiores
        $resumen = [
            ['icono' => 'citas',    'numero' => $citasHoy,             'texto' => 'Citas de hoy'],
            ['icono' => 'citas',    'numero' => $citasProximas->count(), 'texto' => 'Próximas citas'],
            ['icono' => 'historia', 'numero' => HistoriaClinica::where('cedula_medico', $cedulaMedico)->count(), 'texto' => 'Historias creadas'],
        ];

        $seccionActiva = 'inicio';

        return view('medico.inicio', compact('medico', 'citasProximas', 'citasAnteriores', 'citasHoy', 'resumen', 'seccionActiva'));
    }
}
