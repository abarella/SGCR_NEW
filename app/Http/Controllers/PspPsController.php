<?php

namespace App\Http\Controllers;

use App\Services\PspPsService;
use App\Services\GlobalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PspPsController extends Controller
{
    protected $pspPsService;
    protected $globalService;

    public function __construct(PspPsService $pspPsService, GlobalService $globalService)
    {
        $this->pspPsService = $pspPsService;
        $this->globalService = $globalService;
    }

    public function index()
    {
        return view('psp-ps.index');
    }

    public function lista(Request $request)
    {
        try {
            // Tratamento especial para o tipo, preservando o valor "0"
            $tipo = $request->input('tipo');
            if ($tipo === '' || $tipo === null) {
                $tipo = null;
            } else {
                $tipo = (string)$tipo; // Força ser string para preservar "0"
            }

            $filtros = [
                'mes' => $request->mes,
                'ano' => $request->ano,
                'ordem' => $request->ordem ?? 0,
                'tipo' => $tipo,
                'grupo' => $request->grupo ?? session('cdgrupo'),
                'pst_numero' => $request->pst_numero
            ];

            \Log::info('Filtros recebidos:', $filtros);
            \Log::info('Sessão cdgrupo:', ['cdgrupo' => session('cdgrupo')]);

            $resultado = $this->pspPsService->listarPastas($filtros);

            \Log::info('Resultado do service:', [
                'tipo' => gettype($resultado),
                'count' => is_array($resultado) ? count($resultado) : 'N/A',
                'primeiro_item' => is_array($resultado) && !empty($resultado) ? get_class($resultado[0]) : 'N/A'
            ]);

            $dados = [];

            // Processa os dados de forma mais flexível
            if (is_array($resultado) && !empty($resultado)) {
                \Log::info('Processando dados:', ['count' => count($resultado)]);

                $xmlString = '';

                // Concatena todos os fragmentos XML
                foreach ($resultado as $index => $item) {
                    \Log::info("Processando item $index:", [
                        'tipo' => gettype($item),
                        'classe' => is_object($item) ? get_class($item) : 'N/A',
                        'propriedades' => is_object($item) ? array_keys((array)$item) : []
                    ]);

                    if (is_object($item)) {
                        $itemArray = (array)$item;

                        // Log das propriedades disponíveis
                        \Log::info("Propriedades do item $index:", $itemArray);

                        // Verifica se tem a propriedade XML
                        if (isset($itemArray['XML_F52E2B61-18A1-11d1-B105-00805F49916B'])) {
                            $xmlString .= $itemArray['XML_F52E2B61-18A1-11d1-B105-00805F49916B'];
                        }
                    }
                }

                \Log::info('XML String final:', ['xml' => $xmlString, 'length' => strlen($xmlString)]);

                // Processa o XML
                if (!empty($xmlString)) {
                    // Criar um DOM document para processar o XML de forma mais robusta
                    $dom = new \DOMDocument();
                    $xmlString = '<?xml version="1.0" encoding="UTF-8"?><root>' . $xmlString . '</root>';

                    // Suprimir warnings do XML malformado
                    libxml_use_internal_errors(true);
                    $dom->loadXML($xmlString);
                    $rows = $dom->getElementsByTagName('row');

                    \Log::info('Rows encontradas no XML:', ['count' => $rows->length]);

                    foreach ($rows as $row) {
                        try {
                            $dados[] = [
                                'pst_numero' => $row->getAttribute('pst_numero'),
                                'nome_comercial' => $row->getAttribute('nome_comercial'),
                                'lote' => $row->getAttribute('Lote'),
                                'registro' => $row->getAttribute('registro'),
                                'pst_previsaocontrole' => $row->getAttribute('pst_previsaocontrole'),
                                'pst_previsaoproducao' => $row->getAttribute('pst_previsaoproducao'),
                                'producao_revisadopor' => $row->getAttribute('producao_revisadopor'),
                                'controle_revisadopor' => $row->getAttribute('controle_revisadopor'),
                                'status' => $row->getAttribute('status'),
                                'status_producao' => $row->getAttribute('status_producao'),
                                'obs_producao' => $row->getAttribute('pst_obsp'),
                                'obs_controle' => $row->getAttribute('pst_obsc'),
                                'pst_observacao' => $row->getAttribute('pst_observacao'),
                            ];
                        } catch (\Exception $e) {
                            \Log::error('Erro ao processar row XML:', [
                                'error' => $e->getMessage(),
                                'xml' => $row->C14N()
                            ]);
                        }
                    }

                    libxml_clear_errors();
                }
            } else {
                \Log::warning('Resultado vazio ou não é array:', [
                    'tipo' => gettype($resultado),
                    'vazio' => empty($resultado)
                ]);
            }

            \Log::info('Dados processados:', [
                'quantidade' => count($dados),
                'primeiro_item' => !empty($dados) ? $dados[0] : null
            ]);

            // Adiciona os botões de ação e formata os dados
            foreach ($dados as &$item) {
                try {
                    $item['acoes'] = $this->getBotoesAcao($item);
                    \Log::info('Item processado:', [
                        'pst_numero' => $item['pst_numero'],
                        'acoes' => $item['acoes']
                    ]);

                    // Formata as datas se existirem
                    if (!empty($item['pst_previsaocontrole'])) {
                        //$item['pst_previsaocontrole'] = date('d/m/Y', strtotime($item['pst_previsaocontrole']));
                    }
                    if (!empty($item['pst_previsaoproducao'])) {
                        //$item['pst_previsaoproducao'] = date('d/m/Y', strtotime($item['pst_previsaoproducao']));
                    }
                } catch (\Exception $e) {
                    \Log::error('Erro ao processar item:', [
                        'error' => $e->getMessage(),
                        'item' => $item
                    ]);
                    $item = [
                        'pst_numero' => $item['pst_numero'] ?? '',
                        'nome_comercial' => '',
                        'lote' => '',
                        'registro' => '',
                        'pst_previsaocontrole' => '',
                        'pst_previsaoproducao' => '',
                        'producao_revisadopor' => '',
                        'controle_revisadopor' => '',
                        'status' => '',
                        'status_producao' => '',
                        'obs_producao' => '',
                        'pst_observacao' => '',
                        'acoes' => ''
                    ];
                }
            }

            \Log::info('Resposta final:', [
                'dados_count' => count($dados),
                'primeiro_item' => !empty($dados) ? $dados[0] : null
            ]);

            return response()->json(['data' => $dados]);
        } catch (\Exception $e) {
            \Log::error('Erro no método lista:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function status()
    {
        try {
            $status = $this->pspPsService->getStatus();
            return response()->json($status);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($numero)
    {
        $pasta = $this->pspPsService->getPasta($numero);
        if (!$pasta) {
            return redirect()->route('psp-ps.index')
                ->with('error', 'Pasta não encontrada.');
        }

        return view('psp-ps.show', compact('pasta'));
    }

    public function edit($numero)
    {
        //dd($numero);
        /*
        if (!Gate::allows('edit-psp-ps')) {
            return redirect()->route('psp-ps.show', $numero)
                ->with('error', 'Sem permissão para alterar.');
        }
        */

        $pasta = $this->pspPsService->getPasta($numero);
        //dd($pasta);
        if (!$pasta) {
            return redirect()->route('psp-ps.index')
                ->with('error', 'Pasta não encontrada.');
        }

        try {
            // Carrega revisadores
            $revisadores = $this->globalService->CarregarRevisado();
            if (!is_array($revisadores)) {
                $revisadores = [];
                \Log::warning('CarregarRevisado não retornou um array');
            }

            // Carrega situações de produção
            $producaoStatus = $this->globalService->carregarProducaStatus();
            if (!is_array($producaoStatus)) {
                $producaoStatus = [];
                \Log::warning('carregarProducaStatus não retornou um array');
            }

            // Carrega lista de status
            $statusList = $this->globalService->carregarStatus();
            //dd($statusList);
            if (!is_array($statusList)) {
                $statusList = [];
                \Log::warning('carregarStatus não retornou um array');
            }

        } catch (\Exception $e) {
            \Log::error('Erro ao carregar dados: ' . $e->getMessage());
            $revisadores = [];
            $producaoStatus = [];
            $statusList = [];
        }

        return view('psp-ps.edit', compact('pasta', 'revisadores', 'producaoStatus', 'statusList'));
    }

    public function update(Request $request, $numero)
    {
        if (!Gate::allows('edit-psp-ps')) {
            return redirect()->route('psp-ps.show', $numero)
                ->with('error', 'Sem permissão para alterar.');
        }

        $this->validate($request, [
            'pst_previsaocontrole' => 'required|date',
            'pst_previsaoproducao' => 'required|date',
            'pst_observacao_controle' => 'nullable|string|max:1000',
            'pst_observacao_producao' => 'nullable|string|max:1000',
            'cmbSitControle' => 'required',
            'cmbSitProducao' => 'required',
            'cmdStsControle' => 'required',
            'cmdStsProducao' => 'required',
            'password' => 'required'
        ]);

        try {
            $this->pspPsService->updatePasta($numero, $request->all());
            return redirect()->route('psp-ps.show', $numero)
                ->with('success', 'Pasta atualizada com sucesso.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Erro ao atualizar: ' . $e->getMessage());
        }
    }

    public function editDoc($numero)
    {
        if (!Gate::allows('edit-psp-ps-doc')) {
            return redirect()->route('psp-ps.show', $numero)
                ->with('error', 'Sem permissão para alterar documentação.');
        }

        $pasta = $this->pspPsService->getPasta($numero);
        if (!$pasta) {
            return redirect()->route('psp-ps.index')
                ->with('error', 'Pasta não encontrada.');
        }

        return view('psp-ps.edit-doc', compact('pasta'));
    }

    public function updateDoc(Request $request, $numero)
    {
        if (!Gate::allows('edit-psp-ps-doc')) {
            return redirect()->route('psp-ps.show', $numero)
                ->with('error', 'Sem permissão para alterar documentação.');
        }

        $this->validate($request, [
            'data_entrega' => 'required|date',
            'observacao' => 'nullable|string|max:1000'
        ]);

        try {
            $this->pspPsService->updateDocumentacao($numero, $request->all());
            return redirect()->route('psp-ps.show', $numero)
                ->with('success', 'Documentação atualizada com sucesso.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Erro ao atualizar: ' . $e->getMessage());
        }
    }

    public function test(Request $request)
    {
        try {
            $filtros = [
                'mes' => $request->mes ?? date('n'),
                'ano' => $request->ano ?? date('Y'),
                'ordem' => 0,
                'tipo' => '',
                'grupo' => session('cdgrupo'),
                'pst_numero' => ''
            ];

            \Log::info('Teste - Filtros:', $filtros);

            $resultado = $this->pspPsService->listarPastas($filtros);

            \Log::info('Teste - Resultado:', [
                'tipo' => gettype($resultado),
                'count' => is_array($resultado) ? count($resultado) : 'N/A',
                'primeiro_item' => is_array($resultado) && !empty($resultado) ? get_class($resultado[0]) : 'N/A'
            ]);

            // Processa os dados de forma mais flexível
            $dados = [];
            if (is_array($resultado) && !empty($resultado)) {
                foreach ($resultado as $index => $item) {
                    if (is_object($item)) {
                        $itemArray = (array)$item;
                        $dados[] = $itemArray;
                    }
                }
            }

            return response()->json([
                'filtros' => $filtros,
                'resultado_tipo' => gettype($resultado),
                'resultado_count' => is_array($resultado) ? count($resultado) : 'N/A',
                'dados_processados' => $dados,
                'primeiro_item_propriedades' => !empty($dados) ? array_keys($dados[0]) : []
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro no teste:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function getBotoesAcao($item)
    {
        $botoes = '';

        // Botão de Alteração (apenas grupo 6)
        //if (Gate::allows('edit-psp-ps') && session('cdgrupo') == 6) {
            $botoes .= '<a href="'.route('psp-ps.edit', $item['pst_numero']).'"
                          class="btn btn-sm btn-primary" title="Alterar">
                          <i class="fas fa-edit"></i>
                       </a>';
        //}

        // Botão de Documentação (grupos 2 a 6)
        //if (Gate::allows('edit-psp-ps-doc') && in_array(session('cdgrupo'), [2,3,4,5,6])) {
            $botoes .= '<a href="'.route('psp-ps.edit-doc', $item['pst_numero']).'"
                          class="btn btn-sm btn-info ml-1" title="Alterar Data de Entrega">
                          <i class="fas fa-calendar-alt"></i>
                       </a>';
        //}

        // Botão de Detalhes (todos)
        $botoes .= '<a href="'.route('psp-ps.show', $item['pst_numero']).'"
                      class="btn btn-sm btn-secondary ml-1" title="Detalhes">
                      <i class="fas fa-search"></i>
                   </a>';


        return $botoes;
    }
}
