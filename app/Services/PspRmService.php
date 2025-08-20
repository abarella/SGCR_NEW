<?php

namespace App\Services;

use PDO;
use Illuminate\Support\Facades\DB;

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
            
            $stmt->execute();
            
            // Verificar se há resultados
            $rowCount = $stmt->rowCount();
            
            // Obter o resultado como string (XML)
            $xmlResult = '';
            $resultSet = [];
            

            
            // Tentar diferentes abordagens para obter o resultado
            try {
                // Método 1: fetchAll para ver todos os resultados
                $resultSet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                

                
                if (!empty($resultSet)) {
                    // Pegar o primeiro campo da primeira linha
                    $firstRow = $resultSet[0];
                    $firstColumn = array_keys($firstRow)[0] ?? null;
                    
                    if ($firstColumn) {
                        $xmlResult = $firstRow[$firstColumn];

                    }
                }
                
            } catch (\Exception $fetchError) {
                \Log::error('Erro no fetchAll em listarProdutos', [
                    'error' => $fetchError->getMessage()
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
                    \Log::error('Erro no fetch individual em listarProdutos', [
                        'error' => $fetch2Error->getMessage()
                    ]);
                }
            }
            
            if (empty($xmlResult)) {
                \Log::warning('XML vazio retornado pela procedure P0250_Produto_RDMM');
                return [];
            }
            

            
            // Converter XML para array
            $produtos = $this->parseXmlToList($xmlResult);
            

            
            // Adicionar campos adicionais e tratar datas
            foreach ($produtos as $produto) {
                $produto->lote = $lote;
                $produto->categoria = intval($categoria);
                
                // Tratar campo de calibração para formato dd/MM/YYYY
                if (isset($produto->p100dtcl) && !empty($produto->p100dtcl)) {
                    try {
                        $produto->p100dtcl = $this->formatarDataCalibracao($produto->p100dtcl);
                    } catch (\Exception $e) {
                        $produto->p100dtcl = ''; // Definir como vazio se der erro
                    }
                } else {
                    $produto->p100dtcl = ''; // Garantir que seja string vazia se não existir
                }
            }
            
            return $produtos;

        } catch (\PDOException $e) {
            \Log::error('Erro PDO ao listar produtos', [
                'error' => $e->getMessage(),
                'categoria' => $categoria,
                'lote' => $lote
            ]);
            throw new \Exception('Erro de banco de dados ao listar produtos: ' . $e->getMessage());
        } catch (\Exception $e) {
            \Log::error('Erro interno ao listar produtos', [
                'error' => $e->getMessage(),
                'categoria' => $categoria,
                'lote' => $lote
            ]);
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
    public function atualizarProducoes($produto, $lote, $categoria, $numProducoes, $senhaDigitada = '')
    {
        try {
            $dbh = DB::connection()->getPdo();
            
            // Obter usuário autenticado
            $cdusuario = \Illuminate\Support\Facades\Auth::user()->cdusuario ?? 0;
            
            // Validar parâmetros
            if (empty($produto) || empty($lote) || empty($numProducoes)) {
                throw new \Exception('Parâmetros obrigatórios não informados');
            }
            
            // Truncar produto para char(10) se necessário
            $produto = substr($produto, 0, 10);
            
            // Truncar senha para char(6) se necessário
            $senha = substr($senhaDigitada, 0, 6);
            
            // Procedure PPST_RR_MM usando variáveis PHP para capturar OUTPUT
            $sql = "EXEC sgcr.crsa.PPST_RR_MM ?, ?, ?, ?, ?, ?, ?";
            
            $stmt = $dbh->prepare($sql);
            
            // Variáveis para capturar parâmetros OUTPUT
            $resulta = 0;
            $mensa = '';
            
            // Binding dos parâmetros incluindo OUTPUT
            $stmt->bindParam(1, $produto, \PDO::PARAM_STR);
            $stmt->bindParam(2, $lote, \PDO::PARAM_INT);
            $stmt->bindParam(3, $numProducoes, \PDO::PARAM_INT);
            $stmt->bindParam(4, $cdusuario, \PDO::PARAM_INT);
            $stmt->bindParam(5, $senha, \PDO::PARAM_STR);
            $stmt->bindParam(6, $resulta, \PDO::PARAM_INT|\PDO::PARAM_INPUT_OUTPUT, 4);
            $stmt->bindParam(7, $mensa, \PDO::PARAM_STR|\PDO::PARAM_INPUT_OUTPUT, 50);
            
            $stmt->execute();
            

            
            // Verificar resultado da procedure
            if ($resulta != 0) {
                throw new \Exception('Erro na procedure: ' . $mensa);
            }
            
            // Se não há mensagem da procedure, usar mensagem padrão
            $mensagemFinal = !empty(trim($mensa)) ? $mensa : 'Produções atualizadas com sucesso';
            
            return [
                'success' => true,
                'message' => $mensagemFinal
            ];
            
        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar produções: ' . $e->getMessage(), [
                'produto' => $produto,
                'lote' => $lote,
                'categoria' => $categoria,
                'numProducoes' => $numProducoes,
                'cdusuario' => $cdusuario ?? 'N/A',
                'sql' => $sql ?? 'N/A',
                'resulta' => $resulta ?? 'N/A',
                'mensa' => $mensa ?? 'N/A'
            ]);
            
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
            
            // Procedure PPST_LISTA7A para obter séries autorizadas
            $sql = "EXEC sgcr.crsa.PPST_LISTA7A ?, ?";
            $stmt = $dbh->prepare($sql);
            
            $stmt->bindParam(1, $produto, \PDO::PARAM_STR);
            $stmt->bindParam(2, $lote, \PDO::PARAM_INT);
            
            $stmt->execute();
            
            // Obter o resultado como string (XML)
            $xmlResult = '';
            $resultSet = [];
            

            
            // Tentar diferentes abordagens para obter o resultado XML
            try {
                // Método 1: fetchAll para ver todos os resultados
                $resultSet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                

                
                if (!empty($resultSet)) {
                    // Tentar concatenar todas as linhas se houver múltiplas
                    $xmlResult = '';
                    foreach ($resultSet as $row) {
                        $firstColumn = array_keys($row)[0] ?? null;
                        if ($firstColumn && !empty($row[$firstColumn])) {
                            $xmlResult .= $row[$firstColumn];
                        }
                    }
                    

                }
                
            } catch (\Exception $fetchError) {
                \Log::error('Erro no fetchAll', [
                    'error' => $fetchError->getMessage()
                ]);
                
                // Método 2: fetch individual
                try {
                    $stmt->execute(); // Re-executar para resetar o cursor
                    $xmlResult = '';
                    while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                        $resultSet[] = $row;
                        $firstColumn = array_keys($row)[0] ?? null;
                        if ($firstColumn && !empty($row[$firstColumn])) {
                            $xmlResult .= $row[$firstColumn];
                        }
                    }
                    

                    
                } catch (\Exception $fetch2Error) {
                    \Log::error('Erro no fetch individual', [
                        'error' => $fetch2Error->getMessage()
                    ]);
                }
            }
            
            if (empty($xmlResult)) {
                \Log::warning('XML vazio retornado pela procedure PPST_LISTA7A');
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
            \Log::error('Erro PDO ao obter dados de calibração', [
                'error' => $e->getMessage(),
                'produto' => $produto,
                'lote' => $lote
            ]);
            throw new \Exception('Erro de banco de dados ao obter dados de calibração: ' . $e->getMessage());
        } catch (\Exception $e) {
            \Log::error('Erro interno ao obter dados de calibração', [
                'error' => $e->getMessage(),
                'produto' => $produto,
                'lote' => $lote
            ]);
            throw new \Exception('Erro interno ao obter dados de calibração: ' . $e->getMessage());
        }
    }

    /**
     * Atualiza dados de calibração usando procedure Ppst_SERIE2_GRAVA
     *
     * @param string $produto
     * @param string $lote
     * @param int $categoria
     * @param array $dadosCalibracao
     * @param string $senha
     * @return bool
     */
    public function atualizarCalibracao($produto, $lote, $categoria, $dadosCalibracao, $senha = '')
    {
        try {
            $dbh = DB::connection()->getPdo();
            
            // Verificar se dadosCalibracao é um array
            if (!is_array($dadosCalibracao)) {
                throw new \Exception('Dados de calibração inválidos');
            }
            
            // Validar senha se fornecida
            if (!empty($senha)) {
                $usuario = \Illuminate\Support\Facades\Auth::user()->cdusuario ?? '';
                if (!$this->validarSenha($usuario, $senha)) {
                    throw new \Exception('Senha inválida');
                }
            }
            
            $cdusuario = \Illuminate\Support\Facades\Auth::user()->cdusuario ?? '';
            $sucesso = true;
            $erros = [];
            
            // Processar cada item de calibração
            foreach ($dadosCalibracao as $calibracao) {
                if (!isset($calibracao['pst_serie']) || !isset($calibracao['pst_calibracao'])) {
                    continue; // Pular itens inválidos
                }
                
                try {
                    // Converter data de calibração para formato SQL Server
                    $pst_calibracao = $this->converterDataParaSQLServer($calibracao['pst_calibracao']);
                    
                    // Procedure correta para atualizar calibração - usando parâmetros nomeados corretos
                    $sql = "SET NOCOUNT ON; exec sgcr.crsa.Ppst_SERIE2_GRAVA @produto = :produto, @lote = :lote, @data_calibracao = :pst_calibracao, @pst_serie = :pst_serie, @pst_producao = :pst_producao, @pst_numero = :pst_numero, @cdusuario = :cdusuario, @senha = :senha, @pst_observacao = :pst_observacao, @resulta = :resulta, @mensa = :mensa";
                    $stmt = $dbh->prepare($sql);
                    
                                         $pst_serie = $calibracao['pst_serie'] ?? '';
                     $pst_producao = $calibracao['pst_producao'] ?? '';
                     $pst_observacao = $calibracao['pst_observacao'] ?? '';
                     $pst_numero = $calibracao['pst_numero'] ?? 0; // Campo pasta
                     $senha_param = $senha; // Usar a senha passada como parâmetro
                    $resulta = 0;
                    $mensa = '';
                    
                    
                    
                    $stmt->bindParam(':produto', $produto, \PDO::PARAM_STR);
                    $stmt->bindParam(':lote', $lote, \PDO::PARAM_STR);
                    $stmt->bindParam(':pst_calibracao', $pst_calibracao, \PDO::PARAM_STR);
                    $stmt->bindParam(':pst_serie', $pst_serie, \PDO::PARAM_STR);
                    $stmt->bindParam(':pst_producao', $pst_producao, \PDO::PARAM_STR);
                    $stmt->bindParam(':pst_numero', $pst_numero, \PDO::PARAM_INT);
                    $stmt->bindParam(':cdusuario', $cdusuario, \PDO::PARAM_STR);
                    $stmt->bindParam(':senha', $senha_param, \PDO::PARAM_STR);
                    $stmt->bindParam(':pst_observacao', $pst_observacao, \PDO::PARAM_STR);
                    $stmt->bindParam(':resulta', $resulta, \PDO::PARAM_INT|\PDO::PARAM_INPUT_OUTPUT, 4);
                    $stmt->bindParam(':mensa', $mensa, \PDO::PARAM_STR|\PDO::PARAM_INPUT_OUTPUT, 4000);
                    
                    $stmt->execute();
                    

                    
                    if ($resulta != 0) {
                        $erros[] = "Série {$pst_serie}: " . $mensa;
                        $sucesso = false;
                    }
                    
                } catch (\Exception $e) {
                    \Log::error('Erro na procedure Ppst_SERIE2_GRAVA', [
                        'error' => $e->getMessage(),
                        'produto' => $produto,
                        'lote' => $lote,
                        'pst_serie' => $pst_serie ?? 'N/A',
                        'pst_calibracao_original' => $calibracao['pst_calibracao'] ?? 'N/A',
                        'pst_calibracao_convertido' => $pst_calibracao ?? 'N/A'
                    ]);
                    $erros[] = "Série {$pst_serie}: " . $e->getMessage();
                    $sucesso = false;
                }
            }
            
            if (!$sucesso) {
                throw new \Exception('Erros ao atualizar calibração: ' . implode('; ', $erros));
            }
            
            return true;

        } catch (\Exception $e) {
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
        if (empty($data) || $data === null) {
            return '';
        }
        
        // Converter para string se não for
        $data = (string)$data;
        
        // Remover espaços em branco
        $data = trim($data);
        
        // Se está vazio após trim, retorna vazio
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
                \Log::warning('XML string vazio no parseXmlToList');
                return [];
            }



            // Corrige caracteres especiais que podem estar causando problemas
            $xmlString = str_replace('&lt;', '<', $xmlString);
            $xmlString = str_replace('&gt;', '>', $xmlString);
            $xmlString = str_replace('&amp;', '&', $xmlString);
            $xmlString = str_replace('&quot;', '"', $xmlString);
            

            
            // Verificar se o XML está completo ou truncado
            $openTags = substr_count($xmlString, '<row');
            $closeTags = substr_count($xmlString, '</row>');
            

            
            // Verificar se o XML está truncado e corrigir
            if ($openTags > $closeTags) {
                // Como as tags <row> são self-closing (/>), não precisamos adicionar </row>
                // Apenas verificamos se o XML está completo
                if (!str_ends_with($xmlString, '>')) {
                    // Remove a última linha incompleta
                    $lastRowPos = strrpos($xmlString, '<row');
                    if ($lastRowPos !== false) {
                        $xmlString = substr($xmlString, 0, $lastRowPos);
                    }
                }
            }
            
            // Adicionar tag <root> se não existir
            if (!str_contains($xmlString, '<root>')) {
                $xmlString = '<root>' . $xmlString . '</root>';
            }



            // Carrega o XML
            $xml = simplexml_load_string($xmlString);

            if ($xml === false) {
                $errors = libxml_get_errors();
                libxml_clear_errors();
                
                \Log::error('Erro ao parsear XML da procedure PPST_LISTA7A', [
                    'xml_string' => $xmlString,
                    'libxml_errors' => $errors,
                    'xml_length' => strlen($xmlString)
                ]);
                
                return [];
            }

            $result = [];

            // Procura por diferentes estruturas de XML - BUSCA EXAUSTIVA
            $result = [];
            
            // Estrutura 1: <row> direto
            if (isset($xml->row)) {
                foreach ($xml->row as $row) {
                    $item = new \stdClass();
                    foreach ($row->attributes() as $key => $value) {
                        $item->$key = (string)$value;
                    }
                    $result[] = $item;
                }
            }
            
            // Estrutura 2: <rows><row>
            if (isset($xml->rows) && isset($xml->rows->row)) {
                foreach ($xml->rows->row as $row) {
                    $item = new \stdClass();
                    foreach ($row->attributes() as $key => $value) {
                        $item->$key = (string)$value;
                    }
                    $result[] = $item;
                }
            }
            
            // Estrutura 3: <data><row>
            if (isset($xml->data) && isset($xml->data->row)) {
                foreach ($xml->data->row as $row) {
                    $item = new \stdClass();
                    foreach ($row->attributes() as $key => $value) {
                        $item->$key = (string)$value;
                    }
                    $result[] = $item;
                }
            }
            
            // Estrutura 4: <root><row> (se foi adicionado automaticamente)
            if (isset($xml->root) && isset($xml->root->row)) {
                foreach ($xml->root->row as $row) {
                    $item = new \stdClass();
                    foreach ($row->attributes() as $key => $value) {
                        $item->$key = (string)$value;
                    }
                    $result[] = $item;
                }
            }
            
            // Estrutura 5: Busca recursiva por qualquer elemento <row>
            if (empty($result)) {
                $result = $this->findRowsRecursively($xml);
            }
            
            // Estrutura 6: Busca por qualquer elemento que possa conter dados
            if (empty($result)) {
                foreach ($xml->children() as $child) {
                    if ($child->count() > 0) {
                        foreach ($child->children() as $subChild) {
                            if ($subChild->count() == 0) { // Elemento sem filhos
                                $item = new \stdClass();
                                foreach ($subChild->attributes() as $key => $value) {
                                    $item->$key = (string)$value;
                                }
                                if (!empty((array)$item)) {
                                    $result[] = $item;
                                }
                            }
                        }
                    }
                }
            }
            


            return $result;

        } catch (\Exception $e) {
            \Log::error('Exceção ao parsear XML da procedure PPST_LISTA7A', [
                'error' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'xml_string' => $xmlString ?? 'N/A',
                'xml_length' => strlen($xmlString ?? '')
            ]);
            
            return [];
        }
    }
    
    /**
     * Busca recursivamente por elementos <row> em qualquer nível do XML
     */
    private function findRowsRecursively($xml, $depth = 0)
    {
        $result = [];
        $maxDepth = 5; // Limitar profundidade para evitar loops infinitos
        
        if ($depth >= $maxDepth) {
            return $result;
        }
        
        foreach ($xml->children() as $child) {
            $childName = $child->getName();
            
            // Se encontrou um elemento <row>, processar
            if ($childName === 'row') {
                $item = new \stdClass();
                foreach ($child->attributes() as $key => $value) {
                    $item->$key = (string)$value;
                }
                $result[] = $item;
            }
            // Se o elemento tem filhos, buscar recursivamente
            elseif ($child->count() > 0) {
                $subResult = $this->findRowsRecursively($child, $depth + 1);
                $result = array_merge($result, $subResult);
            }
        }
        
        return $result;
    }
    
    /**
     * Extrai séries do XML para debug
     */
    private function extractSeriesFromXml($xmlString)
    {
        $series = [];
        
        // Buscar por diferentes formatos de atributo pst_serie
        if (preg_match_all('/pst_serie="([^"]*)"/', $xmlString, $matches)) {
            $series = $matches[1];
        } elseif (preg_match_all("/pst_serie='([^']*)'/", $xmlString, $matches)) {
            $series = $matches[1];
        }
        

        
        return $series;
    }
    


    /**
     * Converte data de calibração para formato SQL Server (YYYYMMDD HH:mm)
     *
     * @param string $data
     * @return string|null
     */
    private function converterDataParaSQLServer($data)
    {

        
        if (empty($data) || $data === null) {
            return null;
        }
        
        // Converter para string se não for
        $data = (string)$data;
        
        // Remover espaços em branco
        $data = trim($data);
        
        // Se está vazio após trim, retorna null
        if (empty($data)) {
            return null;
        }
        
        try {
            // Se já está no formato dd/MM/yyyy HH:mm, converter para YYYYMMDD HH:mm
            if (preg_match('/^\d{2}\/\d{2}\/\d{4} \d{2}:\d{2}$/', $data)) {
                $date = \DateTime::createFromFormat('d/m/Y H:i', $data);
                if ($date) {
                    return $date->format('Ymd H:i');
                }
            }
            
            // Se está no formato dd/MM/yyyy, converter para YYYYMMDD HH:mm (adicionar hora 00:00)
            if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $data)) {
                $date = \DateTime::createFromFormat('d/m/Y', $data);
                if ($date) {
                    return $date->format('Ymd H:i');
                }
            }
            
            // Se está no formato dd-MM-yyyy HH:mm, converter para YYYYMMDD HH:mm
            if (preg_match('/^\d{2}-\d{2}-\d{4} \d{2}:\d{2}$/', $data)) {
                $date = \DateTime::createFromFormat('d-m-Y H:i', $data);
                if ($date) {
                    return $date->format('Ymd H:i');
                }
            }
            
            // Se está no formato dd-MM-yyyy, converter para YYYYMMDD HH:mm (adicionar hora 00:00)
            if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $data)) {
                $date = \DateTime::createFromFormat('d-m-Y', $data);
                if ($date) {
                    return $date->format('Ymd H:i');
                }
            }
            
            // Se está no formato YYYY-MM-DD HH:MM:SS, converter para YYYYMMDD HH:mm
            if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $data)) {
                $date = \DateTime::createFromFormat('Y-m-d H:i:s', $data);
                if ($date) {
                    return $date->format('Ymd H:i');
                }
            }
            
            // Se está no formato YYYY-MM-DD, adicionar hora 00:00 e converter para YYYYMMDD HH:mm
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
                $date = \DateTime::createFromFormat('Y-m-d', $data);
                if ($date) {
                    return $date->format('Ymd H:i');
                }
            }
            
            // Se está no formato YYYY-MM-DDTHH:MM, converter para YYYYMMDD HH:mm
            if (preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $data)) {
                $date = \DateTime::createFromFormat('Y-m-d\TH:i', $data);
                if ($date) {
                    return $date->format('Ymd H:i');
                }
            }
            
            // Se está no formato YYYY-MM-DDTHH:MM:SS, converter para YYYYMMDD HH:mm
            if (preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}$/', $data)) {
                $date = \DateTime::createFromFormat('Y-m-d\TH:i:s', $data);
                if ($date) {
                    return $date->format('Ymd H:i');
                }
            }
            
            // Se não conseguir converter, tentar com strtotime
            $timestamp = strtotime($data);
            if ($timestamp !== false) {
                return date('Ymd H:i', $timestamp);
            }
            
            // Se não conseguir converter, retorna null
            return null;
            
        } catch (\Exception $e) {
            return null;
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
