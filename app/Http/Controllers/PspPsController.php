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


            $resultado = $this->pspPsService->listarPastas($filtros);


            $dados = [];

            // Processa os dados de forma mais flexível
            if (is_array($resultado) && !empty($resultado)) {


                $xmlString = '';

                // Concatena todos os fragmentos XML
                foreach ($resultado as $index => $item) {

                    if (is_object($item)) {
                        $itemArray = (array)$item;

                        // Verifica se tem a propriedade XML
                        if (isset($itemArray['XML_F52E2B61-18A1-11d1-B105-00805F49916B'])) {
                            $xmlString .= $itemArray['XML_F52E2B61-18A1-11d1-B105-00805F49916B'];
                        }
                    }
                }



                // Processa o XML
                if (!empty($xmlString)) {
                    // Criar um DOM document para processar o XML de forma mais robusta
                    $dom = new \DOMDocument();
                    $xmlString = '<?xml version="1.0" encoding="UTF-8"?><root>' . $xmlString . '</root>';

                    // Suprimir warnings do XML malformado
                    libxml_use_internal_errors(true);
                    $dom->loadXML($xmlString);
                    $rows = $dom->getElementsByTagName('row');



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
                        }
                    }

                    libxml_clear_errors();
                }
            } else {
            }


            // Adiciona os botões de ação e formata os dados
            foreach ($dados as &$item) {
                try {
                    $item['acoes'] = $this->getBotoesAcao($item);

                    // Formata as datas se existirem
                    if (!empty($item['pst_previsaocontrole'])) {
                        //$item['pst_previsaocontrole'] = date('d/m/Y', strtotime($item['pst_previsaocontrole']));
                    }
                    if (!empty($item['pst_previsaoproducao'])) {
                        //$item['pst_previsaoproducao'] = date('d/m/Y', strtotime($item['pst_previsaoproducao']));
                    }
                } catch (\Exception $e) {
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


            return response()->json(['data' => $dados]);
        } catch (\Exception $e) {
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
        try {
            // Carrega dados básicos da pasta
            $pasta = $this->pspPsService->getPasta($numero);
            if (!$pasta) {
                return redirect()->route('psp-ps.index')
                    ->with('error', 'Pasta não encontrada.');
            }

            // Carrega dados das procedures
            $lista2Data = $this->pspPsService->getLista2($numero);
            $lista3Data = $this->pspPsService->getLista3($numero);

            return view('psp-ps.show', compact('pasta', 'lista2Data', 'lista3Data'));

        } catch (\Exception $e) {
            \Log::error('Erro ao carregar dados da pasta:', [
                'numero' => $numero,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('psp-ps.index')
                ->with('error', 'Erro ao carregar dados da pasta: ' . $e->getMessage());
        }
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

        // Carrega dados de controle
        $pasta_controle = $this->pspPsService->getPasta($numero, 'C');
        // Carrega dados de produção
        $pasta_producao = $this->pspPsService->getPasta($numero, 'P');

        // Usa os dados de controle como base (para campos comuns como número da pasta)
        $pasta = $pasta_controle ?: $pasta_producao;

        // Se nenhum dos dois retornou dados, cria objetos vazios para evitar erros
        if (!$pasta_controle) {
            $pasta_controle = new \stdClass();
        }
        if (!$pasta_producao) {
            $pasta_producao = new \stdClass();
        }

        if (!$pasta) {
            return redirect()->route('psp-ps.index')
                ->with('error', 'Pasta não encontrada.');
        }

        try {
            // Carrega revisadores
            $revisadores = $this->globalService->CarregarRevisado();
            if (!is_array($revisadores)) {
                $revisadores = [];

            }

            // Carrega situações de produção
            $producaoStatus = $this->globalService->carregarProducaStatus();
            if (!is_array($producaoStatus)) {
                $producaoStatus = [];

            }

            // Carrega lista de status
            $statusList = $this->globalService->carregarStatus();
            //dd($statusList);
            if (!is_array($statusList)) {
                $statusList = [];

            }

        } catch (\Exception $e) {

            $revisadores = [];
            $producaoStatus = [];
            $statusList = [];
        }

        return view('psp-ps.edit', compact('pasta', 'pasta_controle', 'pasta_producao', 'revisadores', 'producaoStatus', 'statusList'));
    }

    public function update(Request $request, $numero)
    {

        // Valida a senha do usuário logado
        $user = auth()->user();
        if (!$user) {
        }


        // Verifica se a senha fornecida é a mesma do login
        if (!\Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Senha incorreta.',
                'type' => 'error'
            ]);
        }


        // Validação apenas da senha (obrigatória) e formato das datas (se fornecidas)
        $this->validate($request, [
            'pst_previsaocontrole' => 'nullable|date',
            'pst_previsaoproducao' => 'nullable|date',
            'pst_observacao_controle' => 'nullable|string|max:1000',
            'pst_observacao_producao' => 'nullable|string|max:1000',
            'cmbSitControle' => 'nullable',
            'cmbSitProducao' => 'nullable',
            'cmdStsControle' => 'nullable',
            'cmdStsProducao' => 'nullable',
            'password' => 'required'
        ]);

        try {
            $this->pspPsService->updatePasta($numero, $request->all());
            return response()->json([
                'success' => true,
                'message' => 'Pasta atualizada com sucesso.',
                'type' => 'success',
                'redirect' => route('psp-ps.index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function editDoc($numero)
    {
        // Carrega dados de controle
        $pasta_controle = $this->pspPsService->getPasta($numero, 'C');
        // Carrega dados de produção
        $pasta_producao = $this->pspPsService->getPasta($numero, 'P');

        // Usa os dados de controle como base (para campos comuns como número da pasta)
        $pasta = $pasta_controle ?: $pasta_producao;

        // Se nenhum dos dois retornou dados, cria objetos vazios para evitar erros
        if (!$pasta_controle) {
            $pasta_controle = new \stdClass();
        }
        if (!$pasta_producao) {
            $pasta_producao = new \stdClass();
        }

        if (!$pasta) {
            return redirect()->route('psp-ps.index')
                ->with('error', 'Pasta não encontrada.');
        }

        // Carrega dados para os comboboxes
        $producaoStatus = app(GlobalService::class)->carregarProducaStatus();
        $statusList = app(GlobalService::class)->carregarStatus();

        // Log para debug
        \Log::info('editDoc - Dados carregados:', [
            'numero' => $numero,
            'pasta_controle_exists' => isset($pasta_controle),
            'pasta_producao_exists' => isset($pasta_producao),
            'producaoStatus_count' => count($producaoStatus),
            'statusList_count' => count($statusList)
        ]);

        return view('psp-ps.edit-doc', compact('pasta', 'pasta_controle', 'pasta_producao', 'producaoStatus', 'statusList'));
    }

    public function updateDoc(Request $request, $numero)
    {
        // Validação de senha
        $user = auth()->user();
        if (!$user) {
            \Log::error('Usuário não autenticado');
            return response()->json([
                'success' => false,
                'message' => 'Usuário não autenticado.',
                'type' => 'error'
            ]);
        }

        \Log::info('Usuário autenticado:', [
            'user_id' => $user->id,
            'username' => $user->username,
            'password_provided' => !empty($request->password)
        ]);

        if (!\Hash::check($request->password, $user->password)) {
            \Log::error('Senha incorreta para usuário:', [
                'user_id' => $user->id,
                'username' => $user->username
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Senha incorreta.',
                'type' => 'error'
            ]);
        }

        \Log::info('Senha validada com sucesso para usuário:', [
            'user_id' => $user->id,
            'username' => $user->username
        ]);

        $request->validate([
            'password' => 'required',
            'data_entrega_controle' => 'nullable|date',
            'data_entrega_producao' => 'nullable|date',
            'pst_obsc' => 'nullable|string',
            'pst_obsp' => 'nullable|string',
            'cmdStsControle' => 'nullable|integer',
            'cmdStsProducao' => 'nullable|integer',
            'active_tab' => 'required|in:controle,producao'
        ]);

        try {
            $active_tab = $request->input('active_tab');

            // Prepara dados baseados no tab ativo
            $dados = [
                'pst_numero' => $numero,
                'cdusuario' => $user->cdusuario,
                'senha' => $request->password,
                'cmdStsControle' => $request->input('cmdStsControle'),
                'cmdStsProducao' => $request->input('cmdStsProducao')
            ];

            if ($active_tab === 'controle') {
                $dados['data_entrega'] = $request->input('data_entrega_controle');
                $dados['observacao'] = $request->input('pst_obsc');
                $dados['tipo'] = 'C';
            } else {
                $dados['data_entrega'] = $request->input('data_entrega_producao');
                $dados['observacao'] = $request->input('pst_obsp');
                $dados['tipo'] = 'P';
            }

            $result = $this->pspPsService->updatePrevisao($numero, $dados);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Documentação atualizada com sucesso!',
                    'redirect' => route('psp-ps.index')
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao atualizar documentação.',
                    'type' => 'error'
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar documentação:', [
                'numero' => $numero,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor: ' . $e->getMessage(),
                'type' => 'error'
            ]);
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



            $resultado = $this->pspPsService->listarPastas($filtros);


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
