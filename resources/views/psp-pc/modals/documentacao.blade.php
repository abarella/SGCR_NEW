<!-- Conteúdo do Modal de Documentação -->
<div class="container-fluid p-0">
    <!-- Mensagens -->
    <div id="mensagens-doc" class="alert" style="display: none;">
        <span id="mensagem-texto-doc"></span>
        <button type="button" class="close" onclick="esconderMensagemDoc()">
            <span>&times;</span>
        </button>
    </div>

    <!-- Informações da Pasta -->
    <div class="card mb-1">
        <div class="card-header py-1">
            <h6 class="card-title mb-0">Informações da Pasta</h6>
        </div>
        <div class="card-body py-1">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group mb-0">
                        <label class="mb-0 small"><strong>Produto:</strong></label>
                        <p class="mb-0 small">{{ $produto ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mb-0">
                        <label class="mb-0 small"><strong>Lote:</strong></label>
                        <p class="mb-0 small">{{ $lote ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mb-0">
                        <label class="mb-0 small"><strong>Data:</strong></label>
                        <p class="mb-0 small">{{ $data ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mb-0">
                        <label class="mb-0 small"><strong>Status:</strong></label>
                        <p class="mb-0 small">{{ $status ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulário de Documentação -->
    <div class="card">
        <div class="card-body py-2">
            <form id="formDocumentacao">
                <!-- Radio Buttons para Tipo de Documentação -->
                <div class="row mb-2">
                    <div class="col-12">
                        <div class="form-group mb-1">
                            <label class="mb-1 small"><strong>Tipo de Documentação:</strong></label>
                            <div class="d-flex align-items-center">
                                <div class="form-check me-4">
                                    <input type="radio" class="form-check-input" id="radioProducao" name="tipoDoc" value="P" checked>
                                    <label class="form-check-label small" for="radioProducao">Documentação Produção</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" id="radioControle" name="tipoDoc" value="C">
                                    <label class="form-check-label small" for="radioControle">Documentação Controle</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-1">
                            <label for="txtDataInicial" class="mb-0 small">Data Entrega:</label>
                            <input type="date" id="txtDataInicial" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-1">
                            <label for="cmbRevisadoPor" class="mb-0 small">Revisado por:</label>
                            <select id="cmbRevisadoPor" class="form-control form-control-sm">
                                <option value="">Selecione o revisor...</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group mb-1">
                            <label for="txtObservacao" class="mb-0 small">Observação/Comentário:</label>
                            <textarea id="txtObservacao" class="form-control form-control-sm" rows="2" 
                                      placeholder="Digite suas observações..."></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-1">
                            <label for="cmbSituacaoProducao" class="mb-0 small">Situação da Produção:</label>
                            <select id="cmbSituacaoProducao" class="form-control form-control-sm">
                                <option value="">Selecione...</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-1">
                            <label for="cmbSituacaoPasta" class="mb-0 small">Situação da Pasta:</label>
                            <select id="cmbSituacaoPasta" class="form-control form-control-sm">
                                <option value="">Selecione...</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-1">
                            <label for="txtSenha" class="mb-0 small">Senha:</label>
                            <input type="password" id="txtSenha" class="form-control form-control-sm" 
                                   maxlength="6" placeholder="Digite sua senha">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Estilos otimizados para espaçamento e altura reduzida */
    .card {
        margin-bottom: 0.25rem;
        border: 1px solid #dee2e6;
    }
    
    .card-header {
        padding: 0.375rem 0.75rem;
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }
    
    .card-body {
        padding: 0.5rem 0.75rem;
    }
    
    /* Reduzir espaçamento entre elementos */
    .form-group {
        margin-bottom: 0.5rem;
        transition: all 0.2s ease;
    }
    
    .form-group:last-child {
        margin-bottom: 0;
    }
    
    /* Otimizar labels */
    .form-group label {
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 0.25rem;
        color: #495057;
    }
    
    /* Reduzir espaçamento dos inputs */
    .form-control {
        padding: 0.375rem 0.5rem;
        font-size: 0.875rem;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }
    
    /* Otimizar textarea */
    textarea.form-control {
        resize: vertical;
        min-height: 60px;
    }
    
    /* Prevenir estouro da modal */
    .modal-body {
        max-height: 65vh;
        overflow-y: auto;
        padding: 0.5rem;
    }
    
    /* Otimizar espaçamento da modal */
    .container-fluid {
        padding: 0;
        margin: 0;
    }
    
    /* Reduzir espaçamento entre seções */
    .row {
        margin-bottom: 0.375rem;
    }
    
    .row:last-child {
        margin-bottom: 0;
    }
    
    /* Otimizar colunas */
    .col-md-4, .col-md-6, .col-md-12 {
        padding: 0 0.25rem;
    }
    
    /* Reduzir altura dos inputs */
    .form-control-sm {
        height: calc(1.5em + 0.5rem + 2px);
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    
    /* Otimizar textarea */
    textarea.form-control-sm {
        min-height: 50px;
        max-height: 80px;
    }
    
    /* Estilos para os radio buttons */
    .form-check {
        margin-bottom: 0;
        display: flex;
        align-items: center;
    }
    
    .form-check-input {
        margin-top: 0;
        margin-right: 0.5rem;
        cursor: pointer;
    }
    
    .form-check-label {
        font-size: 0.875rem;
        font-weight: 500;
        color: #495057;
        cursor: pointer;
        margin-bottom: 0;
        user-select: none;
    }
    
    /* Hover nos radio buttons */
    .form-check:hover .form-check-label {
        color: #007bff;
    }
    
    /* Espaçamento entre os radio buttons */
    .d-flex .form-check:not(:last-child) {
        margin-right: 1.5rem;
    }
    
    /* Estilo ativo dos radio buttons */
    .form-check-input:checked + .form-check-label {
        color: #007bff;
        font-weight: 600;
    }
</style>
