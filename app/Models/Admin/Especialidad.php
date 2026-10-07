<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Especialidad extends Model
{
    protected $table = 'especialidad';

    protected $fillable = ['nombre', 'descripcion', 'activo'];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // Una especialidad puede tener muchos médicos
    public function medicos()
    {
        return $this->hasMany(\App\Models\Admin\Medico::class, 'id_especialidad');
    }
}
