<?php

namespace App\Models\Admin;

use App\Models\Usuario;
use Illuminate\Database\Eloquent\Model;

class Enfermedad extends Model
{
    protected $table = 'enfermedad';

    protected $fillable = [
        'cedula_paciente', 'cedula_medico', 'id_historia_clinica',
        'nombre', 'descripcion', 'fecha', 'activo',
    ];

    protected $casts = [
        'fecha' => 'date',
        'activo' => 'boolean',
    ];

    public function paciente()
    {
        return $this->belongsTo(Usuario::class, 'cedula_paciente', 'cedula');
    }

    public function medico()
    {
        return $this->belongsTo(Medico::class, 'cedula_medico', 'cedula');
    }

    public function historiaClinica()
    {
        return $this->belongsTo(HistoriaClinica::class, 'id_historia_clinica');
    }
}
