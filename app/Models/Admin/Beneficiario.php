<?php

namespace App\Models\Admin;

use App\Casts\BooleanoCifrado;
use Illuminate\Database\Eloquent\Model;

class Beneficiario extends Model
{
    protected $table = 'beneficiarios';

    // PK = cédula del afiliado, sin autoincrementar
    protected $primaryKey = 'cc_afiliado';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'cc_afiliado', 'tipo_persona', 'cc_cotizante', 'id_parentesco', 'nit_eps',
        'nombres', 'apellidos', 'edad',
        'telefono', 'direccion', 'correo', 'fecha_nacimiento',

        'id_tipo_documento', 'id_pais_expedicion',
        'id_genero', 'id_estado_civil', 'grupo_etnico',
        'tiene_discapacidad', 'tipo_discapacidad',
        'id_pais_nacionalidad', 'id_pais_nacimiento',
        'id_zona_residencia', 'telefono_fijo',
        'estrato', 'id_nivel_educativo', 'id_ocupacion', 'ocupacion_otra',
        'tipo_sangre', 'victima_conflicto_armado',
        'numero_afiliacion_anterior',
    ];

    /*
    | DBA-03 (parte 3): datos sensibles cifrados (Ley 1581 de 2012).
    | Laravel los cifra al guardar y los descifra al leer, con la llave APP_KEY.
    | En la base se ven ilegibles; en la app se ven normales.
    |
    | IMPORTANTE: si se pierde o cambia APP_KEY, estos datos NO se pueden recuperar.
    | Y como están cifrados, no se puede buscar ni ordenar por ellos en SQL.
    */
    protected $casts = [
        'grupo_etnico'             => 'encrypted',
        'tipo_sangre'              => 'encrypted',
        'tipo_discapacidad'        => 'encrypted',
        'telefono'                 => 'encrypted',
        'telefono_fijo'            => 'encrypted',
        'direccion'                => 'encrypted',
        'tiene_discapacidad'       => BooleanoCifrado::class,
        'victima_conflicto_armado' => BooleanoCifrado::class,
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

    public function parentesco()
    {
        return $this->belongsTo(Parentesco::class, 'id_parentesco', 'id_parentesco');
    }

    public function genero()
    {
        return $this->belongsTo(Genero::class, 'id_genero', 'id_genero');
    }

    public function estadoCivil()
    {
        return $this->belongsTo(EstadoCivil::class, 'id_estado_civil', 'id_estado_civil');
    }

    public function nivelEducativo()
    {
        return $this->belongsTo(NivelEducativo::class, 'id_nivel_educativo', 'id_nivel_educativo');
    }

    public function ocupacion()
    {
        return $this->belongsTo(Ocupacion::class, 'id_ocupacion', 'id_ocupacion');
    }

    public function paisNacionalidad()
    {
        return $this->belongsTo(Pais::class, 'id_pais_nacionalidad', 'id_pais');
    }

    public function paisNacimiento()
    {
        return $this->belongsTo(Pais::class, 'id_pais_nacimiento', 'id_pais');
    }

    public function paisExpedicion()
    {
        return $this->belongsTo(Pais::class, 'id_pais_expedicion', 'id_pais');
    }

    public function zonaResidencia()
    {
        return $this->belongsTo(ZonaResidencia::class, 'id_zona_residencia', 'id_zona_residencia');
    }
}