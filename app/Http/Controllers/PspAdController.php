<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PspAdService;
use App\Services\GlobalService;

class PspAdController extends Controller
{
    protected $service;

    public function __construct(PspAdService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $lote = $request->input('lote');
        $serie = $request->input('serie');
        $produto = urldecode($request->input('produto', ''));
        // Corrige datas vindas da querystring (datetime-local via GET)
        $data_fracionamento = str_replace('+', 'T', $request->input('data_fracionamento', ''));
        $data_calibracao = str_replace('+', 'T', $request->input('data_calibracao', ''));
        $pedidos = $this->service->listarPedidos($lote, $serie, $produto);
        $filtros = compact('lote', 'serie', 'produto', 'data_fracionamento', 'data_calibracao');
        $produtos = GlobalService::listarProdutos();
        return view('psp-ad.index', compact('pedidos', 'filtros', 'produtos'));
    }

    public function atualizar(Request $request)
    {
        $ids = $request->input('ids', []);
        $dataFracionamento = $request->input('data_fracionamento');
        $dataCalibracao = $request->input('data_calibracao');
        $usuario = auth()->user()->id ?? null;
        $this->service->atualizarPedidos($ids, $dataFracionamento, $dataCalibracao, $usuario);
        return redirect()->route('psp-ad.index')->with('success', 'Pedidos atualizados com sucesso!');
    }
}
