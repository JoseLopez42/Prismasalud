<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'usuarios';

    // IMPORTANTE: la PK de esta tabla es "cedula", no "id",
    // y NO es autoincremental. Por eso hay que decírselo a Eloquent:
    protected $primaryKey = 'cedula';
    public $incrementing = false;   // Eloquent no debe intentar autoincrementarla
    protected $keyType = 'int';     // el tipo de la PK es entero

    // Campos que se pueden asignar masivamente (create()/fill())
    protected $fillable = [
        'cedula',
        'nombres',
        'apellidos',
        'correo',
        'contrasena',
        'tipo_usuario_id',
    ];

    // La contraseña nunca debe salir cuando el modelo se convierte
    // a array/JSON (por ejemplo si algún día se usa en una API).
    protected $hidden = [
        'contrasena',
    ];

    /**
     * Relación: un usuario pertenece a un tipo de usuario
     * (Administrador, Paciente, Medico, Farmaceuta).
     */
    public function tipoUsuario()
    {
        return $this->belongsTo(TipoUsuario::class, 'tipo_usuario_id');
    }
}