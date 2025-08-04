<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrfTm extends Model
{
    protected $table = 'trf_tms';
    
    protected $fillable = [
        'data_transferencia',
        'material',
        'quantidade',
        'unidade',
        'origem',
        'destino',
        'responsavel',
        'observacoes',
        'status',
        'concluido_por',
        'data_conclusao',
        'cancelado_por',
        'data_cancelamento',
        'motivo_cancelamento'
    ];

    protected $casts = [
        'data_transferencia' => 'date',
        'data_conclusao' => 'datetime',
        'data_cancelamento' => 'datetime',
        'quantidade' => 'decimal:2'
    ];

    public function responsavel()
    {
        return $this->belongsTo(User::class, 'responsavel')->withDefault();
    }

    public function concluidoPor()
    {
        return $this->belongsTo(User::class, 'concluido_por')->withDefault();
    }

    public function canceladoPor()
    {
        return $this->belongsTo(User::class, 'cancelado_por')->withDefault();
    }
}
