<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class TipoSangre extends Model
{
    protected $table = 'tipos_sangre';
    protected $primaryKey = 'id_tipo_sangre';

    protected $fillable = ['nombre', 'activo'];

    public function scopeActivo($query)
    {
        return $query->where('activo', 1);
    }
}
