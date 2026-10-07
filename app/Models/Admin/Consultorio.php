<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Consultorio extends Model
{
    protected $table = 'consultorio';

    protected $fillable = ['nombre', 'descripcion', 'ubicacion', 'id_sede', 'activo'];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // El consultorio puede estar (o no) en una sede
    public function sede()
    {
        return $this->belongsTo(Sede::class, 'id_sede');
    }

    // Horarios asignados a este consultorio
    public function horarios()
    {
        return $this->hasMany(HorarioMedico::class, 'id_consultorio');
    }
}
