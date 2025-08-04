<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class assinatura extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'tb_assinatura';

    protected $fillable = [
        'id',
        'assinatura',
    ];

}
