<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Medicamento extends Model
{
    protected $table = 'medicamento';

    protected $fillable = [
        'nombre', 'presentacion', 'descripcion', 'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];
}
