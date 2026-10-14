<?php

namespace App\Http\Controllers\Medico;

use App\Http\Controllers\Controller;
use App\Models\Admin\CitaMedica;
use App\Models\Admin\HorarioMedico;
use App\Models\Admin\Medico;

class HorarioMedicoController extends Controller
{
    private function cedulaMedico(): int
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return (int) $_SESSION['medico_cedula'];
    }

    /**
     * Todos los horarios programados del médico, con el número de
     * pacientes agendados en cada uno.
     */
    public function index()
    {
        $cedulaMedico = $this->cedulaMedico();

        $medico = Medico::with('especialidad')->find($cedulaMedico);

        $hoy = now()->toDateString();

        $horarios = HorarioMedico::with('consultorio')
            ->where('cedula_medico', $cedulaMedico)
            ->where('activo', true)
            ->orderBy('fecha')
            ->orderBy('hora_inicio')
            ->get();

        foreach ($horarios as $horario) {
            $horario->total_pacientes = $this->citasDelHorario($horario, $cedulaMedico)->count();
            $horario->es_pasado = $horario->fecha->toDateString() < $hoy;
        }

        $proximos = $horarios->filter(fn ($h) => !$h->es_pasado)->values();
        $anteriores = $horarios->filter(fn ($h) => $h->es_pasado)->sortByDesc('fecha')->take(10)->values();

        $seccionActiva = 'horarios';

        return view('medico.horario.index', compact('medico', 'proximos', 'anteriores', 'seccionActiva'));
    }

    /**
     * Detalle de un horario: los pacientes agendados en ese bloque de
     * tiempo. Si no hay nadie, se muestra vacío (sin datos de pacientes).
     */
    public function detalle($id)
    {
        $cedulaMedico = $this->cedulaMedico();

        $medico = Medico::with('especialidad')->find($cedulaMedico);

        $horario = HorarioMedico::with('consultorio')
            ->where('cedula_medico', $cedulaMedico)
            ->findOrFail($id);

        $pacientes = $this->citasDelHorario($horario, $cedulaMedico);

        $seccionActiva = 'horarios';

        return view('medico.horario.detalle', compact('medico', 'horario', 'pacientes', 'seccionActiva'));
    }

    /**
     * Citas activas que caen dentro del bloque del horario (misma fecha
     * y hora de inicio dentro de [hora_inicio, hora_fin)).
     */
    private function citasDelHorario(HorarioMedico $horario, int $cedulaMedico)
    {
        return CitaMedica::with(['paciente', 'tipoCita', 'estadoCita'])
            ->where('cedula_medico', $cedulaMedico)
            ->where('activo', true)
            ->whereDate('fecha', $horario->fecha->toDateString())
            ->where('hora_inicio', '>=', $horario->hora_inicio)
            ->where('hora_inicio', '<', $horario->hora_fin)
            ->orderBy('hora_inicio')
            ->get();
    }
}
