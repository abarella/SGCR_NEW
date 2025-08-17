<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\PspRmService;
use Illuminate\Support\Facades\Log;

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
     * Página de teste para verificar o layout
     */
    public function test()
    {
        return view('psp-rm.test');
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
            Log::error('Erro ao listar produtos RDMM: ' . $e->getMessage());
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
                'produto' => 'required|string',
                'lote' => 'required|string',
                'categoria' => 'required|integer',
                'num_producoes' => 'required|integer|min:0',
                'senha' => 'required|string'
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
                $request->num_producoes
            );

            return response()->json([
                'success' => true,
                'message' => 'Produções atualizadas com sucesso',
                'data' => $resultado
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao atualizar produções: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
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
            Log::error('Erro ao abrir calibração: ' . $e->getMessage());
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

            // Validar senha do usuário
            if (!$this->pspRmService->validarSenha(Auth::user()->cdusuario, $request->senha)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Senha não confere ou Senha Inválida'
                ], 400);
            }

            $resultado = $this->pspRmService->atualizarCalibracao(
                $request->produto,
                $request->lote,
                $request->categoria,
                $request->dados_calibracao
            );

            return response()->json([
                'success' => true,
                'message' => 'Calibração atualizada com sucesso',
                'data' => $resultado
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao atualizar calibração: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }

    /**
     * Testa a procedure para debug
     */
    public function testarProcedure(Request $request)
    {
        try {
            $categoria = $request->get('categoria', 1);
            $lote = $request->get('lote', '001');

            $result = $this->pspRmService->testarProcedure($categoria, $lote);

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Erro ao testar procedure: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
