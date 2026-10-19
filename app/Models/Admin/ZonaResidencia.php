<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class ZonaResidencia extends Model
{
    protected $table = 'zonas_residencia';
    protected $primaryKey = 'id_zona_residencia';

    protected $fillable = ['nombre', 'activo'];

    public function scopeActivo($query)
    {
        return $query->where('activo', 1);
    }
}
