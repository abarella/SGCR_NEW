@extends('adminlte::page')

@section('title', 'Pastas Não Concluídas')

@section('adminlte_css_pre')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* CSS para container de botões */
        .coluna-funcoes {
            width: 130px !important;
            min-width: 130px !important;
            max-width: 130px !important;
            padding: 0 !important;
            text-align: center !important;
        }
        
        /* Container dos botões */
        .botoes-container {
            display: flex !important;
            flex-direction: row !important;
            gap: 1px !important;
            justify-content: center !important;
            align-items: center !important;
            width: 100% !important;
            height: 36px !important;
        }
        
        /* Estilo dos botões */
        .botoes-container .btn {
            border: none !important;
            box-shadow: none !important;
            margin: 0 !important;
            padding: 6px 10px !important;
            width: 36px !important;
            height: 36px !important;
            line-height: 24px !important;
            font-size: 1.1rem !important;
            box-sizing: border-box !important;
            border-radius: 6px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            flex-shrink: 0 !important;
        }
        
        /* Garantir que os ícones não quebrem */
        .botoes-container .btn i {
            display: inline-block !important;
            vertical-align: middle !important;
            line-height: 1 !important;
            margin: 0 !important;
            font-size: 1.1rem !important;
        }
        
        /* Reduzir fonte da tabela */
        #tabelaPastas {
            font-size: 0.85rem;
        }
        
        #tabelaPastas th {
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        #tabelaPastas td {
            font-size: 0.85rem;
            padding: 0.4rem 0.5rem;
        }
        
        /* Garantir que a primeira coluna tenha largura fixa */
        #tabelaPastas th:first-child,
        #tabelaPastas td:first-child {
            width: 130px !important;
            min-width: 130px !important;
            max-width: 130px !important;
        }
    </style>
@endsection

@section('content_header')
<h5 class="m-0">Pastas Não Concluídas</h5>
@stop

@section('content')
<div class="container-fluid">
    <!-- Mensagens -->
    <div id="mensagens" class="alert" style="display: none;">
        <span id="mensagem-texto"></span>
        <button type="button" class="close" onclick="esconderMensagem()">
            <span>&times;</span>
        </button>
    </div>

    <!-- Filtros -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Filtros</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="cmbProduto">Produto</label>
                        <select id="cmbProduto" class="form-control" onchange="carregarLista()">
                            <option value="">Todos os produtos</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="txtPstNumero">Localizar Pasta</label>
                        <input type="text" id="txtPstNumero" class="form-control" maxlength="10" 
                               placeholder="Número da pasta" onchange="carregarLista()">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grid -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Lista de Pastas</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="tabelaPastas" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class="coluna-funcoes" style="width: 130px;">Funções</th>
                            <th style="cursor:pointer" onclick="trocarOrdem(0)">Nr. Pasta</th>
                            <th style="display:none">Produto</th>
                            <th style="cursor:pointer" onclick="trocarOrdem(1)">Produto</th>
                            <th>Lote</th>
                            <th>Lote Novo</th>
                            <th>Ano</th>
                            <th>Autorizado em</th>
                            <th style="cursor:pointer" onclick="trocarOrdem(2)">Documentação Produção</th>
                            <th>Produção-Revisado por:</th>
                            <th style="cursor:pointer" onclick="trocarOrdem(3)">Documentação Controle</th>
                            <th>Controle-Revisado por:</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="tabelaPastasBody">
                        <!-- Dados serão carregados via JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Documentação -->
<div class="modal fade" id="modalDocumentacao" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registrar Documentação</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="conteudoDocumentacao">
                    <p>Modal de documentação funcionando!</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Ocorrências -->
<div class="modal fade" id="modalOcorrencias" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registrar Ocorrências</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="conteudoOcorrencias">
                    <p>Modal de ocorrências funcionando!</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Localização -->
<div class="modal fade" id="modalLocalizar" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Localizar Pasta</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="conteudoLocalizar">
                    <p>Modal de localização funcionando!</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script src="{{ asset('js/psp-pc/psp-pc.js') }}"></script>
<script>
// Inicialização automática
$(document).ready(function() {
    console.log('✅ PSP-PC View: jQuery carregado e DOM pronto');
    
    // Remover parâmetros de paginação da URL - versão melhorada
    function limparURL() {
        if (window.location.search.includes('pagina=')) {
            const url = new URL(window.location);
            url.searchParams.delete('pagina');
            window.history.replaceState({}, '', url);
            console.log('🔧 PSP-PC View: Parâmetro de paginação removido da URL');
        }
    }
    
    // Executar limpeza imediatamente
    limparURL();
    
    // Executar limpeza após um pequeno delay para garantir
    setTimeout(limparURL, 100);
    
    // Executar limpeza após carregar a página completamente
    setTimeout(limparURL, 500);
    
    // Função para manter botões juntos na coluna funções
    function manterBotoesJuntos() {
        const containers = document.querySelectorAll('.botoes-container');
        containers.forEach(container => {
            // Garantir que o container mantenha as propriedades flexbox
            container.style.display = 'flex';
            container.style.flexDirection = 'row';
            container.style.gap = '1px';
            container.style.justifyContent = 'center';
            container.style.alignItems = 'center';
            container.style.width = '100%';
            container.style.height = '36px';
            
            const botoes = container.querySelectorAll('.btn');
            botoes.forEach((botao, index) => {
                // Garantir que cada botão mantenha as propriedades corretas
                botao.style.width = '36px';
                botao.style.height = '36px';
                botao.style.margin = '0';
                botao.style.padding = '6px 10px';
                // Remover qualquer classe Bootstrap que possa adicionar margens
                botao.classList.remove('me-1', 'me-2', 'me-3', 'ms-1', 'ms-2', 'ms-3', 'm-1', 'm-2', 'm-3');
            });
        });
    }
    
    // Executar função de manutenção dos botões
    manterBotoesJuntos();
    
    // Executar após um delay para garantir que a tabela foi carregada
    setTimeout(manterBotoesJuntos, 1000);
    setTimeout(manterBotoesJuntos, 2000);
    
    // Executar mais frequentemente para garantir que os botões permaneçam juntos
    setInterval(manterBotoesJuntos, 3000);
    
    // Aguardar um pouco para o JavaScript principal carregar
    setTimeout(function() {
        console.log('⏰ PSP-PC View: Verificando se JavaScript principal foi carregado...');
        
        if (typeof carregarLista === 'function') {
            console.log('✅ PSP-PC View: JavaScript principal carregado, iniciando carregamento automático...');
            carregarLista();
            
            // Executar manutenção dos botões após carregar a lista
            setTimeout(manterBotoesJuntos, 500);
        } else {
            console.error('❌ PSP-PC View: JavaScript principal NÃO foi carregado!');
        }
    }, 1000);
});
</script>
@stop
