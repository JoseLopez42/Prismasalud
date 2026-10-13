<?php

namespace App\Http\Controllers\Medico;

use App\Http\Controllers\Controller;
use App\Models\Admin\Diagnostico;
use App\Models\Admin\Enfermedad;
use App\Models\Admin\HistoriaClinica;
use App\Models\Usuario;
use Illuminate\Http\Request;

class HistoriaMedicaController extends Controller
{
    /**
     * Búsqueda de la historia médica de un paciente por cédula.
     * Si llega una cédula por la URL (?cedula=...), se cargan el
     * paciente y sus historias, diagnósticos y enfermedades.
     */
    public function index(Request $request)
    {
        $cedulaBuscada = trim((string) $request->query('cedula', ''));
        $idCita = $request->query('cita');

        $paciente = null;
        $historias = collect();
        $diagnosticos = collect();
        $enfermedades = collect();

        if ($cedulaBuscada !== '') {
            $paciente = Usuario::with(['tipoDocumento', 'tipoUsuario'])->find($cedulaBuscada);

            if ($paciente) {
                $historias = HistoriaClinica::with('medico')
                    ->where('cedula_paciente', $paciente->cedula)
                    ->orderBy('fecha', 'desc')
                    ->get();

                $diagnosticos = Diagnostico::with('medico')
                    ->where('cedula_paciente', $paciente->cedula)
                    ->orderBy('fecha', 'desc')
                    ->get();

                $enfermedades = Enfermedad::with('medico')
                    ->where('cedula_paciente', $paciente->cedula)
                    ->orderBy('fecha', 'desc')
                    ->get();
            }
        }

        // Historias del paciente para poder asociar diagnóstico/enfermedad
        $seccionActiva = 'historia';

        return view('medico.historia.index', compact(
            'cedulaBuscada', 'paciente', 'historias', 'diagnosticos', 'enfermedades', 'idCita', 'seccionActiva'
        ));
    }

    // Guarda una nueva historia clínica del paciente
    public function storeHistoria(Request $request)
    {
        $datos = $request->validate([
            'cedula_paciente' => 'required|exists:usuarios,cedula',
            'id_cita'         => 'nullable|exists:cita,id',
            'fecha'           => 'required|date',
            'motivo_consulta' => 'nullable|string',
            'antecedentes'    => 'nullable|string',
            'examen_fisico'   => 'nullable|string',
            'observaciones'   => 'nullable|string',
        ]);

        $datos['cedula_medico'] = $this->cedulaMedico();
        $datos['activo'] = true;

        HistoriaClinica::create($datos);

        return redirect()
            ->route('medico.historia.index', ['cedula' => $datos['cedula_paciente']])
            ->with('exito', 'Historia clínica registrada correctamente.');
    }

    // Guarda un nuevo diagnóstico del paciente
    public function storeDiagnostico(Request $request)
    {
        $datos = $request->validate([
            'cedula_paciente'     => 'required|exists:usuarios,cedula',
            'id_historia_clinica' => 'nullable|exists:historia_clinica,id',
            'descripcion'         => 'required|string|max:255',
            'tipo'                => 'nullable|string|max:50',
            'fecha'               => 'required|date',
        ]);

        $datos['cedula_medico'] = $this->cedulaMedico();
        $datos['activo'] = true;

        Diagnostico::create($datos);

        return redirect()
            ->route('medico.historia.index', ['cedula' => $datos['cedula_paciente']])
            ->with('exito', 'Diagnóstico registrado correctamente.');
    }

    // Guarda una nueva enfermedad del paciente
    public function storeEnfermedad(Request $request)
    {
        $datos = $request->validate([
            'cedula_paciente'     => 'required|exists:usuarios,cedula',
            'id_historia_clinica' => 'nullable|exists:historia_clinica,id',
            'nombre'              => 'required|string|max:150',
            'descripcion'         => 'nullable|string',
            'fecha'               => 'required|date',
        ]);

        $datos['cedula_medico'] = $this->cedulaMedico();
        $datos['activo'] = true;

        Enfermedad::create($datos);

        return redirect()
            ->route('medico.historia.index', ['cedula' => $datos['cedula_paciente']])
            ->with('exito', 'Enfermedad registrada correctamente.');
    }

    // Actualiza una historia clínica existente (solo su autor)
    public function updateHistoria(Request $request, $id)
    {
        $historia = HistoriaClinica::findOrFail($id);
        $this->autorizarAutor($historia->cedula_medico);

        $datos = $request->validate([
            'fecha'           => 'required|date',
            'motivo_consulta' => 'nullable|string',
            'antecedentes'    => 'nullable|string',
            'examen_fisico'   => 'nullable|string',
            'observaciones'   => 'nullable|string',
        ]);

        $historia->update($datos);

        return back()->with('exito', 'Historia clínica actualizada correctamente.');
    }

    // Actualiza un diagnóstico existente (solo su autor)
    public function updateDiagnostico(Request $request, $id)
    {
        $diagnostico = Diagnostico::findOrFail($id);
        $this->autorizarAutor($diagnostico->cedula_medico);

        $datos = $request->validate([
            'id_historia_clinica' => 'nullable|exists:historia_clinica,id',
            'descripcion'         => 'required|string|max:255',
            'tipo'                => 'nullable|string|max:50',
            'fecha'               => 'required|date',
        ]);

        $diagnostico->update($datos);

        return back()->with('exito', 'Diagnóstico actualizado correctamente.');
    }

    // Actualiza una enfermedad existente (solo su autor)
    public function updateEnfermedad(Request $request, $id)
    {
        $enfermedad = Enfermedad::findOrFail($id);
        $this->autorizarAutor($enfermedad->cedula_medico);

        $datos = $request->validate([
            'id_historia_clinica' => 'nullable|exists:historia_clinica,id',
            'nombre'              => 'required|string|max:150',
            'descripcion'         => 'nullable|string',
            'fecha'               => 'required|date',
        ]);

        $enfermedad->update($datos);

        return back()->with('exito', 'Enfermedad actualizada correctamente.');
    }

    // ---------- Ayudas ----------

    private function autorizarAutor($cedulaMedico): void
    {
        abort_unless((int) $cedulaMedico === $this->cedulaMedico(), 403);
    }

    private function cedulaMedico(): int
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return (int) $_SESSION['medico_cedula'];
    }
}
