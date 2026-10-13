<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class DetalleProcedimiento extends Model
{
    protected $table = 'detalle_procedimiento';

    protected $fillable = [
        'id_procedimiento', 'descripcion', 'cantidad', 'resultado', 'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // Procedimiento al que pertenece el detalle
    public function procedimiento()
    {
        return $this->belongsTo(Procedimiento::class, 'id_procedimiento');
    }
}
