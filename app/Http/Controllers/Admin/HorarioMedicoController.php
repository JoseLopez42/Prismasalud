<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Consultorio;
use App\Models\Admin\HorarioMedico;
use App\Models\Admin\Medico;
use Illuminate\Http\Request;

class HorarioMedicoController extends Controller
{
    // Lista todos los horarios (con médico y consultorio resueltos)
    public function index()
    {
        $horarios = HorarioMedico::with(['medico.especialidad', 'consultorio'])
            ->orderBy('fecha')
            ->orderBy('hora_inicio')
            ->get();

        $seccionActiva = 'medicos';
        return view('admin.medicos.horario.index', compact('horarios', 'seccionActiva'));
    }

    // Formulario para crear un horario
    public function create()
    {
        $medicos = Medico::where('estado', 'activo')->orderBy('apellidos')->get();
        $consultorios = Consultorio::where('activo', true)->orderBy('nombre')->get();
        $seccionActiva = 'medicos';
        return view('admin.medicos.horario.create', compact('medicos', 'consultorios', 'seccionActiva'));
    }

    // Guarda el nuevo horario
    public function store(Request $request)
    {
        $datos = $this->validar($request);

        if ($this->hayTraslape($datos, null)) {
            return back()->withInput()->withErrors([
                'hora_inicio' => 'Ese médico ya tiene un horario que se traslapa en esa fecha y hora.',
            ]);
        }

        HorarioMedico::create($datos);

        return redirect()->route('horario_medico.index')->with('exito', 'Horario creado correctamente.');
    }

    // Formulario para editar un horario
    public function edit($id)
    {
        $horario = HorarioMedico::with('medico', 'consultorio')->findOrFail($id);
        $medicos = Medico::orderBy('apellidos')->get();
        $consultorios = Consultorio::orderBy('nombre')->get();
        $seccionActiva = 'medicos';
        return view('admin.medicos.horario.edit', compact('horario', 'medicos', 'consultorios', 'seccionActiva'));
    }

    // Actualiza el horario
    public function update(Request $request, $id)
    {
        $horario = HorarioMedico::findOrFail($id);
        $datos = $this->validar($request);

        if ($this->hayTraslape($datos, $horario->id)) {
            return back()->withInput()->withErrors([
                'hora_inicio' => 'Ese médico ya tiene un horario que se traslapa en esa fecha y hora.',
            ]);
        }

        $horario->update($datos);

        return redirect()->route('horario_medico.index')->with('exito', 'Horario actualizado correctamente.');
    }

    // Activa o desactiva el horario (en vez de eliminarlo)
    public function cambiarEstado($id)
    {
        $horario = HorarioMedico::findOrFail($id);
        $horario->activo = !$horario->activo;
        $horario->save();

        $mensaje = $horario->activo
            ? 'Horario activado correctamente.'
            : 'Horario desactivado correctamente.';

        return redirect()->route('horario_medico.index')->with('exito', $mensaje);
    }

    // ---------- Ayudas ----------

    private function validar(Request $request): array
    {
        return $request->validate([
            'cedula_medico'   => 'required|exists:medico,cedula',
            'id_consultorio'  => 'required|exists:consultorio,id',
            'fecha'           => 'required|date',
            'hora_inicio'     => 'required|date_format:H:i',
            'hora_fin'        => 'required|date_format:H:i|after:hora_inicio',
        ]);
    }

    /**
     * Evita que dos horarios del mismo médico en la misma fecha se
     * solapen (ej: 08:00-12:00 y 11:00-14:00).
     */
    private function hayTraslape(array $datos, ?int $ignorarId): bool
    {
        return HorarioMedico::where('cedula_medico', $datos['cedula_medico'])
            ->where('fecha', $datos['fecha'])
            ->when($ignorarId, fn ($q) => $q->where('id', '!=', $ignorarId))
            ->where('hora_inicio', '<', $datos['hora_fin'])
            ->where('hora_fin', '>', $datos['hora_inicio'])
            ->exists();
    }
}
