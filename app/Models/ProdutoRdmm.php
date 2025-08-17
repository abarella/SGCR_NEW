<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Model para produtos R.D. & M.M.
 * Baseado na estrutura dos arquivos legados cr_pst03.asp e cr_calibracao.asp
 *
 * @author Sistema SGCR
 */
class ProdutoRdmm extends Model
{
    use HasFactory;

    /**
     * A tabela associada ao model
     *
     * @var string
     */
    protected $table = 'produtos_rdmm';

    /**
     * Os atributos que são atribuíveis em massa
     *
     * @var array
     */
    protected $fillable = [
        'prod_cod510',
        'categoria',
        'lote',
        'num_producoes',
        'data_calibracao',
        'partidas',
        'pst_serie',
        'atividade',
        'concentracao',
        'volume',
        'observacoes',
        'usuario_id',
        'data_atualizacao'
    ];

    /**
     * Os atributos que devem ser convertidos para tipos nativos
     *
     * @var array
     */
    protected $casts = [
        'categoria' => 'integer',
        'num_producoes' => 'integer',
        'partidas' => 'integer',
        'data_calibracao' => 'datetime',
        'data_atualizacao' => 'datetime',
        'atividade' => 'decimal:2',
        'concentracao' => 'decimal:4',
        'volume' => 'decimal:2'
    ];

    /**
     * Os atributos que devem ser ocultos para arrays
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    /**
     * Relacionamento com usuário
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Escopo para filtrar por categoria
     */
    public function scopePorCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    /**
     * Escopo para filtrar por lote
     */
    public function scopePorLote($query, $lote)
    {
        return $query->where('lote', $lote);
    }

    /**
     * Escopo para produtos com calibração
     */
    public function scopeComCalibracao($query)
    {
        return $query->whereNotNull('data_calibracao');
    }

    /**
     * Escopo para produtos sem calibração
     */
    public function scopeSemCalibracao($query)
    {
        return $query->whereNull('data_calibracao');
    }

    /**
     * Acessor para nome da categoria
     */
    public function getCategoriaNomeAttribute()
    {
        $categorias = [
            1 => 'Radioisotopos Primarios',
            3 => 'Moléculas Marcadas'
        ];

        return $categorias[$this->categoria] ?? 'Desconhecida';
    }

    /**
     * Acessor para status de calibração
     */
    public function getStatusCalibracaoAttribute()
    {
        return $this->data_calibracao ? 'Calibrado' : 'Não Calibrado';
    }

    /**
     * Mutator para categoria
     */
    public function setCategoriaAttribute($value)
    {
        $this->attributes['categoria'] = (int) $value;
    }

    /**
     * Mutator para número de produções
     */
    public function setNumProducoesAttribute($value)
    {
        $this->attributes['num_producoes'] = max(0, (int) $value);
    }

    /**
     * Boot do model
     */
    protected static function boot()
    {
        parent::boot();

        static::updating(function ($produto) {
            $produto->data_atualizacao = now();
        });
    }
}
