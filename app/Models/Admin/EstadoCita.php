<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class EstadoCita extends Model
{
    protected $table = 'estado_cita';

    protected $fillable = [
        'nombre', 'descripcion', 'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // Un estado de cita puede estar en muchas citas
    public function citas()
    {
        return $this->hasMany(CitaMedica::class, 'id_estado_cita');
    }
}