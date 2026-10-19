<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class EstadoCivil extends Model
{
    protected $table = 'estados_civiles';
    protected $primaryKey = 'id_estado_civil';

    protected $fillable = ['nombre', 'activo'];

    public function scopeActivo($query)
    {
        return $query->where('activo', 1);
    }
}
