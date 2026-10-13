<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $table = 'empresas';

    protected $fillable = [
        'usuario_cedula', 'licencia_id', 'nit', 'nombre', 'tipo_entidad',
        'representante_legal', 'correo', 'telefono', 'departamento', 'ciudad',
        'direccion', 'plan', 'fecha_compra', 'fecha_expiracion', 'estado',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_cedula', 'cedula');
    }

    public function licencia()
    {
        return $this->belongsTo(Licencia::class, 'licencia_id');
    }
}
