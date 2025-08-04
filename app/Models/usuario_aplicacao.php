<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class usuario_aplicacao extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'tb_usuario_aplicacao';

    protected $fillable = [
        'id',
        'id_aplicacao',
        'usuario',
    ];

}
