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
        'id_sub_tipo_paciente',
        'id_ciudad',
        'id_tipo_documento',
        'nit_eps',
        'activo',
        'licencia_id',
        'debe_crear_contrasena',
    ];

    protected $hidden = [
        'contrasena',
    ];

    // Un usuario pertenece a un tipo de usuario (rol)
    public function tipoUsuario()
    {
        return $this->belongsTo(TipoUsuario::class, 'tipo_usuario_id');
    }

    // Un paciente puede ser Cotizante o Beneficiario
    public function subTipoPaciente()
    {
        return $this->belongsTo(SubTipoPaciente::class, 'id_sub_tipo_paciente');
    }

    /**
     * Rol completo para mostrarlo en pantalla: "Paciente Cotizante" o
     * "Paciente Beneficiario" en el caso de los pacientes.
     */
    public function getNombreRolAttribute(): string
    {
        $rol = $this->tipoUsuario->nombre ?? null;

        if (! $rol) {
            return '—';
        }

        if ($this->subTipoPaciente) {
            return $rol . ' ' . $this->subTipoPaciente->nombre;
        }

        return $rol;
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

    // Plan/licencia comprada por este administrador (la asigna el superadmin
    // al activar la licencia; antes de eso se busca por usuario_cedula).
    public function licencia()
    {
        return $this->belongsTo(Licencia::class, 'licencia_id');
    }
}