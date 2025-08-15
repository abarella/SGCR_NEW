<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SGFPService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

/**
 * Controller para gerenciar Escala de Tarefas
 * Migrado de: refatorar/modEscala_Tarefas.php
 */
class EscTrController extends Controller
{
    protected $sgfpService;

    public function __construct(SGFPService $sgfpService)
    {
        $this->middleware('auth');
        $this->sgfpService = $sgfpService;
    }

    /**
     * Exibe a listagem das tarefas da escala
     */
    public function index()
    {
        try {
            $tarefas = $this->sgfpService->retornaEscalaTarefas();
            return view('esc-tr.index', [
                'tarefas' => $tarefas,
                'title' => 'Escala de Tarefas'
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao carregar página de escala de tarefas: ' . $e->getMessage());
            return back()->with('error', 'Erro ao carregar tarefas da escala');
        }
    }

    /**
     * Insere uma nova tarefa
     */
    public function store(Request $request)
    {
        
        try {
            $request->validate([
                'txtNomeTarefa' => 'required|string|max:255',
                'txtSenha' => 'required|string|max:6'
            ]);

            // Valida a senha do usuário
            $usuario = auth()->user()->cdusuario;
            $senha = $request->txtSenha;
            $validacao = $this->sgfpService->validaSenha($usuario, $senha);
          
            
            
            if ($validacao === "") {
                $resultado = $this->sgfpService->inserirEscalaTarefa($request->txtNomeTarefa, $usuario);
                
                if ($resultado) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Tarefa inserida com sucesso!'
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Erro ao inserir tarefa'
                    ]);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Senha inválida'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Erro ao inserir tarefa: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ]);
        }
    }

    /**
     * Atualiza uma tarefa existente
     */
    public function update(Request $request)
    {
        try {
            $request->validate([
                'nr_ID' => 'required|integer',
                'm_txtNome' => 'required|string|max:255',
                'm_txtSenha' => 'required|string|max:6'
            ]);

            // Valida a senha do usuário
            $usuario = auth()->user()->cdusuario;
            $senha = $request->m_txtSenha;
            
            $validacao = $this->sgfpService->validaSenha($usuario, $senha);
            
            if ($validacao === "") {
                $resultado = $this->sgfpService->atualizarEscalaTarefa(
                    $request->nr_ID,
                    $request->m_txtNome,
                    $usuario
                );
                
                if ($resultado) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Tarefa atualizada com sucesso!'
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Erro ao atualizar tarefa'
                    ]);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Senha inválida'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar tarefa: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ]);
        }
    }

    /**
     * Remove uma tarefa
     */
    public function destroy(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|integer',
                'txtSenha' => 'required|string|max:6'
            ]);

            // Valida a senha do usuário
            $usuario = auth()->user()->cdusuario;
            $senha = $request->txtSenha;
            
            $validacao = $this->sgfpService->validaSenha($usuario, $senha);
            
            if ($validacao === "") {
                $resultado = $this->sgfpService->excluirEscalaTarefa($request->id, $usuario);
                
                if ($resultado) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Tarefa excluída com sucesso!'
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Erro ao excluir tarefa'
                    ]);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Senha inválida'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Erro ao excluir tarefa: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ]);
        }
    }

    /**
     * Retorna lista de tarefas em formato JSON para DataTables
     */
    public function getData()
    {
        try {
            $tarefas = $this->sgfpService->retornaEscalaTarefasJson();
            
            return response()->json([
                'data' => $tarefas
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar dados das tarefas: ' . $e->getMessage());
            return response()->json([
                'data' => [],
                'error' => 'Erro ao carregar dados'
            ]);
        }
    }
}
