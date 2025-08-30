@extends('adminlte::page')

@section('title', 'Pastas Não Concluídas')

@section('adminlte_css_pre')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('adminlte_js')
    <script src="{{ asset('js/psp-pc/psp-pc.js') }}"></script>
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
                        <select id="cmbProduto" class="form-control">
                            <option value="">Todos os produtos</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="txtPstNumero">Localizar Pasta</label>
                        <input type="text" id="txtPstNumero" class="form-control" maxlength="10" 
                               placeholder="Número da pasta">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="button" id="btnPesquisar" class="btn btn-primary btn-block">
                            <i class="fas fa-search"></i> Pesquisar
                        </button>
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
                            <th class="coluna-funcoes" style="width: 150px;">Funções</th>
                            <th style="cursor:pointer" onclick="trocarOrdem(0)">Nr. Pasta</th>
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

    <!-- Impressão -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Impressão de Pastas</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Intervalo de Pastas:</label>
                        <div class="input-group">
                            <input type="text" id="txtPstInicio" class="form-control" size="6" maxlength="10" 
                                   placeholder="Início" style="text-align:right;">
                            <div class="input-group-append">
                                <span class="input-group-text">a</span>
                            </div>
                            <input type="text" id="txtPstTermino" class="form-control" size="6" maxlength="10" 
                                   placeholder="Término" style="text-align:right;">
                            <div class="input-group-append">
                                <button type="button" id="btnImpressora" class="btn btn-outline-secondary" 
                                        onclick="chamaImpressaoPastas()" title="Imprimir">
                                    <i class="fas fa-print"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
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
                    <form id="formDocumentacao">
                        <!-- Informações da Pasta -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <strong>Produto:</strong> <span id="infoProduto"></span> | 
                                    <strong>Lote:</strong> <span id="infoLote"></span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tipo de Documentação -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">Tipo de Documentação:</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="tipoDoc" id="radioProducao" value="P">
                                        <label class="form-check-label" for="radioProducao">Documentação Produção</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="tipoDoc" id="radioControle" value="C">
                                        <label class="form-check-label" for="radioControle">Documentação Controle</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Data Entrega -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="txtDataEntrega" class="form-label">Data Entrega:</label>
                                    <input type="date" id="txtDataEntrega" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cmbUsuarioRevisor" class="form-label">Revisado por:</label>
                                    <select id="cmbUsuarioRevisor" class="form-control" required>
                                        <option value="">Selecione...</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Observação -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="txtObservacao" class="form-label">Observação/Comentário:</label>
                                    <textarea id="txtObservacao" class="form-control" rows="3" maxlength="255" placeholder="Digite a observação"></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Situações -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cmbSituacaoProducao" class="form-label">Situação da Produção:</label>
                                    <select id="cmbSituacaoProducao" class="form-control" required>
                                        <option value="">Selecione...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cmbSituacaoPasta" class="form-label">Situação da Pasta:</label>
                                    <select id="cmbSituacaoPasta" class="form-control" required>
                                        <option value="">Selecione...</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Senha -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="txtSenha" class="form-label">Senha:</label>
                                    <input type="password" id="txtSenha" class="form-control" maxlength="6" placeholder="Digite sua senha" required>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Campos ocultos para dados da pasta -->
                        <input type="hidden" id="txtPasta" name="txtPasta">
                        <input type="hidden" id="txtStatus" name="txtStatus">
                        <input type="hidden" id="txtProdStatus" name="txtProdStatus">
                        <input type="hidden" id="txtCDusuario" name="txtCDusuario" value="{{ Auth::user()->p1110_usuarioid ?? '' }}">
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" onclick="gravarDocumentacao()">Enviar</button>
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
                    <!-- Conteúdo será carregado via AJAX -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" onclick="salvarOcorrencias()">Salvar</button>
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
                    <!-- Conteúdo será carregado via AJAX -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Carregamento -->
<div class="modal fade" id="modalCarregando" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Carregando...</span>
                </div>
                <p class="mt-2">Carregando...</p>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .table th {
        background-color: #f4f6f9;
        border-color: #dee2e6;
    }
    
    .table th[style*="cursor:pointer"]:hover {
        background-color: #e9ecef;
    }
    
    /* Estilos específicos para a coluna de Funções */
    .coluna-funcoes {
        white-space: nowrap;
        width: 150px;
    }
    
    .coluna-funcoes button {
        display: inline-block;
        margin: 0 2px;
        padding: 4px 8px;
        font-size: 12px;
    }
    
    .pagination {
        margin-bottom: 0;
    }
    
    /* Estilos para o modal de documentação */
    #modalDocumentacao .col-md-4 {
        flex: 0 0 33.333333% !important;
        max-width: 33.333333% !important;
        padding: 0 0.25rem;
    }
    
    /* Forçar layout em linha única */
    #modalDocumentacao .form-row,
    #modalDocumentacao .row.g-2 {
        display: flex !important;
        flex-wrap: nowrap !important;
        margin: 0 !important;
    }
    
    /* Garantir que os campos não quebrem */
    #modalDocumentacao .form-group {
        flex: 1;
        min-width: 0;
    }
</style>
@stop
