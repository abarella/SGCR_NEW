<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Services\PspPsService;
use App\Services\GlobalService;
use App\Services\PspPcService;

class PspPcController extends Controller
{
    protected $pspPsService;
    protected $globalService;
    protected $pspPcService;

    public function __construct(PspPsService $pspPsService, GlobalService $globalService, PspPcService $pspPcService)
    {
        $this->pspPsService = $pspPsService;
        $this->globalService = $globalService;
        $this->pspPcService = $pspPcService;
    }

    /**
     * Exibe a página principal do PSP-PC
     */
    public function index()
    {
        try {
            Log::info('PSP-PC: Acessando página principal');
            return view('psp-pc.index');
        } catch (\Exception $e) {
            Log::error('PSP-PC: Erro ao acessar página principal: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao carregar página');
        }
    }

    /**
     * Retorna a lista de pastas não concluídas usando procedures
     */
    public function lista(Request $request)
    {
        try {
            Log::info('PSP-PC: Carregando lista de pastas via service');
            
            $produto = $request->get('produto', '');
            $pasta = $request->get('pasta', '');
            $pagina = $request->get('pagina', 1);
            $porPagina = 50;
            
            // Log detalhado dos parâmetros recebidos
            Log::info('PSP-PC: Parâmetros recebidos', [
                'produto' => $produto,
                'pasta' => $pasta,
                'pagina' => $pagina,
                'produto_vazio' => empty($produto),
                'pasta_vazio' => empty($pasta),
                'request_all' => $request->all(),
                'request_query' => $request->query()
            ]);
            
            // Usar o service para buscar as pastas
            $filtros = [];
            if (!empty($produto)) $filtros['produto'] = $produto;
            if (!empty($pasta)) $filtros['pasta'] = $pasta;
            
            $pastas = $this->pspPcService->listarPastasNaoConcluidas($filtros);
            
            Log::info('PSP-PC: Pastas carregadas via service', ['count' => count($pastas)]);
            
            // Paginação manual
            $total = count($pastas);
            $pastas = array_slice($pastas, ($pagina - 1) * $porPagina, $porPagina);
            
            Log::info('PSP-PC: Lista carregada com sucesso via service', ['total' => $total, 'pagina' => $pagina, 'retornando' => count($pastas)]);
            
            return response()->json([
                'success' => true,
                'data' => $pastas,
                'total' => $total,
                'pagina' => $pagina,
                'porPagina' => $porPagina,
                'totalPaginas' => ceil($total / $porPagina)
            ]);
            
        } catch (\Exception $e) {
            Log::error('PSP-PC: Erro ao carregar lista via service: ' . $e->getMessage());
            Log::error('PSP-PC: Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar lista de pastas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retorna a lista de produtos para o combo usando o service
     */
    public function produtos()
    {
        try {
            Log::info('PSP-PC: Carregando lista de produtos via service');
            
            $produtos = $this->pspPcService->listarProdutos();
            
            if (empty($produtos)) {
                Log::warning('PSP-PC: Nenhum produto encontrado via service');
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'message' => 'Nenhum produto encontrado'
                ]);
            }
            
            Log::info('PSP-PC: Produtos carregados com sucesso via service', ['total' => count($produtos)]);
            
            return response()->json([
                'success' => true,
                'data' => $produtos
            ]);
            
        } catch (\Exception $e) {
            Log::error('PSP-PC: Erro ao carregar produtos: ' . $e->getMessage());
            Log::error('PSP-PC: Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar produtos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Método de teste para verificar conexão com banco
     */
    public function testarConexao()
    {
        try {
            Log::info('PSP-PC: Testando conexão com banco de dados via service');
            
            $resultado = $this->pspPcService->testarConexao();
            
            if ($resultado['success']) {
                return response()->json($resultado);
            } else {
                return response()->json($resultado, 500);
            }
            
        } catch (\Exception $e) {
            Log::error('PSP-PC: Erro ao testar conexão: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro na conexão: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Abre modal de documentação
     */
    public function documentacao(Request $request)
    {
        try {
            $pasta = $request->get('pasta');
            Log::info('PSP-PC: Abrindo modal de documentação para pasta', ['pasta' => $pasta]);
            
            // Usar a mesma procedure da lista para obter dados da pasta
            $dbh = DB::connection()->getPdo();
            $sql = "SET NOCOUNT ON; EXEC sgcr.crsa.PPST_LISTA_NAOCONCLUIDOS default, 1, default, default";
            
            $stmt = $dbh->query($sql);
            
            // Obter resultados da procedure
            $xmlResult = "";
            while ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
                $xmlResult .= $row[0];
            }
            
            if (empty($xmlResult)) {
                Log::warning('PSP-PC: Procedure PPST_LISTA_NAOCONCLUIDOS retornou vazio para documentação');
                return response()->json([
                    'success' => false,
                    'message' => 'Pasta não encontrada'
                ], 404);
            }
            
            // Processar XML retornado pela procedure
            $xmlResult = trim($xmlResult);
            if (!str_starts_with($xmlResult, "<root>")) {
                $xmlResult = "<root>$xmlResult</root>";
            }
            
            $xml = simplexml_load_string($xmlResult);
            if (!$xml) {
                Log::error('PSP-PC: Erro ao processar XML da procedure para documentação', ['xml' => $xmlResult]);
                throw new \Exception('Erro ao processar XML retornado pela procedure');
            }
            
            // Encontrar a pasta específica
            $dadosPasta = null;
            if (isset($xml->row)) {
                foreach ($xml->row as $row) {
                    if ((string)($row['pst_numero'] ?? '') === $pasta) {
                        $dadosPasta = (object) [
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
                        break;
                    }
                }
            }
            
            if (empty($dadosPasta)) {
                Log::warning('PSP-PC: Pasta não encontrada na procedure', ['pasta' => $pasta]);
                return response()->json([
                    'success' => false,
                    'message' => 'Pasta não encontrada'
                ], 404);
            }
            
            Log::info('PSP-PC: Dados da pasta carregados com sucesso para documentação', ['pasta' => $pasta]);
            
            return response()->json([
                'success' => true,
                'data' => $dadosPasta,
                'view' => view('psp-pc.modals.documentacao', compact('dadosPasta'))->render()
            ]);
            
        } catch (\Exception $e) {
            Log::error('PSP-PC: Erro ao abrir modal de documentação: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar dados da pasta',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Abre modal de ocorrências
     */
    public function ocorrencias(Request $request)
    {
        try {
            $pasta = $request->get('pasta');
            Log::info('PSP-PC: Abrindo modal de ocorrências para pasta', ['pasta' => $pasta]);
            
            // Usar a mesma procedure da lista para obter dados da pasta
            $dbh = DB::connection()->getPdo();
            $sql = "SET NOCOUNT ON; EXEC sgcr.crsa.PPST_LISTA_NAOCONCLUIDOS default, 1, default, default";
            
            $stmt = $dbh->query($sql);
            
            // Obter resultados da procedure
            $xmlResult = "";
            while ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
                $xmlResult .= $row[0];
            }
            
            if (empty($xmlResult)) {
                Log::warning('PSP-PC: Procedure PPST_LISTA_NAOCONCLUIDOS retornou vazio para ocorrências');
                return response()->json([
                    'success' => false,
                    'message' => 'Pasta não encontrada'
                ], 404);
            }
            
            // Processar XML retornado pela procedure
            $xmlResult = trim($xmlResult);
            if (!str_starts_with($xmlResult, "<root>")) {
                $xmlResult = "<root>$xmlResult</root>";
            }
            
            $xml = simplexml_load_string($xmlResult);
            if (!$xml) {
                Log::error('PSP-PC: Erro ao processar XML da procedure para ocorrências', ['xml' => $xmlResult]);
                throw new \Exception('Erro ao processar XML retornado pela procedure');
            }
            
            // Encontrar a pasta específica
            $dadosPasta = null;
            if (isset($xml->row)) {
                foreach ($xml->row as $row) {
                    if ((string)($row['pst_numero'] ?? '') === $pasta) {
                        $dadosPasta = (object) [
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
                        break;
                    }
                }
            }
            
            if (empty($dadosPasta)) {
                Log::warning('PSP-PC: Pasta não encontrada na procedure', ['pasta' => $pasta]);
                return response()->json([
                    'success' => false,
                    'message' => 'Pasta não encontrada'
                ], 404);
            }
            
            Log::info('PSP-PC: Dados da pasta carregados com sucesso para ocorrências', ['pasta' => $pasta]);
            
            return response()->json([
                'success' => true,
                'data' => $dadosPasta,
                'view' => view('psp-pc.modals.ocorrencias', compact('dadosPasta'))->render()
            ]);
            
        } catch (\Exception $e) {
            Log::error('PSP-PC: Erro ao abrir modal de ocorrências: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar dados da pasta',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Abre modal de localização
     */
    public function localizar(Request $request)
    {
        try {
            $pasta = $request->get('pasta');
            Log::info('PSP-PC: Abrindo modal de localização para pasta', ['pasta' => $pasta]);
            
            // Usar a mesma procedure da lista para obter dados da pasta
            $dbh = DB::connection()->getPdo();
            $sql = "SET NOCOUNT ON; EXEC sgcr.crsa.PPST_LISTA_NAOCONCLUIDOS default, 1, default, default";
            
            $stmt = $dbh->query($sql);
            
            // Obter resultados da procedure
            $xmlResult = "";
            while ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
                $xmlResult .= $row[0];
            }
            
            if (empty($xmlResult)) {
                Log::warning('PSP-PC: Procedure PPST_LISTA_NAOCONCLUIDOS retornou vazio para localização');
                return response()->json([
                    'success' => false,
                    'message' => 'Pasta não encontrada'
                ], 404);
            }
            
            // Processar XML retornado pela procedure
            $xmlResult = trim($xmlResult);
            if (!str_starts_with($xmlResult, "<root>")) {
                $xmlResult = "<root>$xmlResult</root>";
            }
            
            $xml = simplexml_load_string($xmlResult);
            if (!$xml) {
                Log::error('PSP-PC: Erro ao processar XML da procedure para localização', ['xml' => $xmlResult]);
                throw new \Exception('Erro ao processar XML retornado pela procedure');
            }
            
            // Encontrar a pasta específica
            $dadosPasta = null;
            if (isset($xml->row)) {
                foreach ($xml->row as $row) {
                    if ((string)($row['pst_numero'] ?? '') === $pasta) {
                        $dadosPasta = (object) [
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
                        break;
                    }
                }
            }
            
            if (empty($dadosPasta)) {
                Log::warning('PSP-PC: Pasta não encontrada na procedure', ['pasta' => $pasta]);
                return response()->json([
                    'success' => false,
                    'message' => 'Pasta não encontrada'
                ], 404);
            }
            
            Log::info('PSP-PC: Dados da pasta carregados com sucesso para localização', ['pasta' => $pasta]);
            
            return response()->json([
                'success' => true,
                'data' => $dadosPasta,
                'view' => view('psp-pc.modals.localizar', compact('dadosPasta'))->render()
            ]);
            
        } catch (\Exception $e) {
            Log::error('PSP-PC: Erro ao abrir modal de localização: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar dados da pasta',
                'error' => $e->getMessage()
            ], 500);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar dados da pasta',
                'error' => $e->getMessage()
            ], 500);
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
     * Método para testar a procedure PPST_LISTA diretamente
     */
    public function testarProcedure(Request $request)
    {
        try {
            Log::info('PSP-PC: Testando procedure PPST_LISTA diretamente');
            
            $dbh = DB::connection()->getPdo();
            $sql = "SET NOCOUNT ON; EXEC sgcr.crsa.PPST_LISTA
                    @mes = NULL,
                    @ano = :ano,
                    @ordem = 0,
                    @tipo = NULL,
                    @grupo = NULL,
                    @pst_numero = NULL";
            
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':ano', date('Y'));
            $stmt->execute();
            
            Log::info('PSP-PC: Procedure executada, verificando resultados');
            
            $results = [];
            $rowCount = 0;
            
            // Tentar obter resultados
            do {
                try {
                    $rows = $stmt->fetchAll(\PDO::FETCH_OBJ);
                    if ($rows) {
                        $results = $rows;
                        $rowCount = count($rows);
                        Log::info('PSP-PC: Procedure retornou dados', ['count' => $rowCount, 'primeira_linha' => $rows[0] ?? 'N/A']);
                    }
                } catch (\Exception $e) {
                    Log::warning('PSP-PC: Erro ao buscar resultados: ' . $e->getMessage());
                    continue;
                }
            } while ($stmt->nextRowset());
            
            if (empty($results)) {
                Log::warning('PSP-PC: Procedure não retornou dados');
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'message' => 'Procedure executada mas não retornou dados'
                ]);
            }
            
            return response()->json([
                'success' => true,
                'data' => $results,
                'message' => 'Procedure executada com sucesso',
                'count' => $rowCount
            ]);
            
        } catch (\Exception $e) {
            Log::error('PSP-PC: Erro ao executar procedure: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro na procedure: ' . $e->getMessage()
            ], 500);
        }
    }



    /**
     * Método alternativo para testar lista de pastas
     */
    
    // ===================================
    // MÉTODOS PARA MODAIS REFATORADOS
    // ===================================
    
    /**
     * Carrega usuários revisores (grupo 6)
     */
    public function usuariosRevisores()
    {
        try {
            Log::info('PSP-PC: Carregando usuários revisores');
            
            $dbh = DB::connection()->getPdo();
            $sql = "EXEC sgcr.crsa.P1110_USUARIOS @p052_grupocd = 6, @p1110_ativo = 'A', @ordem = 1";
            
            $stmt = $dbh->query($sql);
            $usuarios = [];
            
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $usuarios[] = (object) [
                    'p1110_usuarioid' => $row['p1110_usuarioid'],
                    'p1110_nome' => $row['p1110_nome']
                ];
            }
            
            Log::info('PSP-PC: Usuários revisores carregados', ['total' => count($usuarios)]);
            
            return response()->json([
                'success' => true,
                'data' => $usuarios
            ]);
            
        } catch (\Exception $e) {
            Log::error('PSP-PC: Erro ao carregar usuários revisores: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar usuários revisores'
            ], 500);
        }
    }
    
    /**
     * Carrega status da pasta
     */
    public function statusPasta()
    {
        try {
            Log::info('PSP-PC: Carregando status da pasta');
            
            $dbh = DB::connection()->getPdo();
            $sql = "EXEC sgcr.crsa.PPST_STATUS @codigo = null";
            
            $stmt = $dbh->query($sql);
            $status = [];
            
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $status[] = (object) [
                    'pststs_codigo' => $row['pststs_codigo'],
                    'pststs_descricao' => $row['pststs_descricao']
                ];
            }
            
            Log::info('PSP-PC: Status da pasta carregados', ['total' => count($status)]);
            
            return response()->json([
                'success' => true,
                'data' => $status
            ]);
            
        } catch (\Exception $e) {
            Log::error('PSP-PC: Erro ao carregar status da pasta: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar status da pasta'
            ], 500);
        }
    }
    
    /**
     * Carrega status de produção
     */
    public function statusProducao()
    {
        try {
            Log::info('PSP-PC: Carregando status de produção');
            
            $dbh = DB::connection()->getPdo();
            $sql = "EXEC sgcr.crsa.PPST_PRODUCAOSTATUS";
            
            $stmt = $dbh->query($sql);
            $status = [];
            
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $status[] = (object) [
                    'pstprod_status' => $row['pstprod_status'],
                    'pstprod_descricao' => $row['pstprod_descricao']
                ];
            }
            
            Log::info('PSP-PC: Status de produção carregados', ['total' => count($status)]);
            
            return response()->json([
                'success' => true,
                'data' => $status
            ]);
            
        } catch (\Exception $e) {
            Log::error('PSP-PC: Erro ao carregar status de produção: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar status de produção'
            ], 500);
        }
    }
    
    /**
     * Carrega dados da documentação usando PPST_LISTA4
     */
    public function documentacaoDados(Request $request)
    {
        try {
            $pasta = $request->get('pasta');
            $tipo = $request->get('tipo');
            
            Log::info('PSP-PC: Carregando dados da documentação', ['pasta' => $pasta, 'tipo' => $tipo]);
            
            $dbh = DB::connection()->getPdo();
            $sql = "EXEC sgcr.crsa.PPST_LISTA4 @pst_numero = :numero, @tipo = :tipo";
            
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':numero', $pasta);
            $stmt->bindParam(':tipo', $tipo);
            $stmt->execute();
            
            $dados = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if ($dados) {
                Log::info('PSP-PC: Dados da documentação carregados', ['pasta' => $pasta]);
                
                return response()->json([
                    'success' => true,
                    'data' => (object) $dados
                ]);
            } else {
                Log::warning('PSP-PC: Nenhum dado encontrado para documentação', ['pasta' => $pasta]);
                
                return response()->json([
                    'success' => true,
                    'data' => (object) []
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('PSP-PC: Erro ao carregar dados da documentação: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar dados da documentação'
            ], 500);
        }
    }

    /**
     * Carrega dados da documentação usando PPST_LISTA4 (rota POST)
     */
    public function ppstLista4(Request $request)
    {
        try {
            $pasta = $request->get('pst_numero');
            $tipo = $request->get('tipo');
            
            Log::info('PSP-PC: Carregando dados da documentação via PPST_LISTA4', ['pasta' => $pasta, 'tipo' => $tipo]);
            
            $dbh = DB::connection()->getPdo();
            $sql = "EXEC sgcr.crsa.PPST_LISTA4 @pst_numero = :numero, @tipo = :tipo";
            
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':numero', $pasta);
            $stmt->bindParam(':tipo', $tipo);
            $stmt->execute();
            
            $dados = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if ($dados) {
                Log::info('PSP-PC: Dados da documentação carregados via PPST_LISTA4', ['pasta' => $pasta]);
                
                return response()->json([
                    'success' => true,
                    'data' => (object) $dados
                ]);
            } else {
                Log::warning('PSP-PC: Nenhum dado encontrado para documentação via PPST_LISTA4', ['pasta' => $pasta]);
                
                return response()->json([
                    'success' => true,
                    'data' => (object) []
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('PSP-PC: Erro ao carregar dados da documentação via PPST_LISTA4: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar dados da documentação'
            ], 500);
        }
    }
    
    /**
     * Salva documentação usando a procedure Ppst_Documentacao
     */
    public function documentacaoSalvar(Request $request)
    {
        try {
            $dados = $request->all();
            Log::info('PSP-PC: Salvando documentação', $dados);
            
            // Validar campos obrigatórios
            $camposObrigatorios = ['pst_ano', 'pst_numero', 'pst_status', 'pst_prodstatus', 'pst_de', 'pst_revisadopor', 'pst_doc_data', 'pst_observacao', 'cdusuario', 'senha'];
            foreach ($camposObrigatorios as $campo) {
                if (empty($dados[$campo])) {
                    return response()->json([
                        'success' => false,
                        'message' => "Campo obrigatório não informado: {$campo}"
                    ], 400);
                }
            }
            
            // Verificar senha primeiro
            $dbh = DB::connection()->getPdo();
            $sqlSenha = "EXEC sgcr.crsa.P1110_confsenha @p1110_usuarioid = :usuarioid, @p1110_senha = :senha";
            $stmtSenha = $dbh->prepare($sqlSenha);
            $stmtSenha->bindParam(':usuarioid', $dados['cdusuario']);
            $stmtSenha->bindParam(':senha', $dados['senha']);
            $stmtSenha->execute();
            
            $resultadoSenha = $stmtSenha->fetch(\PDO::FETCH_ASSOC);
            if (!$resultadoSenha || $resultadoSenha['resulta'] != 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Senha não confere ou Senha Inválida'
                ], 400);
            }
            
            // Executar procedure para salvar documentação
            $sql = "EXEC sgcr.crsa.Ppst_Documentacao 
                    @pst_ano = :pst_ano,
                    @pst_numero = :pst_numero,
                    @pst_status = :pst_status,
                    @pst_prodstatus = :pst_prodstatus,
                    @pst_de = :pst_de,
                    @pst_revisadopor = :pst_revisadopor,
                    @pst_doc_data = :pst_doc_data,
                    @pst_observacao = :pst_observacao,
                    @cdusuario = :cdusuario,
                    @senha = :senha";
            
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':pst_ano', $dados['pst_ano']);
            $stmt->bindParam(':pst_numero', $dados['pst_numero']);
            $stmt->bindParam(':pst_status', $dados['pst_status']);
            $stmt->bindParam(':pst_prodstatus', $dados['pst_prodstatus']);
            $stmt->bindParam(':pst_de', $dados['pst_de']);
            $stmt->bindParam(':pst_revisadopor', $dados['pst_revisadopor']);
            $stmt->bindParam(':pst_doc_data', $dados['pst_doc_data']);
            $stmt->bindParam(':pst_observacao', $dados['pst_observacao']);
            $stmt->bindParam(':cdusuario', $dados['cdusuario']);
            $stmt->bindParam(':senha', $dados['senha']);
            
            $stmt->execute();
            
            $resultado = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if ($resultado && isset($resultado['RETURN_VALUE']) && $resultado['RETURN_VALUE'] == 0) {
                Log::info('PSP-PC: Documentação salva com sucesso');
                
                return response()->json([
                    'success' => true,
                    'message' => 'REGISTROS ALTERADOS COM SUCESSO'
                ]);
            } else {
                Log::warning('PSP-PC: Falha ao salvar documentação', ['resultado' => $resultado]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Falha ao salvar documentação'
                ], 500);
            }
            
        } catch (\Exception $e) {
            Log::error('PSP-PC: Erro ao salvar documentação: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar documentação: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Carrega tipos de ocorrência
     */
    public function tiposOcorrencia()
    {
        try {
            Log::info('PSP-PC: Carregando tipos de ocorrência');
            
            // Aqui você implementaria a procedure para carregar tipos de ocorrência
            // Por enquanto, retornamos dados de exemplo
            
            $tipos = [
                (object) ['tipo_codigo' => '1', 'tipo_descricao' => 'Problema na Produção'],
                (object) ['tipo_codigo' => '2', 'tipo_descricao' => 'Material Faltando'],
                (object) ['tipo_codigo' => '3', 'tipo_descricao' => 'Problema de Qualidade'],
                (object) ['tipo_codigo' => '4', 'tipo_descricao' => 'Problema com Equipamento'],
                (object) ['tipo_codigo' => '5', 'tipo_descricao' => 'Outros']
            ];
            
            Log::info('PSP-PC: Tipos de ocorrência carregados', ['total' => count($tipos)]);
            
            return response()->json([
                'success' => true,
                'data' => $tipos
            ]);
            
        } catch (\Exception $e) {
            Log::error('PSP-PC: Erro ao carregar tipos de ocorrência: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar tipos de ocorrência'
            ], 500);
        }
    }
    
    /**
     * Carrega usuários responsáveis
     */
    public function usuariosResponsaveis()
    {
        try {
            Log::info('PSP-PC: Carregando usuários responsáveis');
            
            $dbh = DB::connection()->getPdo();
            $sql = "EXEC sgcr.crsa.P1110_USUARIOS @p1110_ativo = 'A', @ordem = 1";
            
            $stmt = $dbh->query($sql);
            $usuarios = [];
            
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $usuarios[] = (object) [
                    'p1110_usuarioid' => $row['p1110_usuarioid'],
                    'p1110_nome' => $row['p1110_nome']
                ];
            }
            
            Log::info('PSP-PC: Usuários responsáveis carregados', ['total' => count($usuarios)]);
            
            return response()->json([
                'success' => true,
                'data' => $usuarios
            ]);
            
        } catch (\Exception $e) {
            Log::error('PSP-PC: Erro ao carregar usuários responsáveis: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar usuários responsáveis'
            ], 500);
        }
    }
    
    /**
     * Carrega lista de ocorrências
     */
    public function ocorrenciasLista(Request $request)
    {
        try {
            $pasta = $request->get('pasta');
            Log::info('PSP-PC: Carregando lista de ocorrências', ['pasta' => $pasta]);
            
            // Aqui você implementaria a procedure para carregar ocorrências
            // Por enquanto, retornamos dados vazios
            
            Log::info('PSP-PC: Lista de ocorrências carregada', ['pasta' => $pasta]);
            
            return response()->json([
                'success' => true,
                'data' => []
            ]);
            
        } catch (\Exception $e) {
            Log::error('PSP-PC: Erro ao carregar lista de ocorrências: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar lista de ocorrências'
            ], 500);
        }
    }
    
    /**
     * Salva ocorrência
     */
    public function ocorrenciaSalvar(Request $request)
    {
        try {
            $dados = $request->all();
            Log::info('PSP-PC: Salvando ocorrência', $dados);
            
            // Aqui você implementaria a procedure para salvar a ocorrência
            // Por enquanto, apenas retornamos sucesso
            
            Log::info('PSP-PC: Ocorrência salva com sucesso');
            
            return response()->json([
                'success' => true,
                'message' => 'Ocorrência salva com sucesso'
            ]);
            
        } catch (\Exception $e) {
            Log::error('PSP-PC: Erro ao salvar ocorrência: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar ocorrência'
            ], 500);
        }
    }
    
    /**
     * Carrega setores
     */
    public function setores()
    {
        try {
            Log::info('PSP-PC: Carregando setores');
            
            // Aqui você implementaria a procedure para carregar setores
            // Por enquanto, retornamos dados de exemplo
            
            $setores = [
                (object) ['setor_codigo' => '1', 'setor_descricao' => 'Produção'],
                (object) ['setor_codigo' => '2', 'setor_descricao' => 'Controle de Qualidade'],
                (object) ['setor_codigo' => '3', 'setor_descricao' => 'Almoxarifado'],
                (object) ['setor_codigo' => '4', 'setor_descricao' => 'Expedição'],
                (object) ['setor_codigo' => '5', 'setor_descricao' => 'Administrativo']
            ];
            
            Log::info('PSP-PC: Setores carregados', ['total' => count($setores)]);
            
            return response()->json([
                'success' => true,
                'data' => $setores
            ]);
            
        } catch (\Exception $e) {
            Log::error('PSP-PC: Erro ao carregar setores: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar setores'
            ], 500);
        }
    }
    
    /**
     * Carrega histórico de localizações
     */
    public function localizacoesHistorico(Request $request)
    {
        try {
            $pasta = $request->get('pasta');
            Log::info('PSP-PC: Carregando histórico de localizações', ['pasta' => $pasta]);
            
            // Aqui você implementaria a procedure para carregar histórico de localizações
            // Por enquanto, retornamos dados vazios
            
            Log::info('PSP-PC: Histórico de localizações carregado', ['pasta' => $pasta]);
            
            return response()->json([
                'success' => true,
                'data' => []
            ]);
            
        } catch (\Exception $e) {
            Log::error('PSP-PC: Erro ao carregar histórico de localizações: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar histórico de localizações'
            ], 500);
        }
    }
    
    /**
     * Salva localização
     */
    public function localizacaoSalvar(Request $request)
    {
        try {
            $dados = $request->all();
            Log::info('PSP-PC: Salvando localização', $dados);
            
            // Aqui você implementaria a procedure para salvar a localização
            // Por enquanto, apenas retornamos sucesso
            
            Log::info('PSP-PC: Localização salva com sucesso');
            
            return response()->json([
                'success' => true,
                'message' => 'Localização salva com sucesso'
            ]);
            
        } catch (\Exception $e) {
            Log::error('PSP-PC: Erro ao salvar localização: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar localização'
            ], 500);
        }
    }
    
    /**
     * Executa procedures conforme solicitado pelo JavaScript
     */
    public function executarProcedure(Request $request)
    {
        try {
            $procedure = $request->input('procedure');
            $parameters = $request->input('parameters', []);
            
            Log::info('PSP-PC: Executando procedure', ['procedure' => $procedure, 'parameters' => $parameters]);
            
            $dbh = DB::connection()->getPdo();
            $result = null;
            
            switch ($procedure) {
                case 'crsa.P1110_USUARIOS':
                    $result = $this->executarP1110Usuarios($dbh, $parameters);
                    break;
                    
                case 'crsa.PPST_STATUS':
                    $result = $this->executarPPSTStatus($dbh, $parameters);
                    break;
                    
                case 'crsa.PPST_PRODUCAOSTATUS':
                    $result = $this->executarPPSTProducaoStatus($dbh, $parameters);
                    break;
                    
                case 'crsa.PPST_LISTA4':
                    $result = $this->executarPPSTLista4($dbh, $parameters);
                    break;
                    
                case 'crsa.P1110_confsenha':
                    $result = $this->executarP1110ConfSenha($dbh, $parameters);
                    break;
                    
                case 'crsa.Ppst_Documentacao':
                    $result = $this->executarPpstDocumentacao($dbh, $parameters);
                    break;
                    
                default:
                    throw new \Exception('Procedure não implementada: ' . $procedure);
            }
            
            Log::info('PSP-PC: Procedure executada com sucesso', ['procedure' => $procedure]);
            
            return response()->json([
                'success' => true,
                'data' => $result
            ]);
            
        } catch (\Exception $e) {
            Log::error('PSP-PC: Erro ao executar procedure: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao executar procedure: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Executa procedure P1110_USUARIOS
     */
    private function executarP1110Usuarios($dbh, $parameters)
    {
        $grupocd = $parameters['@p052_grupocd'] ?? null;
        $ativo = $parameters['@p1110_ativo'] ?? 'A';
        $ordem = $parameters['@ordem'] ?? 1;
        
        $sql = "EXEC sgcr.crsa.P1110_USUARIOS";
        $params = [];
        
        if ($grupocd !== null) {
            $sql .= " @p052_grupocd = :grupocd";
            $params[':grupocd'] = $grupocd;
        }
        
        $sql .= " @p1110_ativo = :ativo, @ordem = :ordem";
        $params[':ativo'] = $ativo;
        $params[':ordem'] = $ordem;
        
        $stmt = $dbh->prepare($sql);
        $stmt->execute($params);
        
        $usuarios = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $usuarios[] = $row;
        }
        
        return [
            'data' => $usuarios,
            'total' => count($usuarios)
        ];
    }
    
    /**
     * Executa procedure PPST_STATUS
     */
    private function executarPPSTStatus($dbh, $parameters)
    {
        $status = $parameters['@status'] ?? null;
        
        if ($status !== null) {
            // Buscar código por descrição
            $sql = "EXEC sgcr.crsa.PPST_STATUS @status = :status";
            $stmt = $dbh->prepare($sql);
            $stmt->execute([':status' => $status]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            return [
                'data' => [$result],
                'total' => 1,
                'codigo' => $result['codigo'] ?? null
            ];
        } else {
            // Listar todos os status
            $sql = "EXEC sgcr.crsa.PPST_STATUS @codigo = null";
            $stmt = $dbh->query($sql);
            
            $status = [];
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $status[] = $row;
            }
            
            return [
                'data' => $status,
                'total' => count($status)
            ];
        }
    }
    
    /**
     * Executa procedure PPST_PRODUCAOSTATUS
     */
    private function executarPPSTProducaoStatus($dbh, $parameters)
    {
        $sql = "EXEC sgcr.crsa.PPST_PRODUCAOSTATUS";
        $stmt = $dbh->query($sql);
        
        $status = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $status[] = $row;
        }
        
        return [
            'data' => $status,
            'total' => count($status)
        ];
    }
    
    /**
     * Executa procedure PPST_LISTA4
     */
    private function executarPPSTLista4($dbh, $parameters)
    {
        $numero = $parameters['@pst_numero'] ?? null;
        $tipo = $parameters['@tipo'] ?? null;
        
        if (!$numero || !$tipo) {
            throw new \Exception('Parâmetros obrigatórios não fornecidos');
        }
        
        $sql = "EXEC sgcr.crsa.PPST_LISTA4 @pst_numero = :numero, @tipo = :tipo";
        $stmt = $dbh->prepare($sql);
        $stmt->execute([
            ':numero' => $numero,
            ':tipo' => $tipo
        ]);
        
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return $result ?: [];
    }
    
    /**
     * Executa procedure P1110_confsenha
     */
    private function executarP1110ConfSenha($dbh, $parameters)
    {
        $usuarioid = $parameters['@p1110_usuarioid'] ?? null;
        $senha = $parameters['@p1110_senha'] ?? null;
        
        if (!$usuarioid || !$senha) {
            throw new \Exception('Usuário e senha são obrigatórios');
        }
        
        $sql = "EXEC sgcr.crsa.P1110_confsenha @p1110_usuarioid = :usuarioid, @p1110_senha = :senha";
        $stmt = $dbh->prepare($sql);
        $stmt->execute([
            ':usuarioid' => $usuarioid,
            ':senha' => $senha
        ]);
        
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return [
            'data' => [$result],
            'resulta' => $result['resulta'] ?? 1
        ];
    }
    
    /**
     * Executa procedure Ppst_Documentacao
     */
    private function executarPpstDocumentacao($dbh, $parameters)
    {
        $ano = $parameters['@pst_ano'] ?? null;
        $numero = $parameters['@pst_numero'] ?? null;
        $status = $parameters['@pst_status'] ?? null;
        $prodstatus = $parameters['@pst_prodstatus'] ?? null;
        $de = $parameters['@pst_de'] ?? null;
        $revisadopor = $parameters['@pst_revisadopor'] ?? null;
        $doc_data = $parameters['@pst_doc_data'] ?? null;
        $observacao = $parameters['@pst_observacao'] ?? null;
        $cdusuario = $parameters['@cdusuario'] ?? null;
        $senha = $parameters['@senha'] ?? null;
        
        if (!$numero || !$de || !$revisadopor || !$doc_data || !$cdusuario || !$senha) {
            throw new \Exception('Parâmetros obrigatórios não fornecidos');
        }
        
        // Validar senha primeiro
        $senhaValida = $this->validarSenhaUsuario($cdusuario, $senha);
        if (!$senhaValida) {
            return [
                'error' => 'Senha inválida',
                'RETURN_VALUE' => 1
            ];
        }
        
        // Executar procedure de documentação
        $sql = "EXEC sgcr.crsa.Ppst_Documentacao 
                @pst_ano = :ano,
                @pst_numero = :numero,
                @pst_status = :status,
                @pst_prodstatus = :prodstatus,
                @pst_de = :de,
                @pst_revisadopor = :revisadopor,
                @pst_doc_data = :doc_data,
                @pst_observacao = :observacao,
                @cdusuario = :cdusuario,
                @senha = :senha";
        
        $stmt = $dbh->prepare($sql);
        $stmt->execute([
            ':ano' => $ano,
            ':numero' => $numero,
            ':status' => $status,
            ':prodstatus' => $prodstatus,
            ':de' => $de,
            ':revisadopor' => $revisadopor,
            ':doc_data' => $doc_data,
            ':observacao' => $observacao,
            ':cdusuario' => $cdusuario,
            ':senha' => $senha
        ]);
        
        return [
            'data' => ['success' => true],
            'RETURN_VALUE' => 0
        ];
    }
    
    /**
     * Valida senha do usuário
     */
    private function validarSenhaUsuario($usuarioid, $senha)
    {
        try {
            $dbh = DB::connection()->getPdo();
            $sql = "EXEC sgcr.crsa.P1110_confsenha @p1110_usuarioid = :usuarioid, @p1110_senha = :senha";
            $stmt = $dbh->prepare($sql);
            $stmt->execute([
                ':usuarioid' => $usuarioid,
                ':senha' => $senha
            ]);
            
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return isset($result['resulta']) && $result['resulta'] == 0;
            
        } catch (\Exception $e) {
            Log::error('PSP-PC: Erro ao validar senha: ' . $e->getMessage());
            return false;
        }
    }
}
