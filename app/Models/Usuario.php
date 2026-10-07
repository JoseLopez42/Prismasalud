<?php

namespace App\Models;

use App\Models\Admin\Ciudad;
use App\Models\Admin\TipoDocumento;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'usuarios';

    // PK = cedula, sin autoincrementar
    protected $primaryKey = 'cedula';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'cedula',
        'nombres',
        'apellidos',
        'edad',
        'telefono',
        'correo',
        'contrasena',
        'foto',
        'tipo_usuario_id',
        'id_ciudad',
        'id_tipo_documento',
        'nit_eps',
        'activo',
        'licencia_id',
    ];

    protected $hidden = [
        'contrasena',
    ];

    // Un usuario pertenece a un tipo de usuario (rol)
    public function tipoUsuario()
    {
        return $this->belongsTo(TipoUsuario::class, 'tipo_usuario_id');
    }

    // Un usuario pertenece a una ciudad
    public function ciudad()
    {
        return $this->belongsTo(Ciudad::class, 'id_ciudad');
    }

    // Un usuario pertenece a un tipo de documento
    public function tipoDocumento()
    {
        return $this->belongsTo(TipoDocumento::class, 'id_tipo_documento');
    }

    // Licencia (plan comprado) del usuario
    public function licencia()
    {
        return $this->belongsTo(Licencia::class, 'licencia_id');
    }
}