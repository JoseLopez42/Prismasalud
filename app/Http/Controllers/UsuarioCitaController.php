<?php

namespace App\Http\Controllers;

use App\Models\Admin\CitaMedica;
use App\Models\Admin\Especialidad;
use App\Models\Admin\EstadoCita;
use App\Models\Admin\HorarioMedico;
use App\Models\Admin\Medico;
use App\Models\Admin\TipoCita;
use Illuminate\Http\Request;

class UsuarioCitaController extends Controller
{
    // Lista las citas del paciente logueado
    public function misCitas()
    {
        $citas = CitaMedica::with(['medico.especialidad', 'consultorio', 'tipoCita', 'estadoCita'])
            ->where('cedula_usuario', $this->cedulaUsuario())
            ->orderBy('fecha', 'desc')
            ->orderBy('hora_inicio')
            ->get();

        $seccionActiva = 'mis_citas';

        return view('usuario.citas.index', compact('citas', 'seccionActiva'));
    }

    // Formulario para agendar una cita (asistente por pasos)
    public function crearCita()
    {
        $medicos = Medico::with('especialidad')->where('estado', 'activo')->orderBy('apellidos')->get();
        $tiposCita = TipoCita::where('activo', true)->orderBy('nombre')->get();
        $especialidades = Especialidad::where('activo', true)->orderBy('nombre')->get();

        $seccionActiva = 'agendar';

        return view('usuario.citas.create', compact('medicos', 'tiposCita', 'especialidades', 'seccionActiva'));
    }

    /**
     * Cupos (horas) de un médico para una fecha y tipo de cita.
     * Se calculan a partir de su horario de atención (horario_medico);
     * los cupos ocupados por otras citas activas se devuelven marcados
     * con ocupado = true para que el frontend los muestre tachados.
     */
    public function cupos(Request $request)
    {
        $datos = $request->validate([
            'cedula_medico' => 'required|exists:medico,cedula',
            'id_tipo_cita'  => 'required|exists:tipo_cita,id',
            'fecha'         => 'required|date|after_or_equal:today',
        ]);

        $duracion = $this->duracionTipoCita((int) $datos['id_tipo_cita']);

        $horarios = HorarioMedico::with('consultorio')
            ->where('cedula_medico', $datos['cedula_medico'])
            ->where('fecha', $datos['fecha'])
            ->where('activo', true)
            ->orderBy('hora_inicio')
            ->get();

        if ($horarios->isEmpty()) {
            return response()->json([
                'cupos'       => [],
                'consultorio' => null,
                'duracion'    => $duracion,
                'mensaje'     => 'El médico no tiene horario de atención para esa fecha.',
            ]);
        }

        // Citas activas del médico ese día: sus horas ya están ocupadas.
        $ocupadas = CitaMedica::where('cedula_medico', $datos['cedula_medico'])
            ->where('fecha', $datos['fecha'])
            ->where('activo', true)
            ->get(['hora_inicio', 'hora_fin']);

        $cupos = [];
        $consultorio = null;
        $esHoy = $datos['fecha'] === now()->toDateString();
        $ahoraMin = (int) now()->format('H') * 60 + (int) now()->format('i');

        foreach ($horarios as $horario) {
            $consultorio = $consultorio ?: ($horario->consultorio->nombre ?? null);

            $inicio = $this->aMinutos($horario->hora_inicio);
            $fin    = $this->aMinutos($horario->hora_fin);

            for ($m = $inicio; $m + $duracion <= $fin; $m += $duracion) {
                $ocupado = $this->slotOcupado($m, $m + $duracion, $ocupadas);

                $cupos[] = [
                    'hora'   => $this->aHora($m),
                    'fin'    => $this->aHora($m + $duracion),
                    'pasado' => !$ocupado && $esHoy && $m <= $ahoraMin,
                    'ocupado'=> $ocupado,
                ];
            }
        }

        $libres = array_filter($cupos, fn ($c) => ! $c['pasado'] && ! $c['ocupado']);

        return response()->json([
            'cupos'       => array_values($cupos),
            'consultorio' => $consultorio,
            'duracion'    => $duracion,
            'mensaje'     => empty($libres) ? 'No quedan cupos libres para esa fecha.' : null,
        ]);
    }

    // Guarda la cita del paciente en el cupo seleccionado
    public function guardarCita(Request $request)
    {
        $datos = $request->validate([
            'cedula_medico' => 'required|exists:medico,cedula',
            'id_tipo_cita'  => 'required|exists:tipo_cita,id',
            'fecha'         => 'required|date|after_or_equal:today',
            'hora_inicio'   => 'required|date_format:H:i',
            'motivo'        => 'nullable|string|max:255',
        ]);

        $duracion = $this->duracionTipoCita((int) $datos['id_tipo_cita']);

        // El médico debe corresponder a la especialidad del servicio (si el tipo la define).
        $idEspecialidadTipo = TipoCita::where('id', $datos['id_tipo_cita'])->value('id_especialidad');

        if ($idEspecialidadTipo) {
            $idEspecialidadMedico = Medico::where('cedula', $datos['cedula_medico'])->value('id_especialidad');

            if ((int) $idEspecialidadMedico !== (int) $idEspecialidadTipo) {
                return back()->withInput()->withErrors([
                    'cedula_medico' => 'El médico seleccionado no pertenece a la especialidad requerida para este servicio.',
                ]);
            }
        }

        $inicioMin = $this->aMinutos($datos['hora_inicio']);
        $finMin    = $inicioMin + $duracion;

        // Si la cita es hoy, la hora de inicio debe ser posterior a la hora actual.
        if ($datos['fecha'] === now()->toDateString()) {
            $ahoraMin = (int) now()->format('H') * 60 + (int) now()->format('i');

            if ($inicioMin <= $ahoraMin) {
                return back()->withInput()->withErrors([
                    'hora_inicio' => 'No se pueden agendar citas en horarios que ya pasaron. Elige un cupo posterior a la hora actual.',
                ]);
            }
        }

        // El cupo debe caber dentro de un horario activo del médico ese día.
        $horario = HorarioMedico::where('cedula_medico', $datos['cedula_medico'])
            ->where('fecha', $datos['fecha'])
            ->where('activo', true)
            ->get()
            ->first(function ($h) use ($inicioMin, $finMin) {
                return $this->aMinutos($h->hora_inicio) <= $inicioMin
                    && $this->aMinutos($h->hora_fin) >= $finMin;
            });

        if (! $horario) {
            return back()->withInput()->withErrors([
                'hora_inicio' => 'Ese horario no está disponible para el médico seleccionado. Elige otro cupo.',
            ]);
        }

        // Estado inicial de la cita: "Pendiente" (o el primero activo)
        $idEstado = EstadoCita::where('nombre', 'Pendiente')->value('id')
            ?? EstadoCita::where('activo', true)->orderBy('id')->value('id');

        if (! $idEstado) {
            return back()->withInput()->withErrors([
                'hora_inicio' => 'No hay estados de cita configurados. Contacta a la IPS.',
            ]);
        }

        $datos['cedula_usuario'] = $this->cedulaUsuario();
        $datos['hora_fin']       = $this->aHora($finMin);
        $datos['id_consultorio'] = $horario->id_consultorio;
        $datos['id_estado_cita'] = $idEstado;
        $datos['activo']         = true;

        if ($this->hayConflicto($datos)) {
            return back()->withInput()->withErrors([
                'hora_inicio' => 'Ese cupo acaba de ocuparse. Vuelve a elegir un horario disponible.',
            ]);
        }

        CitaMedica::create($datos);

        return redirect()
            ->route('usuario.citas.index')
            ->with('exito', 'Tu cita fue agendada correctamente y quedó asignada al médico seleccionado.');
    }

    // ---------- Ayudas ----------

    private function cedulaUsuario(): int
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return (int) $_SESSION['usuario_cedula'];
    }

    // Duración (en minutos) del tipo de cita; 30 por defecto.
    private function duracionTipoCita(int $idTipoCita): int
    {
        $duracion = TipoCita::where('id', $idTipoCita)->value('duracion_minutos');

        return max(5, (int) ($duracion ?: 30));
    }

    // Convierte una hora "HH:MM" o "HH:MM:SS" a minutos desde medianoche.
    private function aMinutos(?string $hora): int
    {
        [$h, $m] = array_map('intval', explode(':', substr((string) $hora, 0, 5)));

        return $h * 60 + $m;
    }

    // Convierte minutos desde medianoche a "HH:MM".
    private function aHora(int $minutos): string
    {
        return sprintf('%02d:%02d', intdiv($minutos, 60), $minutos % 60);
    }

    // ¿El rango [inicio, fin) se cruza con alguna cita ya ocupada?
    private function slotOcupado(int $inicio, int $fin, $citas): bool
    {
        foreach ($citas as $cita) {
            $cInicio = $this->aMinutos($cita->hora_inicio);
            $cFin    = $this->aMinutos($cita->hora_fin);

            if ($inicio < $cFin && $fin > $cInicio) {
                return true;
            }
        }

        return false;
    }

    /**
     * Evita cruces de horario para el mismo médico, consultorio o paciente.
     */
    private function hayConflicto(array $datos): bool
    {
        $base = CitaMedica::where('fecha', $datos['fecha'])
            ->where('activo', true)
            ->where('hora_inicio', '<', $datos['hora_fin'])
            ->where('hora_fin', '>', $datos['hora_inicio']);

        return (clone $base)->where('cedula_medico', $datos['cedula_medico'])->exists()
            || (clone $base)->where('id_consultorio', $datos['id_consultorio'])->exists()
            || (clone $base)->where('cedula_usuario', $datos['cedula_usuario'])->exists();
    }
}
