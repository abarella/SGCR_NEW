<?php

namespace App\Services;

use PDO;
use PDOException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class DefinicaoSerieService
{
    protected $globalService;

    public function __construct(GlobalService $globalService)
    {
        $this->globalService = $globalService;
    }

    /**
     * Retorna lista de série para um produto/lote
     */
    public function retornarListaSerie($produto, $lote, $ordem = 1)
    {
        try {
            $dbh = DB::connection()->getPdo();
            $sql = "SET NOCOUNT ON; EXEC vendasPelicano.dbo.P0110_SERIE_LISTA @produto = ?, @lote = ?, @ORDEM = ?";
            $sth = $dbh->prepare($sql);
            $sth->execute([$produto, $lote, $ordem]);
            
            // Obtém o resultado da procedure, que deve ser XML
            $xmlResult = "";
            while ($row = $sth->fetch(\PDO::FETCH_NUM)) {
                $xmlResult .= $row[0]; // Supondo que a coluna 0 contenha o XML
            }

            // Verifica se o XML retornado já contém uma tag raiz
            if (!str_starts_with($xmlResult, "<root>")) {
                $xmlResult = "<root>$xmlResult</root>";
            }

            // Tenta carregar o XML
            libxml_use_internal_errors(true); // Ativa captura de erros
            $xml = simplexml_load_string($xmlResult);

            // Verifica se o XML foi carregado corretamente
            if ($xml === false) {
                Log::error('Erro ao carregar XML da lista de série');
                return [];
            }

            $lista = [];
            foreach ($xml->children() as $row) {
                $dataCalibracao = '';
                if (isset($row['p110dtpx']) && !empty($row['p110dtpx'])) {
                    $dataStr = (string) $row['p110dtpx'];
                    // Usar a data completa do banco de dados
                    if (strlen($dataStr) >= 8) {
                        $dataCalibracao = substr($dataStr, 0, 2) . '/' . 
                                         substr($dataStr, 3, 2) . '/' . 
                                         substr($dataStr, 6, 4);
                        // Se houver hora e minuto na string, adicionar
                        if (strlen($dataStr) > 10) {
                            $dataCalibracao .= ' ' . substr($dataStr, 11, 5); // hh:mm
                        }
                    }
                }
                
                $lista[] = (object) [
                    'numero' => (string) $row['p110lote'],
                    'medico' => (string) $row['cli_dest_responsavel'],
                    'uf' => (string) $row['uf_codigo'],
                    'atividade' => (string) $row['p110atv'],
                    'serie' => (string) $row['p110serie'],
                    'producao' => (string) $row['p110prod'],
                    'calibracao' => $dataCalibracao,
                    'observacao' => isset($row['p110obsd']) ? (string) $row['p110obsd'] : '',
                    'chve' => (string) $row['p110chve']
                ];
            }
            
            return $lista;
        } catch (\Exception $e) {
            Log::error('Erro ao retornar lista de série: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Carrega séries disponíveis
     */
    public function carregarSeries($produto, $lote)
    {
        try {
            $dbh = DB::connection()->getPdo();
            $sql = "EXEC sgcr.crsa.PPST_SERIE @produto = ?, @lote = ?";
            $sth = $dbh->prepare($sql);
            $sth->execute([$produto, $lote]);
            
            // Obter o resultado da procedure, que será em XML
            $xmlResult = "";
            while ($row = $sth->fetch(\PDO::FETCH_NUM)) {
                $xmlResult .= $row['0'];
            }

            // Verifica se o resultado está vazio
            if (empty($xmlResult)) {
                Log::warning("Procedure retornou vazio para produto '$produto' e lote '$lote'");
                return [
                    'mensagem' => "NENHUMA SÉRIE ENCONTRADA PARA O PRODUTO '$produto' NO LOTE $lote."
                ];
            }

            // Limpar espaços em branco e remover caracteres inesperados
            $xmlResult = trim($xmlResult);

            // Verifica se o XML retornado já contém uma tag raiz
            if (!str_starts_with($xmlResult, "<root>")) {
                $xmlResult = "<root>$xmlResult</root>";
            }

            // Carregar XML se não estiver vazio
            $xml = !empty($xmlResult) ? simplexml_load_string($xmlResult) : null;
            
            $series = [];
            foreach ($xml->row as $row) {
                $series[] = (object) [
                    'serie' => trim((string) $row['pst_serie']),
                    'numero' => trim((string) $row['numero'])
                ];
            }
    
            return $series;
        } catch (\Exception $e) {
            Log::error('Erro ao carregar séries: ' . $e->getMessage());
            return [
                'mensagem' => "NENHUMA SÉRIE ENCONTRADA PARA O PRODUTO '$produto' NO LOTE $lote."
            ];        
        }
    }
    

    /**
     * Grava série individual
     */
    public function gravarSerie($p110chve, $p110serie, $cdusuario, $senha)
    {
        try {
            // Validar senha
            if (!GlobalService::validaAcesso($cdusuario, $senha)) {
                return "Senha não confere ou Senha Inválida.";
            }

            // Executar procedure para gravar série
            $dbh = DB::connection()->getPdo();
            $sql = "DECLARE @resulta INT, @mensa VARCHAR(100);
                    EXEC vendasPelicano.dbo.P0110_SERIE_01 
                        @p110chve = ?, 
                        @serie = ?, 
                        @cdusuario = ?, 
                        @senha = ?, 
                        @resulta = @resulta OUTPUT, 
                        @mensa = @mensa OUTPUT;
                    SELECT @resulta AS resulta, @mensa AS mensa;";
            $sth = $dbh->prepare($sql);
            $sth->execute([$p110chve, $p110serie, $cdusuario, $senha]);
            $sth->nextRowset();
            
            $resultado = $sth->fetch(\PDO::FETCH_ASSOC);

            if ($resultado && isset($resultado['resulta'])) {
                if ($resultado['resulta'] == 0) {
                    return "Série definida com sucesso!";
                } else {
                    return "Erro: " . $resultado['mensa'];
                }
            } else {
                return "Nenhum valor foi inserido para a gravação.";
            }
        } catch (\Exception $e) {
            Log::error('Erro ao gravar série: ' . $e->getMessage());
            return "Erro ao definir série: " . $e->getMessage();
        }
    }

    public function buscarNumero($produto, $lote)
    {
        try {
            Log::info('Executando SGCR.crsa.PPST_SERIE', [
                'produto' => $produto,
                'lote' => $lote
            ]);
    
            $dbh = DB::connection()->getPdo();
            $sql = "EXEC SGCR.crsa.PPST_SERIE @produto = ?, @lote = ?;";
            $stmt = $dbh->prepare($sql);
            $stmt->execute([$produto, $lote]);
    
            $resultFinal = null;
            do {
                $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                if (!empty($rows)) {
                    $resultFinal = $rows[0]; // aqui está o XML
                }
            } while ($stmt->nextRowset());
    
            Log::info('Resultado final da procedure PPST_SERIE', ['retorno' => $resultFinal]);
    
            if (isset($resultFinal['XML_F52E2B61-18A1-11d1-B105-00805F49916B'])) {
                $xmlString = '<root>' . $resultFinal['XML_F52E2B61-18A1-11d1-B105-00805F49916B'] . '</root>';
                $xml = simplexml_load_string($xmlString);
                $maiorNumero = 1;
    
                foreach ($xml->row as $row) {
                    $numero = (int) $row['numero'];
                    if ($numero > $maiorNumero) {
                        $maiorNumero = $numero;
                    }
                }
    
                Log::debug('Maior número extraído da procedure:', ['numero' => $maiorNumero]);
                return $maiorNumero;
            }
    
            return 1;
    
        } catch (\Exception $e) {
            Log::error('Erro ao buscar número: ' . $e->getMessage());
            return 1;
        }
    }
    
    /**
     * Grava série por intervalo de atividade/lote
     */
    public function gravarSerieAtividade($produto, $lote, $serie, $cdusuario, $senha, $tipo, $inicio, $fim, $forca)
    {
        try {
            $valida = GlobalService::validaAcesso(Auth::user()->username, $senha);
            
            if (!$valida) {
                return "Senha não confere ou Senha Inválida.";
            }

            Log::info('Iniciando gravação de série por intervalo', [
                'produto' => $produto,
                'lote' => $lote,
                'serie' => $serie,
                'cdusuario' => $cdusuario,
                'senha' => $senha,
                'tipo' => $tipo,
                'inicio' => $inicio,
                'fim' => $fim,
                'forca' => $forca
            ]);

            $pdo = DB::connection()->getPdo();

            // Executa a procedure usando bindParam para capturar parâmetros de saída
            $sql = "EXEC vendasPelicano.dbo.P0110_SERIE ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?";
            
            $stmt = $pdo->prepare($sql);
            
            // Bind dos parâmetros de entrada
            $stmt->bindParam(1, $produto, \PDO::PARAM_STR);
            $stmt->bindParam(2, $lote, \PDO::PARAM_INT);
            $stmt->bindParam(3, $serie, \PDO::PARAM_STR);
            $stmt->bindParam(4, $tipo, \PDO::PARAM_INT);
            $stmt->bindParam(5, $inicio, \PDO::PARAM_INT);
            $stmt->bindParam(6, $fim, \PDO::PARAM_INT);
            $stmt->bindParam(7, $forca, \PDO::PARAM_STR);
            
            // Bind dos parâmetros de saída
            $resulta = 0;
            $mensa = '';
            $stmt->bindParam(8, $resulta, \PDO::PARAM_INT | \PDO::PARAM_INPUT_OUTPUT, 4);
            $stmt->bindParam(9, $mensa, \PDO::PARAM_STR | \PDO::PARAM_INPUT_OUTPUT, 100);
            
            // Bind dos parâmetros de entrada restantes
            $stmt->bindParam(10, $cdusuario, \PDO::PARAM_INT);
            $stmt->bindParam(11, $senha, \PDO::PARAM_STR);
            
            $stmt->execute();
            
            Log::info('Resultado da procedure P0110_SERIE', [
                'resulta' => $resulta,
                'mensa' => $mensa
            ]);

            // Verifica o resultado da procedure
            if ($resulta == 0) {
                return "REGISTROS ALTERADOS COM SUCESSO!";
            } else {
                return "Erro: " . ($mensa ?: 'Erro na execução');
            }

        } catch (\Exception $e) {
            Log::error('Erro ao gravar série por intervalo: ' . $e->getMessage());
            return "Erro ao definir série: " . $e->getMessage();
        }
    }
 
    /**
     * Procura série existente
     */
    public function procurarSerie($produto, $lote)
    {
        try {
            $dbh = DB::connection()->getPdo();
            Log::debug('Iniciando procurarSerie', ['produto' => $produto, 'lote' => $lote]);
            $sql = "EXEC sgcr.crsa.PPST_SERIE @produto = ?, @lote = ?";
            $sth = $dbh->prepare($sql);
            $sth->execute([$produto, $lote]);
            
            $xmlResult = "";
            while ($row = $sth->fetch(\PDO::FETCH_ASSOC)) {
                $xmlResult .= $row['0'];
            }

            if (empty($xmlResult)) {
                return "Nenhuma série encontrada para este produto/lote.";
            } else {
                return "Encontradas séries já definidas para este produto/lote.";
            }
        } catch (\Exception $e) {
            Log::error('Erro ao procurar série: ' . $e->getMessage());
            return "Erro ao procurar série: " . $e->getMessage();
        }
    }

      /**
     * Grava múltiplas séries da tabela
     */
    public function gravarMultiplasSeries($dados, $cdusuario, $senha)
    {
        try {
            Log::debug('Iniciando gravação múltipla de séries...');
            
            // Valida a senha como no sistema original
            $valida = GlobalService::validaAcesso(Auth::user()->username, $senha);
            if (!$valida) {
                Log::warning("Senha inválida para o usuário " . Auth::user()->username);
                return "Senha não confere ou Senha Inválida.";
            }
    
            $verifica = 0;
            $dbh = DB::connection()->getPdo();
            $resulta = 0;
            $mensa = '';
    
            // Processa cada item da tabela
            foreach ($dados as $item) {
                if (!empty($item['serie'])) {
                    $verifica = 1;
    
                    // Monta os parâmetros de forma similar à string QS do sistema original
                    $sql = "EXEC vendasPelicano.dbo.P0110_SERIE_01 ?, ?, ?, ?, ?, ?";
                    $sth = $dbh->prepare($sql);
    
                    // Cria variáveis de saída
                    $resulta = 0;
                    $mensa = '';
    
                    // Bind nos parâmetros conforme ordem da procedure
                    $sth->bindParam(1, $item['chve'], PDO::PARAM_STR);
                    $sth->bindParam(2, $item['serie'], PDO::PARAM_STR);
                    $sth->bindParam(3, $cdusuario, PDO::PARAM_INT);
                    $sth->bindParam(4, $senha, PDO::PARAM_STR);
                    $sth->bindParam(5, $resulta, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT, 4);
                    $sth->bindParam(6, $mensa, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 100);
    
                    // Executa a procedure
                    $sth->execute();
    
                    // Verifica erro da procedure, como o JS faz com getMensagemErro
                    if ($resulta != 0) {
                        Log::warning("Erro ao processar item [{$item['chve']}]: {$mensa}");
                        return $mensa;
                    }
                }
            }
    
            // Se nenhum valor foi passado
            if ($verifica == 0) {
                return "Nenhum valor foi inserido para a gravação.";
            }
    
            // Tudo OK
            return "REGISTROS ALTERADOS COM SUCESSO!";
            
        } catch (\Exception $e) {
            Log::error("Erro ao executar gravação múltipla: " . $e->getMessage());
            return "Erro ao definir séries: " . $e->getMessage();
        }
    }  
} 