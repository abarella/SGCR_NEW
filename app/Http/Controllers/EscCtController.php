<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SGFPService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

/**
 * Controller para gerenciar Escala Semanal
 * Migrado de: refatorar/modEscala_Semanal.php
 */
class EscCtController extends Controller
{
    protected $sgfpService;

    public function __construct(SGFPService $sgfpService)
    {
        $this->middleware('auth');
        $this->sgfpService = $sgfpService;
    }

    /**
     * Exibe a página principal da escala semanal
     */
    public function index()
    {
        try {
            $produtos = $this->sgfpService->retornaCMBProdutos();
            $tiposProcesso = $this->sgfpService->retornaEscalaTipoProcesso();
            $tarefas = $this->sgfpService->retornaEscalaTarefasSenanal();
            $usuarios = $this->sgfpService->retornaListaUsuariosCMB();
            $escalas = $this->sgfpService->retornaEscalaSemanal();

            return view('esc-ct.index', [
                'produtos' => $produtos,
                'tiposProcesso' => $tiposProcesso,
                'tarefas' => $tarefas,
                'usuarios' => $usuarios,
                'escalas' => $escalas,
                'title' => 'Escala Semanal'
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao carregar página de escala semanal: ' . $e->getMessage());
            return back()->with('error', 'Erro ao carregar dados da escala semanal');
        }
    }

    /**
     * Insere uma nova escala semanal
     */
    public function store(Request $request)
    {
        try {
            // Log dos dados recebidos para debug
            Log::info('Dados recebidos no store:', $request->all());
            
            // Validação com tratamento de erro mais detalhado
            try {
                $validated = $request->validate([
                    'txtLotes' => 'required|string|max:255',
                    'cmbprod' => 'required|string',
                    'selTipProc' => 'required|string',
                    'txPeriodoINI' => 'required|date',
                    'txPeriodoATE' => 'required|date|after:txPeriodoINI',
                    'selTarefas' => 'required|string',
                    'txDataExecucao' => 'nullable|date',
                    'txtdisponiveis' => 'required|string',
                    'txtSenha' => 'required|string|max:6'
                ]);
                
                Log::info('Dados validados com sucesso:', $validated);
            } catch (\Illuminate\Validation\ValidationException $e) {
                Log::error('Erro de validação no store:', [
                    'errors' => $e->errors(),
                    'dados_recebidos' => $request->all()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Erro de validação: ' . implode(', ', array_map(function($field, $errors) {
                        return $field . ': ' . implode(', ', $errors);
                    }, array_keys($e->errors()), $e->errors()))
                ], 422);
            }

            // Valida a senha do usuário
            $usuario = auth()->user()->cdusuario;
            $senha = $request->txtSenha;
            $validacao = $this->sgfpService->validaSenha($usuario, $senha);

            if ($validacao === "") {
                $dados = [
                    'lotes' => $request->txtLotes,
                    'produto' => $request->cmbprod,
                    'tipoProcesso' => $request->selTipProc,
                    'dataInicio' => $request->txPeriodoINI,
                    'dataFim' => $request->txPeriodoATE,
                    'tarefa' => $request->selTarefas,
                    'dataExecucao' => $request->txDataExecucao,
                    'usuarios' => $request->txtdisponiveis,
                    'usuario' => $usuario
                ];

                Log::info('Dados preparados para inserção:', $dados);

                $resultado = $this->sgfpService->inserirEscalaSemanal($dados);

                if ($resultado) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Escala semanal inserida com sucesso!'
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Erro ao inserir escala semanal'
                    ]);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Senha inválida'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Erro ao inserir escala semanal: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Atualiza uma escala semanal existente
     */
    public function update(Request $request)
    {
        try {
            // Log dos dados recebidos para debug
            Log::info('Dados recebidos no update:', $request->all());
            
            // Validação com tratamento de erro mais detalhado
            try {
                $validated = $request->validate([
                    'nr_ID' => 'required|integer',
                    'txtLotes' => 'required|string|max:255',
                    'cmbprod' => 'required|string',
                    'selTipProc' => 'required|string',
                    'txPeriodoINI' => 'required|date',
                    'txPeriodoATE' => 'required|date|after:txPeriodoINI',
                    'selTarefas' => 'required|string',
                    'txDataExecucao' => 'nullable|date',
                    'txtdisponiveis' => 'required|string',
                    'txtSenha' => 'required|string|max:6'
                ]);
                
                Log::info('Dados validados com sucesso:', $validated);
            } catch (\Illuminate\Validation\ValidationException $e) {
                Log::error('Erro de validação no update:', [
                    'errors' => $e->errors(),
                    'dados_recebidos' => $request->all()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Erro de validação: ' . implode(', ', array_map(function($field, $errors) {
                        return $field . ': ' . implode(', ', $errors);
                    }, array_keys($e->errors()), $e->errors()))
                ], 422);
            }

            // Valida a senha do usuário
            $usuario = auth()->user()->cdusuario;
            $senha = $request->txtSenha;
            $validacao = $this->sgfpService->validaSenha($usuario, $senha);

            if ($validacao === "") {
                $dados = [
                    'id' => $request->nr_ID,
                    'lotes' => $request->txtLotes,
                    'produto' => $request->cmbprod,
                    'tipoProcesso' => $request->selTipProc,
                    'dataInicio' => $request->txPeriodoINI,
                    'dataFim' => $request->txPeriodoATE,
                    'tarefa' => $request->selTarefas,
                    'dataExecucao' => $request->txDataExecucao,
                    'usuarios' => $request->txtdisponiveis,
                    'usuario' => $usuario
                ];

                Log::info('Dados preparados para atualização:', $dados);

                $resultado = $this->sgfpService->atualizarEscalaSemanal($dados);

                if ($resultado) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Escala semanal atualizada com sucesso!'
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Erro ao atualizar escala semanal'
                    ]);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Senha inválida'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar escala semanal: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exclui uma escala semanal
     */
    public function destroy(Request $request)
    {
        try {
            $request->validate([
                'nr_ID' => 'required|integer',
                'txtSenha' => 'required|string|max:6'
            ]);

            // Valida a senha do usuário
            $usuario = auth()->user()->cdusuario;
            $senha = $request->txtSenha;
            $validacao = $this->sgfpService->validaSenha($usuario, $senha);

            if ($validacao === "") {
                $resultado = $this->sgfpService->excluirEscalaSemanal($request->nr_ID, $usuario);

                if ($resultado) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Escala semanal excluída com sucesso!'
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Erro ao excluir escala semanal'
                    ]);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Senha inválida'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Erro ao excluir escala semanal: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ]);
        }
    }

    /**
     * Duplica a última escala semanal
     */
    public function duplicar(Request $request)
    {
        try {
            $request->validate([
                'txtSenha' => 'required|string|max:6'
            ]);

            // Valida a senha do usuário
            $usuario = auth()->user()->cdusuario;
            $senha = $request->txtSenha;
            $validacao = $this->sgfpService->validaSenha($usuario, $senha);

            if ($validacao === "") {
                $resultado = $this->sgfpService->duplicarEscalaSemanal($usuario);

                if ($resultado) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Escala semanal duplicada com sucesso!'
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Erro ao duplicar escala semanal'
                    ]);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Senha inválida'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Erro ao duplicar escala semanal: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ]);
        }
    }

    /**
     * Retorna usuários associados para um lote e tarefa específicos
     */
    public function getUsuariosAssociados(Request $request)
    {
        try {
            $request->validate([
                'lote' => 'required|string',
                'tarefa' => 'required|string'
            ]);

            $usuarios = $this->sgfpService->retornaUsuariosAssocCMB($request->lote, $request->tarefa);

            return response()->json([
                'success' => true,
                'usuarios' => $usuarios
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar usuários associados: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar usuários associados'
            ]);
        }
    }

    /**
     * Retorna dados da escala semanal para atualização do grid
     */
    public function getData()
    {
        try {
            $escalas = $this->sgfpService->retornaEscalaSemanal();
            return response()->json([
                'success' => true,
                'data' => $escalas
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar dados da escala semanal: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'data' => '<tr><td colspan="9">Erro ao carregar dados da escala semanal</td></tr>'
            ]);
        }
    }
}
