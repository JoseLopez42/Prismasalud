<?php

namespace App\Http\Controllers;

use App\Models\Admin\CitaMedica;
use App\Models\Admin\Consultorio;
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

    // Formulario para agendar una cita
    public function crearCita()
    {
        $medicos = Medico::with('especialidad')->where('estado', 'activo')->orderBy('apellidos')->get();
        $tiposCita = TipoCita::where('activo', true)->orderBy('nombre')->get();

        $seccionActiva = 'agendar';

        return view('usuario.citas.create', compact('medicos', 'tiposCita', 'seccionActiva'));
    }

    // Guarda la cita del paciente y la asigna directamente a un médico
    public function guardarCita(Request $request)
    {
        $datos = $request->validate([
            'cedula_medico' => 'required|exists:medico,cedula',
            'id_tipo_cita'  => 'required|exists:tipo_cita,id',
            'fecha'         => 'required|date|after_or_equal:today',
            'hora_inicio'   => 'required|date_format:H:i',
            'hora_fin'      => 'required|date_format:H:i|after:hora_inicio',
            'motivo'        => 'nullable|string|max:255',
        ]);

        $cedulaUsuario = $this->cedulaUsuario();

        // El consultorio se asigna automáticamente: primero el que tenga
        // el médico agendado para esa fecha, si no, el primero disponible.
        $horario = HorarioMedico::where('cedula_medico', $datos['cedula_medico'])
            ->where('fecha', $datos['fecha'])
            ->where('activo', true)
            ->first();

        $idConsultorio = $horario->id_consultorio ?? Consultorio::where('activo', true)->orderBy('id')->value('id');

        if (! $idConsultorio) {
            return back()->withInput()->withErrors([
                'cedula_medico' => 'No hay consultorios disponibles en este momento. Contacta a la IPS.',
            ]);
        }

        // Estado inicial de la cita: "Pendiente" (o el primero activo)
        $idEstado = EstadoCita::where('nombre', 'Pendiente')->value('id')
            ?? EstadoCita::where('activo', true)->orderBy('id')->value('id');

        if (! $idEstado) {
            return back()->withInput()->withErrors([
                'cedula_medico' => 'No hay estados de cita configurados. Contacta a la IPS.',
            ]);
        }

        $datos['cedula_usuario']  = $cedulaUsuario;
        $datos['id_consultorio']  = $idConsultorio;
        $datos['id_estado_cita']  = $idEstado;
        $datos['activo']          = true;

        if ($this->hayConflicto($datos)) {
            return back()->withInput()->withErrors([
                'hora_inicio' => 'Ya tienes una cita activa que se cruza con esa fecha y hora, o el médico ya está ocupado en ese horario.',
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
