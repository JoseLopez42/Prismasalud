<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class TipoProcedimiento extends Model
{
    protected $table = 'tipo_procedimiento';

    protected $fillable = [
        'nombre', 'descripcion', 'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // Procedimientos clasificados con este tipo
    public function procedimientos()
    {
        return $this->hasMany(Procedimiento::class, 'id_tipo_procedimiento');
    }
}
