<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class TipoCita extends Model
{
    protected $table = 'tipo_cita';

    protected $fillable = [
        'nombre', 'descripcion', 'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // Un tipo de cita puede estar en muchas citas
    public function citas()
    {
        return $this->hasMany(CitaMedica::class, 'id_tipo_cita');
    }
}