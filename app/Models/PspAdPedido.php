<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PspAdPedido extends Model
{
    protected $table = 'psp_ad_pedidos';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'lote', 'serie', 'produto', 'cliente', 'medico', 'data_fracionamento', 'data_calibracao', 'data_validade', 'atividade_total'
    ];
}
