<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class CatalogoProcedimiento extends Model
{
    protected $table = 'catalogo_procedimiento';

    protected $fillable = [
        'id_tipo_procedimiento', 'nombre', 'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // Categoría a la que pertenece el procedimiento
    public function tipoProcedimiento()
    {
        return $this->belongsTo(TipoProcedimiento::class, 'id_tipo_procedimiento');
    }
}
