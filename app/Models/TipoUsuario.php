<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoUsuario extends Model
{
    protected $table = 'tipos_usuario';

    protected $fillable = ['nombre', 'activo'];

    /**
     * Relación: un tipo de usuario puede tener muchos usuarios.
     */
    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'tipo_usuario_id');
    }
}