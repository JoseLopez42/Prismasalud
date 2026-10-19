<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoUsuario extends Model
{
    protected $table = 'tipos_usuario';

    protected $fillable = ['nombre', 'activo'];

    /**
     * Roles que el administrador puede asignar al crear usuarios
     * desde el panel. Los demás (Médico, Farmaceuta, etc.) se
     * gestionan en sus propios módulos.
     */
    public const ROLES_ASIGNABLES = ['Administrador', 'Paciente'];

    /**
     * Relación: un tipo de usuario puede tener muchos usuarios.
     */
    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'tipo_usuario_id');
    }

    /**
     * Scope: solo los roles permitidos en el formulario de usuarios.
     */
    public function scopeAsignables($query)
    {
        return $query->whereIn('nombre', self::ROLES_ASIGNABLES);
    }
}