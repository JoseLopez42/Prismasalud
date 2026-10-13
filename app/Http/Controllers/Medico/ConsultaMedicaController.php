<?php

namespace App\Http\Controllers\Medico;

use App\Http\Controllers\Controller;
use App\Models\Admin\CitaMedica;
use App\Models\Admin\Diagnostico;
use App\Models\Admin\Enfermedad;
use App\Models\Admin\EstadoCita;
use App\Models\Admin\HistoriaClinica;
use App\Models\Admin\Medicamento;
use App\Models\Admin\Prescripcion;
use App\Models\Admin\Procedimiento;
use App\Models\Admin\TipoProcedimiento;
use Illuminate\Http\Request;

class ConsultaMedicaController extends Controller
{
    /**
     * Página de atención de una cita: permite al médico registrar la
     * historia clínica, los diagnósticos, las enfermedades, los
     * procedimientos y los medicamentos formulados.
     */
    public function show($id)
    {
        $cita = $this->citaDelMedico($id);

        $historias = HistoriaClinica::with(['diagnosticos', 'enfermedades'])
            ->where('id_cita', $cita->id)
            ->orderBy('fecha', 'desc')
            ->get();

        $procedimientos = Procedimiento::with(['tipoProcedimiento', 'detalles'])
            ->where('id_cita', $cita->id)
            ->orderBy('fecha', 'desc')
            ->get();

        $prescripciones = Prescripcion::where('id_cita', $cita->id)
            ->orderBy('id')
            ->get();

        $medicamentos = Medicamento::where('activo', true)->orderBy('nombre')->get();
        $tipos = TipoProcedimiento::orderBy('nombre')->get();
        $estados = EstadoCita::where('activo', true)->orderBy('nombre')->get();

        $seccionActiva = 'inicio';

        return view('medico.cita.show', compact(
            'cita', 'historias', 'procedimientos', 'prescripciones',
            'medicamentos', 'tipos', 'estados', 'seccionActiva'
        ));
    }

    // Registra la historia clínica de la cita
    public function storeHistoria(Request $request, $id)
    {
        $cita = $this->citaDelMedico($id);

        $datos = $request->validate([
            'fecha'           => 'required|date',
            'motivo_consulta' => 'nullable|string',
            'antecedentes'    => 'nullable|string',
            'examen_fisico'   => 'nullable|string',
            'observaciones'   => 'nullable|string',
        ]);

        $datos['cedula_paciente'] = $cita->cedula_usuario;
        $datos['cedula_medico']   = $this->cedulaMedico();
        $datos['id_cita']         = $cita->id;
        $datos['activo']          = true;

        HistoriaClinica::create($datos);

        return back()->with('exito', 'Historia clínica registrada correctamente.');
    }

    // Registra un diagnóstico asociado a la cita
    public function storeDiagnostico(Request $request, $id)
    {
        $cita = $this->citaDelMedico($id);

        $datos = $request->validate([
            'id_historia_clinica' => 'nullable|exists:historia_clinica,id',
            'descripcion'         => 'required|string|max:255',
            'tipo'                => 'nullable|string|max:50',
            'fecha'               => 'required|date',
        ]);

        $datos['cedula_paciente'] = $cita->cedula_usuario;
        $datos['cedula_medico']   = $this->cedulaMedico();
        $datos['activo']          = true;

        Diagnostico::create($datos);

        return back()->with('exito', 'Diagnóstico registrado correctamente.');
    }

    // Registra una enfermedad asociada a la cita
    public function storeEnfermedad(Request $request, $id)
    {
        $cita = $this->citaDelMedico($id);

        $datos = $request->validate([
            'id_historia_clinica' => 'nullable|exists:historia_clinica,id',
            'nombre'              => 'required|string|max:150',
            'descripcion'         => 'nullable|string',
            'fecha'               => 'required|date',
        ]);

        $datos['cedula_paciente'] = $cita->cedula_usuario;
        $datos['cedula_medico']   = $this->cedulaMedico();
        $datos['activo']          = true;

        Enfermedad::create($datos);

        return back()->with('exito', 'Enfermedad registrada correctamente.');
    }

    // Registra un procedimiento realizado en la cita
    public function storeProcedimiento(Request $request, $id)
    {
        $cita = $this->citaDelMedico($id);

        $datos = $request->validate([
            'id_tipo_procedimiento' => 'nullable|exists:tipo_procedimiento,id',
            'fecha'                 => 'required|date',
            'descripcion'           => 'required|string|max:255',
            'observaciones'         => 'nullable|string',
        ]);

        $datos['cedula_paciente'] = $cita->cedula_usuario;
        $datos['cedula_medico']   = $this->cedulaMedico();
        $datos['id_cita']         = $cita->id;
        $datos['activo']          = true;

        Procedimiento::create($datos);

        return back()->with('exito', 'Procedimiento registrado correctamente.');
    }

    // Registra un medicamento formulado para el paciente
    public function storePrescripcion(Request $request, $id)
    {
        $cita = $this->citaDelMedico($id);

        $datos = $request->validate([
            'id_medicamento' => 'required|exists:medicamento,codigo',
            'dosis'        => 'nullable|string|max:100',
            'frecuencia'   => 'nullable|string|max:100',
            'duracion'     => 'nullable|string|max:100',
            'indicaciones' => 'nullable|string',
        ]);

        $datos['medicamento']     = Medicamento::where('codigo', $datos['id_medicamento'])->value('nombre');
        $datos['cedula_paciente'] = $cita->cedula_usuario;
        $datos['cedula_medico']   = $this->cedulaMedico();
        $datos['id_cita']         = $cita->id;
        $datos['fecha']           = now()->toDateString();
        $datos['activo']          = true;

        Prescripcion::create($datos);

        return back()->with('exito', 'Medicamento formulado correctamente.');
    }

    // Marca la cita como "Atendida"
    public function marcarAtendida($id)
    {
        $cita = $this->citaDelMedico($id);

        $idEstado = EstadoCita::where('nombre', 'Atendida')->value('id');

        if ($idEstado) {
            $cita->id_estado_cita = $idEstado;
            $cita->save();
        }

        return back()->with('exito', 'Cita marcada como atendida.');
    }

    // Actualiza un medicamento formulado (solo su autor)
    public function updatePrescripcion(Request $request, $id)
    {
        $prescripcion = Prescripcion::findOrFail($id);

        abort_unless((int) $prescripcion->cedula_medico === $this->cedulaMedico(), 403);

        $datos = $request->validate([
            'id_medicamento' => 'required|exists:medicamento,codigo',
            'dosis'        => 'nullable|string|max:100',
            'frecuencia'   => 'nullable|string|max:100',
            'duracion'     => 'nullable|string|max:100',
            'indicaciones' => 'nullable|string',
        ]);

        $datos['medicamento'] = Medicamento::where('codigo', $datos['id_medicamento'])->value('nombre');

        $prescripcion->update($datos);

        return back()->with('exito', 'Medicamento actualizado correctamente.');
    }

    // ---------- Ayudas ----------

    // Devuelve la cita solo si pertenece al médico logueado
    private function citaDelMedico($id): CitaMedica
    {
        $cita = CitaMedica::with(['paciente', 'medico.especialidad', 'consultorio', 'tipoCita', 'estadoCita'])
            ->findOrFail($id);

        abort_unless((int) $cita->cedula_medico === $this->cedulaMedico(), 403);

        return $cita;
    }

    private function cedulaMedico(): int
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return (int) $_SESSION['medico_cedula'];
    }
}
