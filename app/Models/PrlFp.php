<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrlFp extends Model
{
    protected $table = 'prl_fps';
    
    protected $fillable = [
        'data_producao',
        'produto',
        'lote',
        'quantidade',
        'unidade',
        'status',
        'observacoes',
        'responsavel',
        'data_inicio',
        'data_fim',
        'temperatura',
        'umidade',
        'ph',
        'condicoes_especiais',
        'aprovado_por',
        'data_aprovacao',
        'rejeitado_por',
        'data_rejeicao',
        'motivo_rejeicao'
    ];

    protected $casts = [
        'data_producao' => 'date',
        'data_inicio' => 'datetime',
        'data_fim' => 'datetime',
        'data_aprovacao' => 'datetime',
        'data_rejeicao' => 'datetime',
        'temperatura' => 'decimal:2',
        'umidade' => 'decimal:2',
        'ph' => 'decimal:2'
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
