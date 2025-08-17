<?php

namespace App\Services;

use PDO;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Service para funcionalidades de R.D. & M.M. (psp-rm)
 * Refatorado a partir dos arquivos cr_pst03.asp e cr_calibracao.asp
 *
 * @author Sistema SGCR
 */
class PspRmService
{
    /**
     * Construtor do service
     */
    public function __construct()
    {
        // Inicializações se necessário
    }

    /**
     * Valida senha do usuário
     *
     * @param string $usuario
     * @param string $senha
     * @return bool
     */
    public function validarSenha($usuario, $senha)
    {
        try {
            $dbh = DB::connection()->getPdo();
            $sql = "SET NOCOUNT ON; exec sgcr.crsa.[P1110_CONFSENHA] @p1110_usuarioid = :p1110_usuarioid, @p1110_senha = :p1110_senha, @resulta = :resulta, @mensa = :mensa";
            $stmt = $dbh->prepare($sql);

            $resulta = "0";
            $mensa = "";

            // Input parameters
            $stmt->bindParam(':p1110_usuarioid', $usuario, \PDO::PARAM_STR);
            $stmt->bindParam(':p1110_senha', $senha, \PDO::PARAM_STR);
            // Output parameters
            $stmt->bindParam(':resulta', $resulta, \PDO::PARAM_STR|\PDO::PARAM_INPUT_OUTPUT, 4000);
            $stmt->bindParam(':mensa', $mensa, \PDO::PARAM_STR|\PDO::PARAM_INPUT_OUTPUT, 4000);

            $stmt->execute();
            $stmt->nextRowset();

            return $resulta == "0";
        } catch (\Exception $e) {
            Log::error('Erro na validação de senha: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Lista produtos por categoria e lote
     *
     * @param int $categoria
     * @param string $lote
     * @return array
     */
    public function listarProdutos($categoria, $lote)
    {
        try {
            $dbh = DB::connection()->getPdo();
            
            // Procedure baseada no arquivo cr_pst03.js - P0250_Produto_RDMM
            $sql = "SET NOCOUNT ON; exec sgcr.crsa.P0250_Produto_RDMM @categoria = :categoria, @lote = :lote";
            $stmt = $dbh->prepare($sql);
            
            $stmt->bindParam(':categoria', $categoria, \PDO::PARAM_INT);
            $stmt->bindParam(':lote', $lote, \PDO::PARAM_STR);
            
            Log::info('Executando procedure P0250_Produto_RDMM:', [
                'categoria' => $categoria,
                'lote' => $lote,
                'sql' => $sql
            ]);
            
            $stmt->execute();
            
            // Verificar se há resultados
            $rowCount = $stmt->rowCount();
            Log::info('Procedure executada:', [
                'rowCount' => $rowCount,
                'columnCount' => $stmt->columnCount()
            ]);
            
            // Obter o resultado como string (XML)
            $xmlResult = '';
            $resultSet = [];
            
            // Tentar diferentes abordagens para obter o resultado
            try {
                // Método 1: fetchAll para ver todos os resultados
                $resultSet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                Log::info('Resultado fetchAll:', [
                    'total_rows' => count($resultSet),
                    'primeira_linha' => $resultSet[0] ?? 'vazio'
                ]);
                
                if (!empty($resultSet)) {
                    // Pegar o primeiro campo da primeira linha
                    $firstRow = $resultSet[0];
                    $firstColumn = array_keys($firstRow)[0] ?? null;
                    
                    if ($firstColumn) {
                        $xmlResult = $firstRow[$firstColumn];
                        Log::info('XML extraído:', [
                            'coluna' => $firstColumn,
                            'valor' => $xmlResult
                        ]);
                    }
                }
                
            } catch (\Exception $fetchError) {
                Log::warning('Erro no fetchAll, tentando fetch individual:', [
                    'erro' => $fetchError->getMessage()
                ]);
                
                // Método 2: fetch individual
                try {
                    $stmt->execute(); // Re-executar para resetar o cursor
                    while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                        $resultSet[] = $row;
                        if (empty($xmlResult)) {
                            $xmlResult = $row[array_keys($row)[0]] ?? '';
                        }
                    }
                } catch (\Exception $fetch2Error) {
                    Log::error('Erro no fetch individual:', [
                        'erro' => $fetch2Error->getMessage()
                    ]);
                }
            }
            
            if (empty($xmlResult)) {
                Log::warning('Nenhum resultado XML encontrado da procedure:', [
                    'categoria' => $categoria,
                    'lote' => $lote,
                    'resultSet' => $resultSet
                ]);
                return [];
            }
            
            // Converter XML para array
            $produtos = $this->parseXmlToList($xmlResult);
            
            // Adicionar campos adicionais e tratar datas
            foreach ($produtos as $produto) {
                $produto->lote = $lote;
                $produto->categoria = intval($categoria);
                
                // Tratar campo de calibração para formato dd/MM/YYYY
                if (isset($produto->p100dtcl)) {
                    $produto->p100dtcl = $this->formatarDataCalibracao($produto->p100dtcl);
                }
            }
            
            Log::info('Produtos processados com sucesso:', [
                'total_produtos' => count($produtos)
            ]);
            
            return $produtos;

        } catch (\PDOException $e) {
            Log::error('Erro PDO ao listar produtos RDMM: ' . $e->getMessage());
            throw new \Exception('Erro de banco de dados ao listar produtos: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Erro geral ao listar produtos RDMM: ' . $e->getMessage());
            throw new \Exception('Erro interno ao listar produtos: ' . $e->getMessage());
        }
    }

    /**
     * Atualiza número de produções
     *
     * @param string $produto
     * @param string $lote
     * @param int $categoria
     * @param int $numProducoes
     * @return bool
     */
    public function atualizarProducoes($produto, $lote, $categoria, $numProducoes)
    {
        try {
            $dbh = DB::connection()->getPdo();
            
            // Procedure baseada no arquivo cr_pst03.js - PPST_RR_MM
            $sql = "SET NOCOUNT ON; exec sgcr.crsa.PPST_RR_MM @produto = :produto, @lote = :lote, @produto_qtde = :produto_qtde, @cdusuario = :cdusuario, @senha = :senha";
            $stmt = $dbh->prepare($sql);
            
            $stmt->bindParam(':produto', $produto, \PDO::PARAM_STR);
            $stmt->bindParam(':lote', $lote, \PDO::PARAM_STR);
            $stmt->bindParam(':produto_qtde', $numProducoes, \PDO::PARAM_INT);
            $stmt->bindParam(':cdusuario', auth()->user()->cdusuario ?? '', \PDO::PARAM_STR);
            $stmt->bindParam(':senha', '', \PDO::PARAM_STR); // Senha já foi validada no controller
            
            $stmt->execute();
            
            return true;

        } catch (\Exception $e) {
            Log::error('Erro ao atualizar produções: ' . $e->getMessage());
            throw new \Exception('Erro ao atualizar produções: ' . $e->getMessage());
        }
    }

    /**
     * Obtém dados de calibração
     *
     * @param string $produto
     * @param string $lote
     * @param int $categoria
     * @return array
     */
    public function obterDadosCalibracao($produto, $lote, $categoria)
    {
        try {
            $dbh = DB::connection()->getPdo();
            
            // Procedure baseada no arquivo cr_calibracao.js - PPST_LISTA7A
            $sql = "SET NOCOUNT ON; exec sgcr.crsa.PPST_LISTA7A @produto = :produto, @lote = :lote";
            $stmt = $dbh->prepare($sql);
            
            $stmt->bindParam(':produto', $produto, \PDO::PARAM_STR);
            $stmt->bindParam(':lote', $lote, \PDO::PARAM_STR);
            
            $stmt->execute();
            
            // Obter o resultado como string (XML)
            $xmlResult = '';
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                // A procedure retorna XML, então pegamos o primeiro campo
                $xmlResult = $row[array_keys($row)[0]] ?? '';
                break; // Só precisamos da primeira linha
            }
            
            if (empty($xmlResult)) {
                return [];
            }
            
            // Converter XML para array
            $dados = $this->parseXmlToList($xmlResult);
            
            // Tratar campos de data de calibração
            foreach ($dados as $calibracao) {
                if (isset($calibracao->pst_calibracao)) {
                    $calibracao->pst_calibracao = $this->formatarDataCalibracao($calibracao->pst_calibracao);
                }
            }
            
            return $dados;

        } catch (\PDOException $e) {
            Log::error('Erro PDO ao obter dados de calibração: ' . $e->getMessage());
            throw new \Exception('Erro de banco de dados ao obter dados de calibração: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Erro geral ao obter dados de calibração: ' . $e->getMessage());
            throw new \Exception('Erro interno ao obter dados de calibração: ' . $e->getMessage());
        }
    }

    /**
     * Atualiza dados de calibração
     *
     * @param string $produto
     * @param string $lote
     * @param int $categoria
     * @param array $dadosCalibracao
     * @return bool
     */
    public function atualizarCalibracao($produto, $lote, $categoria, $dadosCalibracao)
    {
        try {
            $dbh = DB::connection()->getPdo();
            
            // Procedure para atualizar calibração (baseada no arquivo legado)
            // Nota: Esta procedure precisa ser implementada no SQL Server
            $sql = "SET NOCOUNT ON; exec sgcr.crsa.PPST_AtualizarCalibracao @produto = :produto, @lote = :lote, @pst_serie = :pst_serie, @pst_calibracao = :pst_calibracao, @pst_producao = :pst_producao, @pst_observacao = :pst_observacao, @cdusuario = :cdusuario";
            $stmt = $dbh->prepare($sql);
            
            $stmt->bindParam(':produto', $produto, \PDO::PARAM_STR);
            $stmt->bindParam(':lote', $lote, \PDO::PARAM_STR);
            $stmt->bindParam(':pst_serie', $dadosCalibracao['pst_serie'] ?? '', \PDO::PARAM_STR);
            $stmt->bindParam(':pst_calibracao', $dadosCalibracao['pst_calibracao'] ?? '', \PDO::PARAM_STR);
            $stmt->bindParam(':pst_producao', $dadosCalibracao['pst_producao'] ?? '', \PDO::PARAM_STR);
            $stmt->bindParam(':pst_observacao', $dadosCalibracao['pst_observacao'] ?? '', \PDO::PARAM_STR);
            $stmt->bindParam(':cdusuario', auth()->user()->cdusuario ?? '', \PDO::PARAM_STR);
            
            $stmt->execute();
            
            return true;

        } catch (\Exception $e) {
            Log::error('Erro ao atualizar calibração: ' . $e->getMessage());
            throw new \Exception('Erro ao atualizar calibração: ' . $e->getMessage());
        }
    }

    /**
     * Formata data de calibração para dd/MM/YYYY
     *
     * @param string $data
     * @return string
     */
    private function formatarDataCalibracao($data)
    {
        if (empty($data)) {
            return '';
        }
        
        try {
            // Se já está no formato dd/MM/YYYY, retorna como está
            if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $data)) {
                return $data;
            }
            
            // Se está no formato dd-MM-YYYY, converte para dd/MM/YYYY
            if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $data)) {
                return str_replace('-', '/', $data);
            }
            
            // Se está no formato YYYY-MM-DD, converte para dd/MM/YYYY
            if (preg_match('/^\d{4}-\d{2}-\d{2}/', $data)) {
                $date = \DateTime::createFromFormat('Y-m-d', substr($data, 0, 10));
                if ($date) {
                    return $date->format('d/m/Y');
                }
            }
            
            // Se está no formato YYYY-MM-DD HH:MM:SS, converte para dd/MM/YYYY
            if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $data)) {
                $date = \DateTime::createFromFormat('Y-m-d H:i:s', substr($data, 0, 19));
                if ($date) {
                    return $date->format('d/m/Y');
                }
            }
            
            // Se não conseguir converter, retorna o valor original
            return $data;
            
        } catch (\Exception $e) {
            Log::warning('Erro ao formatar data de calibração:', [
                'data_original' => $data,
                'erro' => $e->getMessage()
            ]);
            return $data;
        }
    }

    /**
     * Converte XML para array de objetos
     */
    private function parseXmlToList($xmlString)
    {
        try {
            // Remove caracteres especiais do início se existirem
            $xmlString = trim($xmlString);

            // Verifica se o XML é válido
            if (empty($xmlString)) {
                Log::warning('XML vazio:', ['xml' => $xmlString]);
                return [];
            }

            // Log do XML original para debug
            Log::info('XML recebido da procedure:', [
                'xml_original' => $xmlString,
                'tamanho' => strlen($xmlString)
            ]);

            // Corrige caracteres especiais que podem estar causando problemas
            $xmlString = str_replace('&lt;', '<', $xmlString);
            $xmlString = str_replace('&gt;', '>', $xmlString);
            $xmlString = str_replace('&amp;', '&', $xmlString);
            $xmlString = str_replace('&quot;', '"', $xmlString);

            // Se não tem declaração XML, adiciona uma
            if (!preg_match('/^<\?xml/', $xmlString)) {
                // Sempre adiciona a declaração XML e envolve em <root>
                $xmlString = '<?xml version="1.0" encoding="UTF-8"?><root>' . $xmlString . '</root>';
                Log::info('Adicionada declaração XML:', ['xml_modificado' => $xmlString]);
            }

            // Carrega o XML
            $xml = simplexml_load_string($xmlString);

            if ($xml === false) {
                $errors = libxml_get_errors();
                Log::error('Erro ao carregar XML:', [
                    'xml' => $xmlString,
                    'errors' => $errors
                ]);
                libxml_clear_errors();
                return [];
            }

            Log::info('XML carregado com sucesso:', [
                'xml_objeto' => $xml ? 'carregado' : 'falhou',
                'elementos_children' => count($xml->children()),
                'elementos_row' => isset($xml->row) ? count($xml->row) : 0
            ]);

            $result = [];

            // Procura por elementos 'row' no XML
            if (isset($xml->row)) {
                foreach ($xml->row as $row) {
                    $item = new \stdClass();
                    foreach ($row->attributes() as $key => $value) {
                        $item->$key = (string)$value;
                    }
                    $result[] = $item;
                }
            }

            Log::info('XML convertido com sucesso:', [
                'total_registros' => count($result),
                'primeiro_registro' => $result ? get_object_vars($result[0]) : []
            ]);

            return $result;

        } catch (\Exception $e) {
            Log::error('Erro ao processar XML:', [
                'xml' => $xmlString,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Testa a conexão com a procedure (método alternativo)
     *
     * @param int $categoria
     * @param string $lote
     * @return array
     */
    public function testarProcedure($categoria, $lote)
    {
        try {
            $dbh = DB::connection()->getPdo();
            
            // Teste simples para ver se a procedure existe e executa
            $sql = "SET NOCOUNT ON; exec sgcr.crsa.P0250_Produto_RDMM @categoria = :categoria, @lote = :lote";
            $stmt = $dbh->prepare($sql);
            
            $stmt->bindParam(':categoria', $categoria, \PDO::PARAM_INT);
            $stmt->bindParam(':lote', $lote, \PDO::PARAM_STR);
            
            Log::info('Testando procedure P0250_Produto_RDMM:', [
                'categoria' => $categoria,
                'lote' => $lote
            ]);
            
            $stmt->execute();
            
            // Tentar obter informações sobre o resultado
            $columnCount = $stmt->columnCount();
            $rowCount = $stmt->rowCount();
            
            Log::info('Informações da procedure:', [
                'columnCount' => $columnCount,
                'rowCount' => $rowCount
            ]);
            
            // Tentar obter o resultado de forma mais direta
            $result = [];
            try {
                $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                Log::info('Resultado do teste:', [
                    'total_rows' => count($result),
                    'estrutura' => $result ? array_keys($result[0]) : []
                ]);
            } catch (\Exception $e) {
                Log::warning('Erro ao fazer fetch:', ['erro' => $e->getMessage()]);
            }
            
            return [
                'success' => true,
                'columnCount' => $columnCount,
                'rowCount' => $rowCount,
                'result' => $result
            ];
            
        } catch (\Exception $e) {
            Log::error('Erro ao testar procedure: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtém categorias disponíveis
     *
     * @return array
     */
    public function obterCategorias()
    {
        return [
            ['id' => 1, 'nome' => 'Radioisotopos Primarios'],
            ['id' => 3, 'nome' => 'Moléculas Marcadas']
        ];
    }
}
