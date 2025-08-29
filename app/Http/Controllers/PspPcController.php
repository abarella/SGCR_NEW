<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Services\PspPsService;
use App\Services\GlobalService;

class PspPcController extends Controller
{
    protected $pspPsService;
    protected $globalService;

    public function __construct(PspPsService $pspPsService, GlobalService $globalService)
    {
        $this->pspPsService = $pspPsService;
        $this->globalService = $globalService;
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
            Log::info('PSP-PC: Carregando lista de pastas via procedure PPST_LISTA_NAOCONCLUIDOS', $request->all());
            
            $produto = $request->get('produto', '');
            $pasta = $request->get('pasta', '');
            $pagina = $request->get('pagina', 1);
            $porPagina = 50;
            
            // Usar a procedure correta PPST_LISTA_NAOCONCLUIDOS
            $dbh = DB::connection()->getPdo();
            $sql = "SET NOCOUNT ON; EXEC sgcr.crsa.PPST_LISTA_NAOCONCLUIDOS default, 1, default, default";
            
            Log::info('PSP-PC: Executando procedure', ['sql' => $sql]);
            
            $stmt = $dbh->query($sql);
            
            // Obter resultados da procedure
            $xmlResult = "";
            
            // A procedure retorna XML, vamos capturar
            while ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
                $xmlResult .= $row[0];
            }
            
            Log::info('PSP-PC: XML retornado pela procedure', ['xml' => $xmlResult]);
            
            if (empty($xmlResult)) {
                Log::warning('PSP-PC: Procedure PPST_LISTA_NAOCONCLUIDOS retornou vazio');
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'total' => 0,
                    'pagina' => $pagina,
                    'porPagina' => $porPagina,
                    'totalPaginas' => 0
                ]);
            }
            
            // Processar XML retornado pela procedure
            $xmlResult = trim($xmlResult);
            
            // Verificar se o XML já tem tag raiz
            if (!str_starts_with($xmlResult, "<root>")) {
                $xmlResult = "<root>$xmlResult</root>";
            }
            
            Log::info('PSP-PC: XML antes do processamento', ['xml' => $xmlResult]);
            
            $xml = simplexml_load_string($xmlResult);
            if (!$xml) {
                Log::error('PSP-PC: Erro ao processar XML da procedure', ['xml' => $xmlResult]);
                throw new \Exception('Erro ao processar XML retornado pela procedure');
            }
            
            Log::info('PSP-PC: XML processado com sucesso', ['xml_structure' => $xml->asXML()]);
            
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
                            'pst_status' => (string)($row['pst_status'] ?? '')
                        ];
                        $pastas[] = $pastaObj;
                    } catch (\Exception $e) {
                        Log::error('PSP-PC: Erro ao processar linha do XML', ['row' => $row->asXML(), 'error' => $e->getMessage()]);
                        continue;
                    }
                }
            } else {
                Log::warning('PSP-PC: XML não contém elementos row');
            }
            
            Log::info('PSP-PC: Pastas processadas da procedure', ['count' => count($pastas), 'primeira_pasta' => $pastas[0] ?? 'N/A']);
            
            // Filtrar por produto se especificado
            if (!empty($produto)) {
                // Log para debug dos valores de produto
                $produtosUnicos = array_unique(array_map(function($p) { return trim($p->pst_produto510); }, $pastas));
                Log::info('PSP-PC: Produtos únicos encontrados', ['produtos' => array_values($produtosUnicos)]);
                
                $pastas = array_filter($pastas, function($pasta) use ($produto) {
                    $produtoPasta = trim($pasta->pst_produto510);
                    return isset($pasta->pst_produto510) && $produtoPasta == $produto;
                });
                Log::info('PSP-PC: Filtro por produto aplicado', ['produto' => $produto, 'count_apos_filtro' => count($pastas)]);
            }
            
            // Filtrar por número da pasta se especificado
            if (!empty($pasta)) {
                $pastas = array_filter($pastas, function($p) use ($pasta) {
                    return isset($p->pst_numero) && stripos((string)$p->pst_numero, (string)$pasta) !== false;
                });
                Log::info('PSP-PC: Filtro por pasta aplicado', ['pasta' => $pasta, 'count_apos_filtro' => count($pastas)]);
            }
            
            // Reindexar array após filtros
            $pastas = array_values($pastas);
            
            // Paginação manual
            $total = count($pastas);
            $pastas = array_slice($pastas, ($pagina - 1) * $porPagina, $porPagina);
            
            // Adicionar status calculado
            $pastas = array_map(function ($pasta) {
                $pasta->status = $this->calcularStatus($pasta);
                $pasta->status_producao = $this->calcularStatusProducao($pasta);
                return $pasta;
            }, $pastas);
            
            Log::info('PSP-PC: Lista carregada com sucesso via procedure PPST_LISTA_NAOCONCLUIDOS', ['total' => $total, 'pagina' => $pagina, 'retornando' => count($pastas)]);
            
            return response()->json([
                'success' => true,
                'data' => $pastas,
                'total' => $total,
                'pagina' => $pagina,
                'porPagina' => $porPagina,
                'totalPaginas' => ceil($total / $porPagina)
            ]);
            
        } catch (\Exception $e) {
            Log::error('PSP-PC: Erro ao carregar lista via procedure PPST_LISTA_NAOCONCLUIDOS: ' . $e->getMessage());
            Log::error('PSP-PC: Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar lista de pastas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retorna a lista de produtos para o combo usando a mesma procedure da lista
     */
    public function produtos()
    {
        try {
            Log::info('PSP-PC: Carregando lista de produtos via procedure PPST_LISTA_NAOCONCLUIDOS');
            
            // Usar a mesma procedure da lista para garantir compatibilidade
            $dbh = DB::connection()->getPdo();
            $sql = "SET NOCOUNT ON; EXEC sgcr.crsa.PPST_LISTA_NAOCONCLUIDOS default, 1, default, default";
            
            $stmt = $dbh->query($sql);
            
            // Obter resultados da procedure
            $xmlResult = "";
            while ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
                $xmlResult .= $row[0];
            }
            
            if (empty($xmlResult)) {
                Log::warning('PSP-PC: Procedure PPST_LISTA_NAOCONCLUIDOS retornou vazio para produtos');
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'message' => 'Nenhum produto encontrado'
                ]);
            }
            
            // Processar XML retornado pela procedure
            $xmlResult = trim($xmlResult);
            if (!str_starts_with($xmlResult, "<root>")) {
                $xmlResult = "<root>$xmlResult</root>";
            }
            
            $xml = simplexml_load_string($xmlResult);
            if (!$xml) {
                Log::error('PSP-PC: Erro ao processar XML da procedure para produtos', ['xml' => $xmlResult]);
                throw new \Exception('Erro ao processar XML retornado pela procedure');
            }
            
            // Extrair produtos únicos da procedure
            $produtosUnicos = [];
            if (isset($xml->row)) {
                foreach ($xml->row as $row) {
                    $codigo = trim((string)($row['pst_produto510'] ?? ''));
                    $nome = (string)($row['nome_comercial'] ?? '');
                    
                    if (!empty($codigo) && !isset($produtosUnicos[$codigo])) {
                        $produtosUnicos[$codigo] = (object) [
                            'codigo' => $codigo,
                            'nome_comercial' => $nome
                        ];
                    }
                }
            }
            
            $produtos = array_values($produtosUnicos);
            
            if (empty($produtos)) {
                Log::warning('PSP-PC: Nenhum produto encontrado na procedure');
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'message' => 'Nenhum produto encontrado'
                ]);
            }
            
            Log::info('PSP-PC: Produtos carregados com sucesso via procedure', ['total' => count($produtos)]);
            
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
     * Abre modal de documentação
     */
    public function documentacao(Request $request)
    {
        try {
            $pasta = $request->get('pasta');
            Log::info('PSP-PC: Abrindo modal de documentação para pasta', ['pasta' => $pasta]);
            
            // Usar a procedure PPST_LISTA2 para obter dados da pasta
            $dadosPasta = $this->pspPsService->getPasta($pasta);
            
            if (empty($dadosPasta)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pasta não encontrada'
                ], 404);
            }
            
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
            
            // Usar a procedure PPST_LISTA3 para obter ocorrências da pasta
            $dadosPasta = $this->pspPsService->getPasta($pasta, 'O'); // Tipo 'O' para ocorrências
            
            if (empty($dadosPasta)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pasta não encontrada'
                ], 404);
            }
            
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
            
            // Usar a procedure PPST_LISTA2 para obter dados básicos da pasta
            $dadosPasta = $this->pspPsService->getPasta($pasta);
            
            if (empty($dadosPasta)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pasta não encontrada'
                ], 404);
            }
            
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
}
