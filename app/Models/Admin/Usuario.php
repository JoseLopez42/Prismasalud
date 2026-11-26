<?php

namespace App\Models\Admin;

use Carbon\Carbon;
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
        'cedula', 'nombres', 'apellidos', 'telefono', 'correo',
        'foto', 'contrasena', 'tipo_usuario_id', 'id_sub_tipo_paciente',
        'nit_eps', 'activo', 'debe_crear_contrasena',
    ];

    /**
     * Relación con el beneficiario que tiene la misma cédula.
     * En esta base los pacientes están en beneficiarios.cc_afiliado.
     */
    public function beneficiario()
    {
        return $this->hasOne(Beneficiario::class, 'cc_afiliado', 'cedula');
    }

    /**
     * Edad calculada, nunca guardada.
     *
     * usuarios no tiene fecha_nacimiento: se toma la del beneficiario
     * con la misma cédula. Si no hay fecha, devuelve null y la vista
     * muestra una raya (—) en vez de un número inventado.
     */
    public function getEdadAttribute(): ?int
    {
        if (! $this->cedula) {
            return null;
        }

        $beneficiario = $this->beneficiario;

        if (! $beneficiario || ! $beneficiario->fecha_nacimiento) {
            return null;
        }

        return Carbon::parse($beneficiario->fecha_nacimiento)->age;
    }
}
