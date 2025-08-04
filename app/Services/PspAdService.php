<?php
namespace App\Services;


use Illuminate\Support\Facades\DB;


class PspAdService
{
    /**
     * Executa a procedure de listagem de pedidos PSP-AD com os filtros informados.
     *
     * @param string|null $lote
     * @param string|null $serie
     * @param string|null $produto
     * @param int $ordem
     * @param int $pagina
     * @return array
     */
    public static function listarPedidos($lote = null, $serie = null, $produto = null, $ordem = 0, $pagina = 1)
    {
        $dbh = DB::connection()->getPdo();
        $sql = "set nocount on; exec vendasPelicano.dbo.P0110_PEDIDO_DATA_LISTA @p110prod = :produto, @p110lote = :lote, @p110serie = :serie, @ordem = :ordem";
        $sth = $dbh->prepare($sql);

        //dd($produto, $lote, $serie, $ordem);    
        
        $sth->bindValue(':produto', $produto ?? '');
        $sth->bindValue(':lote', $lote ?? '');
        $sth->bindValue(':serie', $serie ?? '');
        $sth->bindValue(':ordem', $ordem ?? 0);
        $sth->execute();

        $xmlRows = [];
        while ($row = $sth->fetch(\PDO::FETCH_NUM)) {
            $xmlRows[] = trim($row[0]);
        }

        $sth = null; // fecha o cursor explicitamente
        if (count($xmlRows)) {
            $xml = '<root>' . implode('', $xmlRows) . '</root>';
            $xml = preg_replace('/"([a-zA-Z0-9_\-]+)=/', '" $1=', $xml);
            $xmlCorrigido = preg_replace('/&(?!amp;|lt;|gt;|quot;|apos;)/', '&amp;', $xml);
            $result = [];
            $xmlObj = @simplexml_load_string($xmlCorrigido);
       

            if ($xmlObj) {
                foreach ($xmlObj->row as $item) {
             
                    $result[] = (object) [
                        'id' => (string)($item['p110id'] ?? ''),
                        'nr_pedido' => (string)($item['p110id'] ?? ''),
                        'lote' => (string)($item['P110CHVE'] ?? ''),
                        'cliente' => (string)($item['cliente'] ?? ''),
                        'medico' => (string)($item['cli_dest_responsavel'] ?? ''),
                        'data_fracionamento' => (string)($item['p110dtfab'] ?? ''),
                        'data_calibracao' => (string)($item['p110dtcl'] ?? ''),
                        'data_validade' => (string)($item['p110dtvl'] ?? ''),
                        'atividade_total' => (string)($item['p110atv'] ?? ''),
                    ];
                }
            }
            return $result;
        }
        return [];
    }

    /**
     * Executa a procedure de atualização em bloco dos pedidos selecionados.
     *
     * @param array $ids
     * @param string $dataFracionamento
     * @param string $dataCalibracao
     * @param int|string $usuario
     * @return void
     */
    public static function atualizarPedidos(array $ids, $dataFracionamento, $dataCalibracao, $usuario)
    {
        // Converter datas do formato HTML5 datetime-local (YYYY-MM-DDTHH:MM) para SQL Server (YYYY-MM-DD HH:MM:SS)
        $formatInput = 'Y-m-d\TH:i';
        $formatOutput = 'Ymd H:i:s';
        $dataFracionamentoSql = '';
        $dataCalibracaoSql = '';
        if (!empty($dataFracionamento)) {
            $dt = \DateTime::createFromFormat($formatInput, substr($dataFracionamento, 0, 16));
            $dataFracionamentoSql = $dt ? $dt->format($formatOutput) : $dataFracionamento;
        }
        if (!empty($dataCalibracao)) {
            $dt = \DateTime::createFromFormat($formatInput, substr($dataCalibracao, 0, 16));
            $dataCalibracaoSql = $dt ? $dt->format($formatOutput) : $dataCalibracao;
        }

        

        $dbh = DB::connection()->getPdo();
        $resultados = [];
        foreach ($ids as $id) {
            // Adiciona parâmetros de saída @resulta e @mensa
            $sql = "DECLARE @resulta INT, @mensa NVARCHAR(255);\n"
                 . "set nocount on; EXEC vendasPelicano.dbo.P0110_PEDIDO_ALTERA_PRODUCAO "
                 . "@p110id = :id, "
                 . "@Data_Fracionamento = :dataFracionamento, "
                 . "@Data_Calibracao = :dataCalibracao, "
                 . "@cdusuario = :usuario, "
                 . "@resulta = @resulta OUTPUT, "
                 . "@mensa = @mensa OUTPUT;\n"
                 . "SELECT @resulta as resulta, @mensa as mensa;";
            $sth = $dbh->prepare($sql);
            $sth->bindValue(':id', $id);
            $sth->bindValue(':dataFracionamento', $dataFracionamentoSql);
            $sth->bindValue(':dataCalibracao', $dataCalibracaoSql);
            $sth->bindValue(':usuario', $usuario);
            $sth->execute();
            $saida = $sth->fetch(\PDO::FETCH_ASSOC);
            $resultados[] = [
                'id' => $id,
                'resulta' => $saida['resulta'] ?? null,
                'mensa' => $saida['mensa'] ?? null
            ];
        }
        return $resultados;
    }
}
