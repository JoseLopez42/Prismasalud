<?php

namespace App\Models\Admin;

use App\Models\Usuario;
use Illuminate\Database\Eloquent\Model;

class Prescripcion extends Model
{
    protected $table = 'prescripcion';

    protected $fillable = [
        'id_cita', 'cedula_paciente', 'cedula_medico',
        'medicamento', 'dosis', 'frecuencia', 'duracion',
        'indicaciones', 'fecha', 'activo',
    ];

    protected $casts = [
        'fecha' => 'date',
        'activo' => 'boolean',
    ];

    // Paciente al que se le formuló el medicamento
    public function paciente()
    {
        return $this->belongsTo(Usuario::class, 'cedula_paciente', 'cedula');
    }

    // Médico que formuló
    public function medico()
    {
        return $this->belongsTo(Medico::class, 'cedula_medico', 'cedula');
    }

    // Cita que originó la prescripción
    public function cita()
    {
        return $this->belongsTo(CitaMedica::class, 'id_cita');
    }
}
