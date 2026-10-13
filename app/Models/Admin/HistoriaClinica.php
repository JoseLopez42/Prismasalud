<?php

namespace App\Models\Admin;

use App\Models\Usuario;
use Illuminate\Database\Eloquent\Model;

class HistoriaClinica extends Model
{
    protected $table = 'historia_clinica';

    protected $fillable = [
        'cedula_paciente', 'cedula_medico', 'id_cita', 'fecha',
        'motivo_consulta', 'antecedentes', 'examen_fisico', 'observaciones',
        'activo',
    ];

    protected $casts = [
        'fecha' => 'date',
        'activo' => 'boolean',
    ];

    // Paciente (usuario) al que pertenece la historia
    public function paciente()
    {
        return $this->belongsTo(Usuario::class, 'cedula_paciente', 'cedula');
    }

    // Médico que elaboró la historia
    public function medico()
    {
        return $this->belongsTo(Medico::class, 'cedula_medico', 'cedula');
    }

    // Cita que originó la atención
    public function cita()
    {
        return $this->belongsTo(CitaMedica::class, 'id_cita');
    }

    // Diagnósticos asociados a esta historia
    public function diagnosticos()
    {
        return $this->hasMany(Diagnostico::class, 'id_historia_clinica');
    }

    // Enfermedades asociadas a esta historia
    public function enfermedades()
    {
        return $this->hasMany(Enfermedad::class, 'id_historia_clinica');
    }
}
