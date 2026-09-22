<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model
{
    protected $table = 'tipo_documento';
    protected $primaryKey = 'id_tipo_documento';
    public $timestamps = false; // esta tabla no tiene created_at/updated_at

    protected $fillable = ['tipo_documento'];
}