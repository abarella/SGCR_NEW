<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\PspRmService;
use Illuminate\Support\Facades\DB; // Added DB facade

class PspRmController extends Controller
{
    protected $pspRmService;

    public function __construct(PspRmService $pspRmService)
    {
        $this->pspRmService = $pspRmService;
        $this->middleware('auth');
    }

    /**
     * Exibe a página principal de R.D. & M.M.
     */
    public function index()
    {
        return view('psp-rm.index');
    }

    /**
     * Método de teste para verificar se a rota está funcionando
     */
    public function test()
    {
        try {
            $user = Auth::user();
            
            return response()->json([
                'success' => true,
                'message' => 'Rota funcionando',
                'user' => [
                    'id' => $user->id,
                    'cdusuario' => $user->cdusuario ?? 'N/A',
                    'name' => $user->name ?? 'N/A'
                ],
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro: ' . $e->getMessage(),
                'timestamp' => now()->toISOString()
            ], 500);
        }
    }

    /**
     * Testa a conexão com o banco de dados
     */
    public function testDatabase()
    {
        try {
            // Testar conexão básica
            DB::connection()->getPdo();
            
            // Testar se conseguimos executar uma query simples
            $result = DB::select('SELECT 1 as test');
            
            // Testar se conseguimos acessar a procedure
            $dbh = DB::connection()->getPdo();
            $sql = "SET NOCOUNT ON; SELECT 'Teste de conexão' as status";
            $stmt = $dbh->prepare($sql);
            $stmt->execute();
            $testResult = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            return response()->json([
                'success' => true,
                'message' => 'Conexão com banco funcionando',
                'database' => [
                    'connection' => 'OK',
                    'simple_query' => 'OK',
                    'procedure_test' => 'OK',
                    'driver' => DB::connection()->getDriverName(),
                    'database' => DB::connection()->getDatabaseName()
                ],
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro na conexão com banco: ' . $e->getMessage(),
                'error_details' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ],
                'timestamp' => now()->toISOString()
            ], 500);
        }
    }

    /**
     * Lista produtos por categoria e lote
     */
    public function listarProdutos(Request $request)
    {
        try {
            $categoria = $request->input('categoria', 3); // Default para Moléculas Marcadas
            $lote = $request->input('lote');

            if (empty($lote)) {
                return response()->json([
                    'success' => false,
                    'message' => 'INFORME O LOTE!'
                ], 400);
            }

            $produtos = $this->pspRmService->listarProdutos($categoria, $lote);

            return response()->json([
                'success' => true,
                'data' => $produtos
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }

    /**
     * Atualiza número de produções
     */
    public function atualizarProducoes(Request $request)
    {
        try {
            $request->validate([
                'produto' => 'required|string|max:10',
                'lote' => 'required|string',
                'categoria' => 'required|integer',
                'num_producoes' => 'required|integer|min:0',
                'senha' => 'required|string|max:6'
            ]);

            // Validar senha do usuário
            if (!$this->pspRmService->validarSenha(Auth::user()->cdusuario, $request->senha)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Senha não confere ou Senha Inválida'
                ], 400);
            }

            $resultado = $this->pspRmService->atualizarProducoes(
                $request->produto,
                $request->lote,
                $request->categoria,
                $request->num_producoes,
                $request->senha
            );

            return response()->json($resultado);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Erro no controller atualizarProducoes: ' . $e->getMessage(), [
                'request' => $request->all(),
                'user' => Auth::user()->cdusuario ?? 'N/A'
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Abre modal de calibração
     */
    public function abrirCalibracao(Request $request)
    {
        try {
            $request->validate([
                'produto' => 'required|string',
                'lote' => 'required|string',
                'categoria' => 'required|integer'
            ]);

            $dadosCalibracao = $this->pspRmService->obterDadosCalibracao(
                $request->produto,
                $request->lote,
                $request->categoria
            );

            return response()->json([
                'success' => true,
                'data' => $dadosCalibracao
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }

    /**
     * Atualiza dados de calibração
     */
    public function atualizarCalibracao(Request $request)
    {
        try {
            $request->validate([
                'produto' => 'required|string',
                'lote' => 'required|string',
                'categoria' => 'required|integer',
                'dados_calibracao' => 'required|array',
                'senha' => 'required|string'
            ]);

            $resultado = $this->pspRmService->atualizarCalibracao(
                $request->produto,
                $request->lote,
                $request->categoria,
                $request->dados_calibracao,
                $request->senha
            );

            return response()->json([
                'success' => true,
                'message' => 'Calibração atualizada com sucesso',
                'data' => $resultado
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }


}
