<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Medicamento extends Model
{
    protected $table = 'medicamento';

    // La llave primaria es el código del medicamento (lo digita el admin),
    // no un id autoincremental.
    protected $primaryKey = 'codigo';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'codigo', 'nombre', 'presentacion', 'descripcion', 'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];
}
