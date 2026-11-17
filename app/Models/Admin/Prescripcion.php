<?php

namespace App\Models\Admin;

use App\Models\Usuario;
use Illuminate\Database\Eloquent\Model;

class Prescripcion extends Model
{
    protected $table = 'prescripcion';

    protected $fillable = [
        'id_cita', 'cedula_paciente', 'cedula_medico', 'id_medicamento',
        'medicamento', 'dosis', 'frecuencia', 'duracion',
        'indicaciones', 'fecha', 'activo',
        'estado', 'fecha_entrega', 'cedula_farmaceuta',
        'cantidad_entregada', 'observacion_entrega',
    ];

    protected $casts = [
        'fecha' => 'date',
        'fecha_entrega' => 'datetime',
        'cantidad_entregada' => 'integer',
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

    // Medicamento del catálogo que se formuló
    public function medicamentoCatalogo()
    {
        return $this->belongsTo(Medicamento::class, 'id_medicamento', 'codigo');
    }

    // Farmacéutico que registró la entrega
    public function farmaceutico()
    {
        return $this->belongsTo(Usuario::class, 'cedula_farmaceuta', 'cedula');
    }

    // ¿Ya fue entregada por la farmacia?
    public function entregada(): bool
    {
        return $this->estado === 'entregada';
    }
}
