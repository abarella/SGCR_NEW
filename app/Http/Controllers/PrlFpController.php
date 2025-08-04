<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PrlFp;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PrlFpController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $folhas = PrlFp::with(['responsavel', 'aprovadoPor', 'rejeitadoPor'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('prl-fp.index', compact('folhas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $responsaveis = User::all();
        return view('prl-fp.create', compact('responsaveis'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'data_producao' => 'required|date',
            'produto' => 'required|string|max:255',
            'lote' => 'required|string|max:255',
            'quantidade' => 'required|numeric|min:0',
            'unidade' => 'required|string|max:50',
            'observacoes' => 'nullable|string',
            'responsavel' => 'nullable|exists:users,id',
            'temperatura' => 'nullable|numeric',
            'umidade' => 'nullable|numeric',
            'ph' => 'nullable|numeric|between:0,14',
            'condicoes_especiais' => 'nullable|string'
        ]);

        $data = $request->all();
        $data['status'] = 'pendente';
        
        // Garantir que responsavel seja um ID válido ou null
        if (empty($data['responsavel'])) {
            $data['responsavel'] = null;
        }
        
        // Converter valores numéricos se existirem
        if (isset($data['temperatura']) && $data['temperatura'] !== '') {
            $data['temperatura'] = (float) $data['temperatura'];
        } else {
            $data['temperatura'] = null;
        }
        
        if (isset($data['umidade']) && $data['umidade'] !== '') {
            $data['umidade'] = (float) $data['umidade'];
        } else {
            $data['umidade'] = null;
        }
        
        if (isset($data['ph']) && $data['ph'] !== '') {
            $data['ph'] = (float) $data['ph'];
        } else {
            $data['ph'] = null;
        }
        
        $folha = PrlFp::create($data);

        return redirect()->route('prl-fp.index')
            ->with('success', 'Folha de Produção criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(PrlFp $prlFp)
    {
        $folha = $prlFp->load(['responsavel', 'aprovadoPor', 'rejeitadoPor']);
        return view('prl-fp.show', compact('folha'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PrlFp $prlFp)
    {
        $folha = $prlFp;
        $responsaveis = User::all();
        return view('prl-fp.edit', compact('folha', 'responsaveis'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PrlFp $prlFp)
    {
        $request->validate([
            'data_producao' => 'required|date',
            'produto' => 'required|string|max:255',
            'lote' => 'required|string|max:255',
            'quantidade' => 'required|numeric|min:0',
            'unidade' => 'required|string|max:50',
            'observacoes' => 'nullable|string',
            'responsavel' => 'nullable|exists:users,id',
            'temperatura' => 'nullable|numeric',
            'umidade' => 'nullable|numeric',
            'ph' => 'nullable|numeric|between:0,14',
            'condicoes_especiais' => 'nullable|string'
        ]);

        $data = $request->all();
        
        // Garantir que responsavel seja um ID válido ou null
        if (empty($data['responsavel'])) {
            $data['responsavel'] = null;
        }
        
        // Converter valores numéricos se existirem
        if (isset($data['temperatura']) && $data['temperatura'] !== '') {
            $data['temperatura'] = (float) $data['temperatura'];
        } else {
            $data['temperatura'] = null;
        }
        
        if (isset($data['umidade']) && $data['umidade'] !== '') {
            $data['umidade'] = (float) $data['umidade'];
        } else {
            $data['umidade'] = null;
        }
        
        if (isset($data['ph']) && $data['ph'] !== '') {
            $data['ph'] = (float) $data['ph'];
        } else {
            $data['ph'] = null;
        }
        
        $prlFp->update($data);

        return redirect()->route('prl-fp.index')
            ->with('success', 'Folha de Produção atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PrlFp $prlFp)
    {
        $prlFp->delete();

        return redirect()->route('prl-fp.index')
            ->with('success', 'Folha de Produção excluída com sucesso!');
    }

    /**
     * Iniciar produção
     */
    public function iniciarProducao(PrlFp $prlFp)
    {
        $prlFp->update([
            'status' => 'em_producao',
            'data_inicio' => now()
        ]);

        return redirect()->back()
            ->with('success', 'Produção iniciada com sucesso!');
    }

    /**
     * Finalizar produção
     */
    public function finalizarProducao(Request $request, PrlFp $prlFp)
    {
        $request->validate([
            'temperatura' => 'nullable|numeric',
            'umidade' => 'nullable|numeric',
            'ph' => 'nullable|numeric|between:0,14',
            'condicoes_especiais' => 'nullable|string'
        ]);

        $data = [
            'status' => 'concluido',
            'data_fim' => now(),
            'condicoes_especiais' => $request->condicoes_especiais
        ];
        
        // Converter valores numéricos
        if ($request->filled('temperatura')) {
            $data['temperatura'] = (float) $request->temperatura;
        }
        
        if ($request->filled('umidade')) {
            $data['umidade'] = (float) $request->umidade;
        }
        
        if ($request->filled('ph')) {
            $data['ph'] = (float) $request->ph;
        }
        
        $prlFp->update($data);

        return redirect()->back()
            ->with('success', 'Produção finalizada com sucesso!');
    }

    /**
     * Aprovar produção
     */
    public function aprovarProducao(PrlFp $prlFp)
    {
        $prlFp->update([
            'status' => 'aprovado',
            'aprovado_por' => Auth::id(),
            'data_aprovacao' => now()
        ]);

        return redirect()->back()
            ->with('success', 'Produção aprovada com sucesso!');
    }

    /**
     * Rejeitar produção
     */
    public function rejeitarProducao(Request $request, PrlFp $prlFp)
    {
        $request->validate([
            'motivo_rejeicao' => 'required|string'
        ]);

        $prlFp->update([
            'status' => 'rejeitado',
            'rejeitado_por' => Auth::id(),
            'data_rejeicao' => now(),
            'motivo_rejeicao' => $request->motivo_rejeicao
        ]);

        return redirect()->back()
            ->with('success', 'Produção rejeitada com sucesso!');
    }
}
