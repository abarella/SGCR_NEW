<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class bxp extends Model
{
    // Defina a tabela se não seguir o padrão plural
    protected $table = 'bxp';
    // Defina os campos fillable se necessário
    protected $guarded = [];
    public $timestamps = false;
} 