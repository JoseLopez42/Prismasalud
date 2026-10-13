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

        // Todas las citas asignadas a este médico (las programadas y las pasadas)
        $citas = CitaMedica::with(['paciente', 'consultorio', 'tipoCita', 'estadoCita'])
            ->where('cedula_medico', $cedulaMedico)
            ->orderBy('fecha', 'desc')
            ->orderBy('hora_inicio')
            ->get();

        // Resumen para las tarjetas superiores
        $resumen = [
            ['icono' => 'citas',    'numero' => $citas->count(),                                       'texto' => 'Citas asignadas'],
            ['icono' => 'citas',    'numero' => $citas->where('fecha', '>=', now()->toDateString())->count(), 'texto' => 'Próximas citas'],
            ['icono' => 'historia', 'numero' => HistoriaClinica::where('cedula_medico', $cedulaMedico)->count(), 'texto' => 'Historias creadas'],
        ];

        $seccionActiva = 'inicio';

        return view('medico.inicio', compact('medico', 'citas', 'resumen', 'seccionActiva'));
    }
}
