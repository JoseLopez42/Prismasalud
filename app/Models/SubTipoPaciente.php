<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubTipoPaciente extends Model
{
    protected $table = 'sub_tipo_paciente';
    protected $primaryKey = 'id_sub_tipo_paciente';

    protected $fillable = ['nombre', 'descripcion', 'activo'];

    public function scopeActivo($query)
    {
        return $query->where('activo', 1);
    }

    /**
     * Id de un sub tipo de paciente por su nombre (Cotizante / Beneficiario).
     */
    public static function idPorNombre(string $nombre): ?int
    {
        return static::where('nombre', $nombre)->value('id_sub_tipo_paciente');
    }

    /**
     * Relación: un sub tipo de paciente puede tener muchos usuarios.
     */
    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'id_sub_tipo_paciente');
    }
}
