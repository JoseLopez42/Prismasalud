<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Afiliacion extends Model
{
    protected $table = 'afiliacion';
    protected $primaryKey = 'id_afiliacion';

    protected $fillable = [
        'fecha_afiliacion', 'id_parentesco', 'id_tipo_afiliacion',
        'nit_eps', 'nit_empresa', 'nombre_empresa',
        'cc_usuario', 'id_estado', 'cc_afiliado',
    ];

    public function beneficiario()
    {
        return $this->belongsTo(Beneficiario::class, 'cc_afiliado', 'cc_afiliado');
    }

    public function eps()
    {
        return $this->belongsTo(Eps::class, 'nit_eps', 'nit_eps');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'cc_usuario', 'cedula');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'id_estado', 'id_estado');
    }

    public function tipoAfiliacion()
    {
        return $this->belongsTo(TipoAfiliacion::class, 'id_tipo_afiliacion', 'id_regimen');
    }

    public function parentesco()
    {
        return $this->belongsTo(Parentesco::class, 'id_parentesco', 'id_parentesco');
    }
}
