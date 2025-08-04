<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class aplicacao extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'tb_aplicacao';

    protected $fillable = [
        'id',
        'aplicacao',
    ];
}
