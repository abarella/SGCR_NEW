<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TrfTm;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TrfTmController extends Controller
{
    public function index()
    {
        $trfTms = TrfTm::with(['responsavel', 'concluidoPor', 'canceladoPor'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('trf-tm.index', compact('trfTms'));
    }

    public function create()
    {
        $responsaveis = User::all();
        return view('trf-tm.create', compact('responsaveis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'data_transferencia' => 'required|date',
            'material' => 'required|string|max:255',
            'quantidade' => 'required|numeric|min:0',
            'unidade' => 'required|string|max:50',
            'origem' => 'required|string|max:255',
            'destino' => 'required|string|max:255',
            'observacoes' => 'nullable|string',
            'responsavel' => 'nullable|exists:users,id'
        ]);

        $data = $request->all();
        $data['status'] = 'pendente';
        if (empty($data['responsavel'])) {
            $data['responsavel'] = null;
        }
        $data['quantidade'] = (float) $data['quantidade'];
        $trfTm = TrfTm::create($data);

        return redirect()->route('trf-tm.index')
            ->with('success', 'Transferência de Material criada com sucesso!');
    }

    public function show(TrfTm $trfTm)
    {
        $trfTm = $trfTm->load(['responsavel', 'concluidoPor', 'canceladoPor']);
        return view('trf-tm.show', compact('trfTm'));
    }

    public function edit(TrfTm $trfTm)
    {
        $responsaveis = User::all();
        return view('trf-tm.edit', compact('trfTm', 'responsaveis'));
    }

    public function update(Request $request, TrfTm $trfTm)
    {
        $request->validate([
            'data_transferencia' => 'required|date',
            'material' => 'required|string|max:255',
            'quantidade' => 'required|numeric|min:0',
            'unidade' => 'required|string|max:50',
            'origem' => 'required|string|max:255',
            'destino' => 'required|string|max:255',
            'observacoes' => 'nullable|string',
            'responsavel' => 'nullable|exists:users,id'
        ]);

        $data = $request->all();
        if (empty($data['responsavel'])) {
            $data['responsavel'] = null;
        }
        $data['quantidade'] = (float) $data['quantidade'];
        $trfTm->update($data);

        return redirect()->route('trf-tm.index')
            ->with('success', 'Transferência de Material atualizada com sucesso!');
    }

    public function destroy(TrfTm $trfTm)
    {
        $trfTm->delete();
        return redirect()->route('trf-tm.index')
            ->with('success', 'Transferência de Material excluída com sucesso!');
    }

    public function concluir(TrfTm $trfTm)
    {
        $trfTm->update([
            'status' => 'concluida',
            'concluido_por' => Auth::id(),
            'data_conclusao' => now()
        ]);
        return redirect()->back()
            ->with('success', 'Transferência de Material concluída!');
    }

    public function cancelar(Request $request, TrfTm $trfTm)
    {
        $request->validate([
            'motivo_cancelamento' => 'required|string'
        ]);
        $trfTm->update([
            'status' => 'cancelada',
            'cancelado_por' => Auth::id(),
            'data_cancelamento' => now(),
            'motivo_cancelamento' => $request->motivo_cancelamento
        ]);
        return redirect()->back()
            ->with('success', 'Transferência de Material cancelada!');
    }
}
