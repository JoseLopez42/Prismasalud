<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

// La tabla `regimen` funciona como catálogo de tipos de afiliación.
class TipoAfiliacion extends Model
{
    protected $table = 'regimen';
    protected $primaryKey = 'id_regimen';

    protected $fillable = ['nombre_regimen'];
}
