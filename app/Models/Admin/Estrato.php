<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Estrato extends Model
{
    protected $table = 'estratos';
    protected $primaryKey = 'id_estrato';

    protected $fillable = ['numero', 'activo'];

    public function scopeActivo($query)
    {
        return $query->where('activo', 1)->orderBy('numero');
    }
}
