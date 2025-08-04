<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fpe extends Model
{
    protected $table = 'fpes';
    
    protected $fillable = [
        'data_embalagem',
        'produto',
        'lote',
        'quantidade',
        'unidade',
        'status',
        'observacoes',
        'responsavel',
        'aprovado_por',
        'data_aprovacao',
        'rejeitado_por',
        'data_rejeicao',
        'motivo_rejeicao'
    ];

    protected $casts = [
        'data_embalagem' => 'date',
        'data_aprovacao' => 'datetime',
        'data_rejeicao' => 'datetime',
        'quantidade' => 'decimal:2'
    ];

    public function responsavel()
    {
        return $this->belongsTo(User::class, 'responsavel')->withDefault();
    }

    public function aprovadoPor()
    {
        return $this->belongsTo(User::class, 'aprovado_por')->withDefault();
    }

    public function rejeitadoPor()
    {
        return $this->belongsTo(User::class, 'rejeitado_por')->withDefault();
    }
}
