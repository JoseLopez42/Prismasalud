<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class HorarioMedico extends Model
{
    protected $table = 'horario_medico';

    protected $fillable = [
        'cedula_medico', 'id_consultorio', 'fecha',
        'hora_inicio', 'hora_fin', 'activo',
    ];

    protected $casts = [
        'fecha' => 'date',
        'activo' => 'boolean',
    ];

    // Nombre del día de la semana de la fecha (lunes, martes, ...)
    public function getDiaSemanaAttribute(): string
    {
        return \Illuminate\Support\Carbon::parse($this->fecha)->locale('es')->dayName;
    }

    // El médico dueño del horario (PK de medico = cedula)
    public function medico()
    {
        return $this->belongsTo(Medico::class, 'cedula_medico', 'cedula');
    }

    // El consultorio donde se atiende
    public function consultorio()
    {
        return $this->belongsTo(Consultorio::class, 'id_consultorio');
    }
}
