<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrlAe extends Model
{
    protected $table = 'prl_aes';
    
    protected $fillable = [
        'data_alteracao',
        'produto',
        'lote',
        'quantidade_atual',
        'quantidade_nova',
        'unidade',
        'tipo_alteracao',
        'motivo',
        'observacoes',
        'responsavel',
        'aprovado_por',
        'data_aprovacao',
        'rejeitado_por',
        'data_rejeicao',
        'motivo_rejeicao',
        'status'
    ];

    protected $casts = [
        'data_alteracao' => 'date',
        'data_aprovacao' => 'datetime',
        'data_rejeicao' => 'datetime',
        'quantidade_atual' => 'decimal:2',
        'quantidade_nova' => 'decimal:2'
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
