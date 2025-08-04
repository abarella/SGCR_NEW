<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fpe;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class FpeController extends Controller
{
    public function index()
    {
        $fpes = Fpe::with(['responsavel', 'aprovadoPor', 'rejeitadoPor'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('fpe.index', compact('fpes'));
    }

    public function create()
    {
        $responsaveis = User::all();
        return view('fpe.create', compact('responsaveis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'data_embalagem' => 'required|date',
            'produto' => 'required|string|max:255',
            'lote' => 'required|string|max:255',
            'quantidade' => 'required|numeric|min:0',
            'unidade' => 'required|string|max:50',
            'observacoes' => 'nullable|string',
            'responsavel' => 'nullable|exists:users,id'
        ]);

        $data = $request->all();
        $data['status'] = 'pendente';
        if (empty($data['responsavel'])) {
            $data['responsavel'] = null;
        }
        $data['quantidade'] = (float) $data['quantidade'];
        $fpe = Fpe::create($data);

        return redirect()->route('fpe.index')
            ->with('success', 'Folha de Produção Embalado criada com sucesso!');
    }

    public function show(Fpe $fpe)
    {
        $fpe = $fpe->load(['responsavel', 'aprovadoPor', 'rejeitadoPor']);
        return view('fpe.show', compact('fpe'));
    }

    public function edit(Fpe $fpe)
    {
        $responsaveis = User::all();
        return view('fpe.edit', compact('fpe', 'responsaveis'));
    }

    public function update(Request $request, Fpe $fpe)
    {
        $request->validate([
            'data_embalagem' => 'required|date',
            'produto' => 'required|string|max:255',
            'lote' => 'required|string|max:255',
            'quantidade' => 'required|numeric|min:0',
            'unidade' => 'required|string|max:50',
            'observacoes' => 'nullable|string',
            'responsavel' => 'nullable|exists:users,id'
        ]);
        $data = $request->all();
        if (empty($data['responsavel'])) {
            $data['responsavel'] = null;
        }
        $data['quantidade'] = (float) $data['quantidade'];
        $fpe->update($data);
        return redirect()->route('fpe.index')
            ->with('success', 'Folha de Produção Embalado atualizada com sucesso!');
    }

    public function destroy(Fpe $fpe)
    {
        $fpe->delete();
        return redirect()->route('fpe.index')
            ->with('success', 'Folha de Produção Embalado excluída com sucesso!');
    }

    public function embalar(Fpe $fpe)
    {
        $fpe->update([
            'status' => 'embalado'
        ]);
        return redirect()->back()
            ->with('success', 'Folha marcada como Embalada!');
    }

    public function aprovar(Fpe $fpe)
    {
        $fpe->update([
            'status' => 'aprovado',
            'aprovado_por' => Auth::id(),
            'data_aprovacao' => now()
        ]);
        return redirect()->back()
            ->with('success', 'Folha de Produção Embalado aprovada!');
    }

    public function rejeitar(Request $request, Fpe $fpe)
    {
        $request->validate([
            'motivo_rejeicao' => 'required|string'
        ]);
        $fpe->update([
            'status' => 'rejeitado',
            'rejeitado_por' => Auth::id(),
            'data_rejeicao' => now(),
            'motivo_rejeicao' => $request->motivo_rejeicao
        ]);
        return redirect()->back()
            ->with('success', 'Folha de Produção Embalado rejeitada!');
    }
}
