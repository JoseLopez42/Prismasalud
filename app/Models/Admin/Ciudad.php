<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Ciudad extends Model
{
    protected $table = 'ciudades';
    protected $primaryKey = 'id_ciudad';

    protected $fillable = ['ciudad', 'id_departamento'];

    // Una ciudad pertenece a un departamento
    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'id_departamento');
    }
}