<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class emissor extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'tb_emissor';

    protected $fillable = [
        'id',
        'descricao',
    ];
}
