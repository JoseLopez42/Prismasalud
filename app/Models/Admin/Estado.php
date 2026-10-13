<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    protected $table = 'estado';
    protected $primaryKey = 'id_estado';

    protected $fillable = ['estado'];
}
