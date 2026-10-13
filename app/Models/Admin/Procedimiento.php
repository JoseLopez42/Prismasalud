<?php

namespace App\Models\Admin;

use App\Models\Usuario;
use Illuminate\Database\Eloquent\Model;

class Procedimiento extends Model
{
    protected $table = 'procedimiento';

    protected $fillable = [
        'cedula_paciente', 'cedula_medico', 'id_cita', 'id_tipo_procedimiento',
        'fecha', 'descripcion', 'observaciones', 'activo',
    ];

    protected $casts = [
        'fecha' => 'date',
        'activo' => 'boolean',
    ];

    // Paciente (usuario) al que se le practicó el procedimiento
    public function paciente()
    {
        return $this->belongsTo(Usuario::class, 'cedula_paciente', 'cedula');
    }

    // Médico que lo realizó
    public function medico()
    {
        return $this->belongsTo(Medico::class, 'cedula_medico', 'cedula');
    }

    // Cita que originó el procedimiento
    public function cita()
    {
        return $this->belongsTo(CitaMedica::class, 'id_cita');
    }

    // Tipo de procedimiento (catálogo)
    public function tipoProcedimiento()
    {
        return $this->belongsTo(TipoProcedimiento::class, 'id_tipo_procedimiento');
    }

    // Detalles / resultados del procedimiento
    public function detalles()
    {
        return $this->hasMany(DetalleProcedimiento::class, 'id_procedimiento');
    }
}
