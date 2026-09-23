<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Eps extends Model
{
    protected $table = 'eps';

    // PK = NIT (string), sin autoincrementar
    protected $primaryKey = 'nit_eps';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['nit_eps', 'nombre_eps', 'telefono_contacto', 'id_sede', 'activo'];

    // Una EPS pertenece a una sede
    public function sede()
    {
        return $this->belongsTo(Sede::class, 'id_sede');
    }
}