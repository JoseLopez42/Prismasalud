<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\CitaMedica;
use App\Models\Admin\Consultorio;
use App\Models\Admin\EstadoCita;
use App\Models\Admin\Medico;
use App\Models\Admin\TipoCita;
use App\Models\Usuario;
use Illuminate\Http\Request;

class CitaMedicaController extends Controller
{
    // Lista todas las citas (con paciente, médico, consultorio, tipo y estado)
    public function index()
    {
        $citas = CitaMedica::with(['paciente', 'medico.especialidad', 'consultorio', 'tipoCita', 'estadoCita'])
            ->orderBy('fecha', 'desc')
            ->orderBy('hora_inicio')
            ->get();

        $seccionActiva = 'citas';
        return view('admin.citas.cita.index', compact('citas', 'seccionActiva'));
    }

    // Formulario para crear una cita
    public function create()
    {
        $pacientes = Usuario::orderBy('apellidos')->get();
        $medicos = Medico::where('estado', 'activo')->orderBy('apellidos')->get();
        $consultorios = Consultorio::where('activo', true)->orderBy('nombre')->get();
        $tiposCita = TipoCita::where('activo', true)->orderBy('nombre')->get();
        $estadosCita = EstadoCita::where('activo', true)->orderBy('nombre')->get();
        $seccionActiva = 'citas';
        return view('admin.citas.cita.create', compact('pacientes', 'medicos', 'consultorios', 'tiposCita', 'estadosCita', 'seccionActiva'));
    }

    // Guarda la nueva cita
    public function store(Request $request)
    {
        $datos = $this->validar($request);

        if ($this->hayConflicto($datos, null)) {
            return back()->withInput()->withErrors([
                'hora_inicio' => 'Ya existe una cita activa que se cruza con esa fecha y hora (mismo médico, consultorio o paciente).',
            ]);
        }

        CitaMedica::create($datos);

        return redirect()->route('cita.index')->with('exito', 'Cita creada correctamente.');
    }

    // Formulario para editar una cita
    public function edit($id)
    {
        $cita = CitaMedica::with('paciente', 'medico', 'consultorio', 'tipoCita', 'estadoCita')->findOrFail($id);
        $pacientes = Usuario::orderBy('apellidos')->get();
        $medicos = Medico::orderBy('apellidos')->get();
        $consultorios = Consultorio::orderBy('nombre')->get();
        $tiposCita = TipoCita::orderBy('nombre')->get();
        $estadosCita = EstadoCita::orderBy('nombre')->get();
        $seccionActiva = 'citas';
        return view('admin.citas.cita.edit', compact('cita', 'pacientes', 'medicos', 'consultorios', 'tiposCita', 'estadosCita', 'seccionActiva'));
    }

    // Actualiza la cita
    public function update(Request $request, $id)
    {
        $cita = CitaMedica::findOrFail($id);
        $datos = $this->validar($request);

        if ($this->hayConflicto($datos, $cita->id)) {
            return back()->withInput()->withErrors([
                'hora_inicio' => 'Ya existe una cita activa que se cruza con esa fecha y hora (mismo médico, consultorio o paciente).',
            ]);
        }

        $cita->update($datos);

        return redirect()->route('cita.index')->with('exito', 'Cita actualizada correctamente.');
    }

    // Activa o desactiva la cita (en vez de eliminarla)
    public function cambiarEstado($id)
    {
        $cita = CitaMedica::findOrFail($id);
        $cita->activo = !$cita->activo;
        $cita->save();

        $mensaje = $cita->activo
            ? 'Cita activada correctamente.'
            : 'Cita desactivada correctamente.';

        return redirect()->route('cita.index')->with('exito', $mensaje);
    }

    // ---------- Ayudas ----------

    private function validar(Request $request): array
    {
        return $request->validate([
            'cedula_usuario'  => 'required|exists:usuarios,cedula',
            'cedula_medico'   => 'required|exists:medico,cedula',
            'id_consultorio'  => 'required|exists:consultorio,id',
            'id_tipo_cita'    => 'required|exists:tipo_cita,id',
            'id_estado_cita'  => 'required|exists:estado_cita,id',
            'fecha'           => 'required|date',
            'hora_inicio'     => 'required|date_format:H:i',
            'hora_fin'        => 'required|date_format:H:i|after:hora_inicio',
            'motivo'          => 'nullable|string|max:255',
        ]);
    }

    /**
     * Evita citas cruzadas en la misma fecha y con horas que se
     * solapan (ej: 08:00-09:00 vs 08:30-10:00), para las citas activas:
     *  - el mismo médico no puede tener dos citas a la vez;
     *  - el mismo consultorio no puede tener dos citas a la vez;
     *  - el mismo paciente no puede tener dos citas a la vez.
     */
    private function hayConflicto(array $datos, ?int $ignorarId): bool
    {
        $base = CitaMedica::where('fecha', $datos['fecha'])
            ->where('activo', true)
            ->where('hora_inicio', '<', $datos['hora_fin'])
            ->where('hora_fin', '>', $datos['hora_inicio'])
            ->when($ignorarId, fn ($q) => $q->where('id', '!=', $ignorarId));

        $mismoMedico = (clone $base)->where('cedula_medico', $datos['cedula_medico'])->exists();
        $mismoConsultorio = (clone $base)->where('id_consultorio', $datos['id_consultorio'])->exists();
        $mismoPaciente = (clone $base)->where('cedula_usuario', $datos['cedula_usuario'])->exists();

        return $mismoMedico || $mismoConsultorio || $mismoPaciente;
    }
}