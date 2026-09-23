<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Regimen extends Model
{
    protected $table = 'regimen';
    protected $primaryKey = 'id_regimen';

    protected $fillable = ['nombre_regimen', 'activo'];
}