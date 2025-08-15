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
            $request->validate([
                'txtLotes' => 'required|string|max:255',
                'cmbprod' => 'required|string',
                'selTipProc' => 'required|string',
                'txPeriodoINI' => 'required|date',
                'txPeriodoATE' => 'required|date|after:txPeriodoINI',
                'selTarefas' => 'required|string',
                'txDataExecucao' => 'required|date',
                'txtdisponiveis' => 'required|string',
                'txtSenha' => 'required|string|max:6'
            ]);

            // Valida a senha do usuário
            $usuario = auth()->user()->cdusuario;
            $senha = $request->txtSenha;
            $validacao = $this->sgfpService->validaSenha($usuario, $senha);
            
            if ($validacao === "") {
                $dados = [
                    'lote' => $request->txtLotes,
                    'produto' => $request->cmbprod,
                    'tipoProcesso' => $request->selTipProc,
                    'dataInicio' => $request->txPeriodoINI,
                    'dataFim' => $request->txPeriodoATE,
                    'tarefa' => $request->selTarefas,
                    'dataExecucao' => $request->txDataExecucao,
                    'usuarios' => $request->txtdisponiveis,
                    'usuario' => $usuario
                ];
                
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
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ]);
        }
    }

    /**
     * Atualiza uma escala semanal existente
     */
    public function update(Request $request)
    {
        try {
            $request->validate([
                'nr_ID' => 'required|integer',
                'txtLotes' => 'required|string|max:255',
                'cmbprod' => 'required|string',
                'selTipProc' => 'required|string',
                'txPeriodoINI' => 'required|date',
                'txPeriodoATE' => 'required|date|after:txPeriodoINI',
                'selTarefas' => 'required|string',
                'txDataExecucao' => 'required|date',
                'txtdisponiveis' => 'required|string',
                'txtSenha' => 'required|string|max:6'
            ]);

            // Valida a senha do usuário
            $usuario = auth()->user()->cdusuario;
            $senha = $request->txtSenha;
            $validacao = $this->sgfpService->validaSenha($usuario, $senha);
            
            if ($validacao === "") {
                $dados = [
                    'id' => $request->nr_ID,
                    'lote' => $request->txtLotes,
                    'produto' => $request->cmbprod,
                    'tipoProcesso' => $request->selTipProc,
                    'dataInicio' => $request->txPeriodoINI,
                    'dataFim' => $request->txPeriodoATE,
                    'tarefa' => $request->selTarefas,
                    'dataExecucao' => $request->txDataExecucao,
                    'usuarios' => $request->txtdisponiveis,
                    'usuario' => $usuario
                ];
                
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
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ]);
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
     * Retorna dados da escala semanal para DataTables
     */
    public function getData()
    {
        try {
            $escalas = $this->sgfpService->retornaEscalaSemanal();
            return response()->json(['data' => $escalas]);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar dados da escala semanal: ' . $e->getMessage());
            return response()->json(['data' => []]);
        }
    }
}
