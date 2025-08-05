<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DefinicaoSerie extends Model
{
    protected $table = 'p110';
    protected $primaryKey = 'chve';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'chve',
        'produto',
        'lote',
        'numero',
        'medico',
        'uf',
        'atividade',
        'serie',
        'producao',
        'calibracao',
        'observacao',
        'usuario_alteracao',
        'data_alteracao'
    ];

    protected $casts = [
        'data_alteracao' => 'datetime',
    ];

    /**
     * Scope para filtrar por produto
     */
    public function scopePorProduto($query, $produto)
    {
        return $query->where('produto', $produto);
    }

    /**
     * Scope para filtrar por lote
     */
    public function scopePorLote($query, $lote)
    {
        return $query->where('lote', $lote);
    }

    /**
     * Scope para filtrar por série
     */
    public function scopePorSerie($query, $serie)
    {
        return $query->where('serie', $serie);
    }

    /**
     * Scope para filtrar por atividade
     */
    public function scopePorAtividade($query, $atividade)
    {
        return $query->where('atividade', $atividade);
    }

    /**
     * Relacionamento com usuário que alterou
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_alteracao', 'id');
    }
} 