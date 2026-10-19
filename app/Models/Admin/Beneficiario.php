<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Beneficiario extends Model
{
    protected $table = 'beneficiarios';

    // PK = cédula del afiliado, sin autoincrementar
    protected $primaryKey = 'cc_afiliado';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'cc_afiliado', 'tipo_persona', 'cc_cotizante', 'parentesco', 'nit_eps',
        'nombres', 'apellidos', 'edad',
        'telefono', 'direccion', 'correo', 'fecha_nacimiento',

        // Campos adicionales del paciente
        'id_tipo_documento', 'pais_expedicion',
        'genero', 'estado_civil', 'grupo_etnico',
        'tiene_discapacidad', 'tipo_discapacidad',
        'nacionalidad', 'pais_nacimiento',
        'zona_residencia', 'telefono_fijo',
        'estrato', 'nivel_educativo', 'ocupacion',
        'tipo_sangre', 'victima_conflicto_armado',
        'numero_afiliacion_anterior',
    ];

    // Si es beneficiario (familiar): el cotizante del que depende
    public function cotizante()
    {
        return $this->belongsTo(self::class, 'cc_cotizante', 'cc_afiliado');
    }

    // EPS de la persona (el beneficiario hereda la de su cotizante)
    public function eps()
    {
        return $this->belongsTo(Eps::class, 'nit_eps', 'nit_eps');
    }

    public function esCotizante(): bool
    {
        return $this->tipo_persona === 'cotizante';
    }

    public function usuario()
    {
        return $this->belongsTo(\App\Models\Usuario::class, 'cc_afiliado', 'cedula');
    }
}