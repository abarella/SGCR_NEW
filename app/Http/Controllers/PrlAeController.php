<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PrlAe;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PrlAeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $alteracoes = PrlAe::with(['responsavel', 'aprovadoPor', 'rejeitadoPor'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('prl-ae.index', compact('alteracoes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $responsaveis = User::all();
        return view('prl-ae.create', compact('responsaveis'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'data_alteracao' => 'required|date',
            'produto' => 'required|string|max:255',
            'lote' => 'required|string|max:255',
            'quantidade_atual' => 'required|numeric|min:0',
            'quantidade_nova' => 'required|numeric|min:0',
            'unidade' => 'required|string|max:50',
            'tipo_alteracao' => 'required|in:entrada,saida,ajuste',
            'motivo' => 'required|string',
            'observacoes' => 'nullable|string',
            'responsavel' => 'nullable|exists:users,id'
        ]);

        $data = $request->all();
        $data['status'] = 'pendente';
        
        // Garantir que responsavel seja um ID válido ou null
        if (empty($data['responsavel'])) {
            $data['responsavel'] = null;
        }
        
        // Converter valores numéricos
        $data['quantidade_atual'] = (float) $data['quantidade_atual'];
        $data['quantidade_nova'] = (float) $data['quantidade_nova'];
        
        $alteracao = PrlAe::create($data);

        return redirect()->route('prl-ae.index')
            ->with('success', 'Alteração de Estoque criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(PrlAe $prlAe)
    {
        $alteracao = $prlAe->load(['responsavel', 'aprovadoPor', 'rejeitadoPor']);
        return view('prl-ae.show', compact('alteracao'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PrlAe $prlAe)
    {
        $alteracao = $prlAe;
        $responsaveis = User::all();
        return view('prl-ae.edit', compact('alteracao', 'responsaveis'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PrlAe $prlAe)
    {
        $request->validate([
            'data_alteracao' => 'required|date',
            'produto' => 'required|string|max:255',
            'lote' => 'required|string|max:255',
            'quantidade_atual' => 'required|numeric|min:0',
            'quantidade_nova' => 'required|numeric|min:0',
            'unidade' => 'required|string|max:50',
            'tipo_alteracao' => 'required|in:entrada,saida,ajuste',
            'motivo' => 'required|string',
            'observacoes' => 'nullable|string',
            'responsavel' => 'nullable|exists:users,id'
        ]);

        $data = $request->all();
        
        // Garantir que responsavel seja um ID válido ou null
        if (empty($data['responsavel'])) {
            $data['responsavel'] = null;
        }
        
        // Converter valores numéricos
        $data['quantidade_atual'] = (float) $data['quantidade_atual'];
        $data['quantidade_nova'] = (float) $data['quantidade_nova'];
        
        $prlAe->update($data);

        return redirect()->route('prl-ae.index')
            ->with('success', 'Alteração de Estoque atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PrlAe $prlAe)
    {
        $prlAe->delete();

        return redirect()->route('prl-ae.index')
            ->with('success', 'Alteração de Estoque excluída com sucesso!');
    }

    /**
     * Aprovar alteração
     */
    public function aprovarAlteracao(PrlAe $prlAe)
    {
        $prlAe->update([
            'status' => 'aprovado',
            'aprovado_por' => Auth::id(),
            'data_aprovacao' => now()
        ]);

        return redirect()->back()
            ->with('success', 'Alteração de Estoque aprovada com sucesso!');
    }

    /**
     * Rejeitar alteração
     */
    public function rejeitarAlteracao(Request $request, PrlAe $prlAe)
    {
        $request->validate([
            'motivo_rejeicao' => 'required|string'
        ]);

        $prlAe->update([
            'status' => 'rejeitado',
            'rejeitado_por' => Auth::id(),
            'data_rejeicao' => now(),
            'motivo_rejeicao' => $request->motivo_rejeicao
        ]);

        return redirect()->back()
            ->with('success', 'Alteração de Estoque rejeitada com sucesso!');
    }
}
