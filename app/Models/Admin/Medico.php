<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Medico extends Model
{
    protected $table = 'medico';

    // La PK es la cédula (INT), NO autoincremental, igual que "usuarios".
    protected $primaryKey = 'cedula';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        // REC-03: empresa_id es la IPS del médico; la asigna el Administrador.
        'cedula', 'empresa_id', 'nombres', 'apellidos', 'foto', 'correo', 'telefono',
        'direccion', 'contrasena', 'estado', 'id_especialidad',
    ];

    // Nunca exponer la contraseña (ni siquiera en collect() / toArray())
    protected $hidden = ['contrasena'];

    // Compatibilidad con el partial "acciones_fila", que espera un
    // booleano $activo como en el resto de tablas del panel.
    public function getActivoAttribute(): bool
    {
        return $this->estado === 'activo';
    }

    // El médico pertenece a una especialidad
    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class, 'id_especialidad');
    }

    // Horarios de atención del médico (FK: cedula_medico -> cedula)
    public function horarios()
    {
        return $this->hasMany(HorarioMedico::class, 'cedula_medico', 'cedula');
    }
}
