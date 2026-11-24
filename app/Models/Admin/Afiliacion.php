<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Afiliacion extends Model
{
    protected $table = 'afiliacion';
    protected $primaryKey = 'id_afiliacion';

    protected $fillable = [
        'fecha_afiliacion', 'parentesco', 'id_tipo_afiliacion',
        'nit_eps', 'nit_empresa', 'nombre_empresa',
        'cc_usuario', 'id_estado', 'cc_afiliado',

        // Identificación
        'id_tipo_documento', 'pais_expedicion',

        // Demográficos
        'genero', 'estado_civil', 'grupo_etnico',
        'tiene_discapacidad', 'tipo_discapacidad',
        'nacionalidad', 'pais_nacimiento',

        // Residencia
        'zona_residencia', 'telefono_fijo',

        // Socioeconómico (sin ingresos ni NIT empleador)
        'estrato', 'nivel_educativo', 'ocupacion',

        // Salud (sin RH, sin alergias, sin embarazo)
        'tipo_sangre', 'victima_conflicto_armado',

        // Afiliación
        'numero_afiliacion_anterior',
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

    public function tipoDocumento()
    {
        return $this->belongsTo(\App\Models\Admin\TipoDocumento::class, 'id_tipo_documento', 'id_tipo_documento');
    }
}
