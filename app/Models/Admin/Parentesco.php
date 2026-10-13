<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Parentesco extends Model
{
    protected $table = 'parentescos';
    protected $primaryKey = 'id_parentesco';

    protected $fillable = ['nombre'];
}