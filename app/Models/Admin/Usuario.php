<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

// La tabla `usuarios` (PK cedula). Modelo aparte de App\Models\Usuario
// para usar solo los campos necesarios del panel de afiliaciones.
class Usuario extends Model
{
    protected $table = 'usuarios';

    protected $primaryKey = 'cedula';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'cedula', 'nombres', 'apellidos', 'edad', 'telefono', 'correo',
        'foto', 'contrasena', 'tipo_usuario_id', 'nit_eps', 'activo',
        'debe_crear_contrasena',
    ];
}
