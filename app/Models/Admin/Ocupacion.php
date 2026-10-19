<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Ocupacion extends Model
{
    protected $table = 'ocupaciones';
    protected $primaryKey = 'id_ocupacion';

    protected $fillable = ['nombre', 'activo'];

    public function scopeActivo($query)
    {
        return $query->where('activo', 1);
    }
}
