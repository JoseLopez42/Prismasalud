<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\HistoriaClinica;
use Illuminate\Http\Request;

class HistoriaClinicaController extends Controller
{
    /**
     * Lista todas las historias clínicas creadas por los médicos,
     * con opción de filtrarlas por la cédula del paciente.
     */
    public function index(Request $request)
    {
        $buscar = trim((string) $request->query('cedula', ''));

        $historias = HistoriaClinica::with(['paciente', 'medico.especialidad', 'diagnosticos', 'enfermedades'])
            ->when($buscar !== '', fn ($q) => $q->where('cedula_paciente', $buscar))
            ->orderBy('fecha', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $seccionActiva = 'historia';

        return view('admin.historia.index', compact('historias', 'buscar', 'seccionActiva'));
    }
}
