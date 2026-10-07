<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaccion extends Model
{
    protected $table = 'transacciones';

    protected $fillable = [
        'licencia_id', 'usuario_cedula', 'plan_id', 'monto',
        'tipo', 'metodo_pago', 'referencia_pago', 'estado',
    ];

    public function licencia()
    {
        return $this->belongsTo(Licencia::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_cedula', 'cedula');
    }
}