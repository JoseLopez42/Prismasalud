<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Sede extends Model
{
    protected $table = 'sedes';
    protected $primaryKey = 'id_sede';

    protected $fillable = ['nombre_sede', 'direccion', 'telefono', 'id_ciudad'];

    // Una sede pertenece a una ciudad
    public function ciudad()
    {
        return $this->belongsTo(Ciudad::class, 'id_ciudad');
    }
}