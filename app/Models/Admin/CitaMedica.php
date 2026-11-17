<?php

namespace App\Models\Admin;

use App\Models\Usuario;
use Illuminate\Database\Eloquent\Model;

class CitaMedica extends Model
{
    protected $table = 'cita';

    protected $fillable = [
        'cedula_usuario', 'cedula_medico', 'id_consultorio',
        'id_tipo_cita', 'id_estado_cita',
        'fecha', 'hora_inicio', 'hora_fin', 'motivo', 'activo',
    ];

    protected $casts = [
        'fecha' => 'date',
        'activo' => 'boolean',
    ];

    // Nombre del día de la semana de la fecha de la cita
    public function getDiaSemanaAttribute(): string
    {
        return \Illuminate\Support\Carbon::parse($this->fecha)->locale('es')->dayName;
    }

    // El paciente de la cita (un usuario de la plataforma)
    public function paciente()
    {
        return $this->belongsTo(Usuario::class, 'cedula_usuario', 'cedula');
    }

    // El médico que atiende la cita
    public function medico()
    {
        return $this->belongsTo(Medico::class, 'cedula_medico', 'cedula');
    }

    // El consultorio donde se realiza la cita
    public function consultorio()
    {
        return $this->belongsTo(Consultorio::class, 'id_consultorio');
    }

    // Tipo de cita (consulta general, control, ...)
    public function tipoCita()
    {
        return $this->belongsTo(TipoCita::class, 'id_tipo_cita');
    }

    // Estado de la cita (pendiente, confirmada, ...)
    public function estadoCita()
    {
        return $this->belongsTo(EstadoCita::class, 'id_estado_cita');
    }

    public function atendida(): bool
    {
        return strtolower(trim((string) ($this->estadoCita->nombre ?? ''))) === 'atendida';
    }
}