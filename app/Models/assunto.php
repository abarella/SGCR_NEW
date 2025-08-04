<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class assunto extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'tb_assunto';
    protected $fillable = [
        'id',
        'assunto',
    ];
}
