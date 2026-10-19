<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class GrupoEtnico extends Model
{
    protected $table = 'grupos_etnicos';
    protected $primaryKey = 'id_grupo_etnico';

    protected $fillable = ['nombre', 'activo'];

    public function scopeActivo($query)
    {
        return $query->where('activo', 1);
    }
}
