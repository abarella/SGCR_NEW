<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PspPcService
{
    /**
     * Lista pastas não concluídas via procedure PPST_LISTA_NAOCONCLUIDOS
     */
    public function listarPastasNaoConcluidas($filtros = [])
    {
        try {
            Log::info('PspPcService: Iniciando busca de pastas não concluídas', $filtros);
            
            $dbh = DB::connection()->getPdo();
            $sql = "SET NOCOUNT ON; EXEC sgcr.crsa.PPST_LISTA_NAOCONCLUIDOS default, 1, default, default";
            
            Log::info('PspPcService: Executando procedure', ['sql' => $sql]);
            
            $stmt = $dbh->query($sql);
            
            // Obter resultados da procedure
            $xmlResult = "";
            $rowCount = 0;
            
            while ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
                $xmlResult .= $row[0];
                $rowCount++;
            }
            
            Log::info('PspPcService: Procedure executada', ['rowCount' => $rowCount, 'xmlLength' => strlen($xmlResult)]);
            
            if (empty($xmlResult)) {
                Log::warning('PspPcService: Procedure retornou vazio');
                return [];
            }
            
            // Processar XML retornado pela procedure
            $xmlResult = trim($xmlResult);
            
            // Verificar se o XML já tem tag raiz
            if (!str_starts_with($xmlResult, "<root>")) {
                $xmlResult = "<root>$xmlResult</root>";
            }
            
            $xml = simplexml_load_string($xmlResult);
            if (!$xml) {
                Log::error('PspPcService: Erro ao processar XML', ['xml' => $xmlResult]);
                throw new \Exception('Erro ao processar XML retornado pela procedure');
            }
            
            // Converter XML para array de objetos
            $pastas = [];
            if (isset($xml->row)) {
                foreach ($xml->row as $row) {
                    try {
                        $pastaObj = (object) [
                            'pst_numero' => (string)($row['pst_numero'] ?? ''),
                            'pst_produto510' => (string)($row['pst_produto510'] ?? ''),
                            'Lote' => (string)($row['Lote'] ?? ''),
                            'pst_ano_lote' => (string)($row['pst_ano_lote'] ?? ''),
                            'pst_ano' => (string)($row['pst_ano'] ?? ''),
                            'pst_registro' => (string)($row['pst_registro'] ?? ''),
                            'pst_previsaoproducao' => (string)($row['pst_previsaoproducao'] ?? ''),
                            'pst_previsaocontrole' => (string)($row['pst_previsaocontrole'] ?? ''),
                            'pst_observacao' => (string)($row['pst_observacao'] ?? ''),
                            'nome_comercial' => (string)($row['nome_comercial'] ?? ''),
                            'pessoaData' => (string)($row['pessoaData'] ?? ''),
                            'pessoaData2' => (string)($row['pessoaData2'] ?? ''),
                            'producao_revisadopor' => (string)($row['producao_revisadopor'] ?? ''),
                            'controle_revisadopor' => (string)($row['controle_revisadopor'] ?? ''),
                            'pst_status' => (string)($row['pst_status'] ?? '')
                        ];
                        $pastas[] = $pastaObj;
                    } catch (\Exception $e) {
                        Log::error('PspPcService: Erro ao processar linha do XML', ['row' => $row->asXML(), 'error' => $e->getMessage()]);
                        continue;
                    }
                }
            }
            
            Log::info('PspPcService: Pastas processadas com sucesso', ['count' => count($pastas)]);
            
            // Aplicar filtros se especificados
            if (!empty($filtros['produto'])) {
                $pastas = array_filter($pastas, function($pasta) use ($filtros) {
                    return trim($pasta->pst_produto510) === trim($filtros['produto']);
                });
            }
            
            if (!empty($filtros['pasta'])) {
                $pastas = array_filter($pastas, function($pasta) use ($filtros) {
                    return stripos((string)$pasta->pst_numero, (string)$filtros['pasta']) !== false;
                });
            }
            
            // Reindexar array após filtros
            $pastas = array_values($pastas);
            
            // Adicionar status calculado
            $pastas = array_map(function ($pasta) {
                $pasta->status = $this->calcularStatus($pasta);
                $pasta->status_producao = $this->calcularStatusProducao($pasta);
                return $pasta;
            }, $pastas);
            
            return $pastas;
            
        } catch (\Exception $e) {
            Log::error('PspPcService: Erro ao listar pastas não concluídas: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Lista produtos únicos das pastas não concluídas
     */
    public function listarProdutos($filtros = [])
    {
        try {
            $pastas = $this->listarPastasNaoConcluidas($filtros);
            
            // Extrair produtos únicos
            $produtosUnicos = [];
            foreach ($pastas as $pasta) {
                $codigo = trim($pasta->pst_produto510);
                if (!empty($codigo) && !isset($produtosUnicos[$codigo])) {
                    $produtosUnicos[$codigo] = (object) [
                        'codigo' => $codigo,
                        'nome_comercial' => $pasta->nome_comercial
                    ];
                }
            }
            
            return array_values($produtosUnicos);
            
        } catch (\Exception $e) {
            Log::error('PspPcService: Erro ao listar produtos: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Calcula o status geral da pasta
     */
    private function calcularStatus($pasta)
    {
        if (empty($pasta->pst_previsaoproducao) && empty($pasta->pst_previsaocontrole)) {
            return 'PENDENTE';
        } elseif (!empty($pasta->pst_previsaoproducao) && empty($pasta->pst_previsaocontrole)) {
            return 'EM PRODUÇÃO';
        } elseif (!empty($pasta->pst_previsaoproducao) && !empty($pasta->pst_previsaocontrole)) {
            return 'EM CONTROLE';
        }
        
        return 'PENDENTE';
    }
    
    /**
     * Calcula o status de produção
     */
    private function calcularStatusProducao($pasta)
    {
        if (empty($pasta->pst_previsaoproducao)) {
            return 'PENDENTE';
        }
        
        return 'CONCLUÍDA';
    }
    
    /**
     * Testa a conexão com o banco e a procedure
     */
    public function testarConexao()
    {
        try {
            Log::info('PspPcService: Testando conexão com banco de dados');
            
            // Testar conexão básica
            $result = DB::select('SELECT 1 as teste');
            Log::info('PspPcService: Conexão básica OK', ['result' => $result]);
            
            // Testar procedure
            $pastas = $this->listarPastasNaoConcluidas();
            
            return [
                'success' => true,
                'message' => 'Conexão OK',
                'pastas_count' => count($pastas),
                'primeira_pasta' => $pastas[0] ?? null
            ];
            
        } catch (\Exception $e) {
            Log::error('PspPcService: Erro ao testar conexão: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro na conexão: ' . $e->getMessage()
            ];
        }
    }
}
