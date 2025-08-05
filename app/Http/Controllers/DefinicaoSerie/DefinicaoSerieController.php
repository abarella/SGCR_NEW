<?php

namespace App\Http\Controllers\DefinicaoSerie;

use App\Http\Controllers\Controller;
use App\Services\DefinicaoSerieService;
use App\Services\GlobalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DefinicaoSerieController extends Controller
{
    protected $definicaoSerieService;
    protected $globalService;

    public function __construct(DefinicaoSerieService $definicaoSerieService, GlobalService $globalService)
    {
        $this->definicaoSerieService = $definicaoSerieService;
        $this->globalService = $globalService;
    }

    /**
     * Exibe a página principal de definição de série
     */
    public function index(Request $request)
    {
        $produto = $request->get('produto', 0);
        $lote = $request->get('lote', 0);
        
        $produtos = $this->globalService->listarProdutos();
        $lotes = GlobalService::carregarLotes($produto);
        $listaSerie = $this->definicaoSerieService->retornarListaSerie($produto, $lote);

        return view('definicaoserie.index', compact('produtos', 'lotes', 'listaSerie', 'produto', 'lote'));
    }

    /**
     * Carrega lotes baseado no produto selecionado (AJAX)
     */
    public function carregarLotes(Request $request)
    {
        $produto = $request->get('produto');
        $lotes = GlobalService::carregarLotes($produto);
        
        return response()->json(['lotes' => $lotes]);
    }

    /**
     * Pesquisa lista de série (AJAX)
     */
    public function pesquisarListaSerie(Request $request)
    {
        $produto = $request->get('produto');
        $lote = $request->get('lote');
        
        $listaSerie = $this->definicaoSerieService->retornarListaSerie($produto, $lote);
        
        return response()->json(['lista' => $listaSerie]);
    }

    /**
     * Define série individual
     */
    public function definirSerie(Request $request)
    {
        $request->validate([
            'p110chve' => 'required',
            'p110serie' => 'required',
            'senha' => 'required'
        ]);

        $resultado = $this->definicaoSerieService->gravarSerie(
            $request->p110chve,
            $request->p110serie,
            Auth::id(),
            $request->senha
        );

        return response()->json(['message' => $resultado]);
    }

    /**
     * Exibe página de definição por intervalo de atividade
     */
    public function intervalo(Request $request)
    {
        $produto = $request->get('PRODUTO', 0);
        $lote = $request->get('LOTE', 0);
        
        $series = $this->definicaoSerieService->carregarSeries($produto, $lote);
        $tecnicos = $this->globalService->carregarTecnicos();

        $mensagem = null;
        if (is_array($series) && isset($series['mensagem'])) {
            $mensagem = $series['mensagem'];
            $series = [];
        }

        return view('definicaoserie.intervalo', compact('produto', 'lote', 'series', 'tecnicos', 'mensagem'));
    }

    /**
     * Define série por intervalo de atividade
     */
    public function definirSerieIntervalo(Request $request)
    {
        $request->validate([
            'produto' => 'required',
            'lote' => 'required',
            'serie' => 'required',
            'inicio' => 'required|numeric',
            'fim' => 'required|numeric|gte:inicio',
            'forca' => 'required|in:S,N',
            'senha' => 'required'
        ]);

        $resultado = $this->definicaoSerieService->gravarSerieAtividade(
            $request->produto,
            $request->lote,
            $request->serie,
            Auth::id(),
            $request->senha,
            1, // tipo = atividade
            $request->inicio,
            $request->fim,
            $request->forca
        );

        return response()->json(['message' => $resultado]);
    }

    /**
     * Exibe página de definição por intervalo de lote/número
     */
    public function intervaloLote(Request $request)
    {
        $produto = $request->get('PRODUTO', 0);
        $lote = $request->get('LOTE', 0);
        
        $series = $this->definicaoSerieService->carregarSeries($produto, $lote);
        $numero = $this->definicaoSerieService->buscarNumero($produto, $lote);
        $tecnicos = $this->globalService->carregarTecnicos();

        return view('definicaoserie.intervalo-lote', compact('produto', 'lote', 'series', 'tecnicos', 'numero'));
    }

    /**
     * Define série por intervalo de lote/número
     */
    public function definirSerieIntervaloLote(Request $request)
    {
        $request->validate([
            'produto' => 'required',
            'lote' => 'required',
            'serie' => 'required',
            'inicio' => 'required|numeric',
            'fim' => 'required|numeric|gte:inicio',
            'forca' => 'required|in:S,N',
            'senha' => 'required'
        ]);

        $resultado = $this->definicaoSerieService->gravarSerieAtividade(
            $request->produto,
            $request->lote,
            $request->serie,
            Auth::id(),
            $request->senha,
            2, // tipo = lote
            $request->inicio,
            $request->fim,
            $request->forca
        );

        return response()->json(['message' => $resultado]);
    }


    /**
     * Define múltiplas séries da tabela
     */
    public function definirMultiplasSeries(Request $request)
    {
        $request->validate([
            'dados' => 'required|array',
            'senha' => 'required'
        ]);

        $resultado = $this->definicaoSerieService->gravarMultiplasSeries(
            $request->dados,
            Auth::id(),
            $request->senha
        );

        return response()->json(['message' => $resultado]);
    }
} 