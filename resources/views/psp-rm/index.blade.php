@extends('adminlte::page')

@section('title', 'R.D. & M.M.')

{{-- Plugins necessários --}}
@section('plugins.TempusDominus', true)
@section('plugins.Moment', true)

@section('content_header')
    <h5 class="m-0">R.D. & M.M. - Radioisotopos e Moléculas Marcadas</h5>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <!-- Cabeçalho -->
        <h4 class="text-center mb-4">Produção de Moléculas Marcadas - Autorização</h4>
        
        <!-- Alertas -->
        <div id="alert-container"></div>

        <!-- Formulário de busca -->
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="cmbarea">Área:</label>
                <select name="categoria" id="cmbarea" class="form-control">
                    <option value="1">Radioisotopos Primarios</option>
                    <option value="3" selected>Moléculas Marcadas</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="txtlote">Lote:</label>
                <input type="text" id="txtlote" name="lote" class="form-control" 
                       size="4" maxlength="3" placeholder="Lote" 
                       onkeypress="return event.charCode >= 48 && event.charCode <= 57">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="button" class="btn btn-primary" onclick="buscarProdutos()">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>

        <!-- Tabela de Produtos -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="tabela-produtos">
                <thead>
                    <tr>
                        <th>Produtos</th>
                        <th>Número de Produções</th>
                        <th>Calibração</th>
                        <th>Partidas</th>
                        <th>Séries Autorizadas</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="tbody-produtos">
                    <tr>
                        <td colspan="6" class="text-center">Informe um lote para buscar produtos</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal de Calibração -->
<div class="modal fade" id="modal-calibracao" tabindex="-1" role="dialog" aria-labelledby="modal-calibracao-label" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-calibracao-label">Data de Calibração</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-calibracao">
                    <input type="hidden" id="produto-calibracao" name="produto">
                    <input type="hidden" id="lote-calibracao" name="lote">
                    <input type="hidden" id="categoria-calibracao" name="categoria">
                    
                    <!-- Informações do Produto e Lote -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="produto-display">Produto:</label>
                                <input type="text" id="produto-display" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="lote-display">Lote:</label>
                                <input type="text" id="lote-display" class="form-control" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tabela de Calibração -->
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Série</th>
                                    <th>Pasta</th>
                                    <th>Data e Hora da Calibração</th>
                                    <th>Observação</th>
                                </tr>
                            </thead>
                            <tbody id="calibracao-tbody">
                                <!-- Linhas serão geradas dinamicamente -->
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Senha -->
                    <div class="form-group mt-3">
                        <label for="senha-calibracao">Senha:</label>
                        <input type="password" id="senha-calibracao" name="senha" class="form-control" placeholder="Digite sua senha">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="salvarCalibracao()">Salvar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Senha -->
<div class="modal fade" id="modal-senha" tabindex="-1" role="dialog" aria-labelledby="modal-senha-label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-senha-label">Confirmação de Senha</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="senha-confirmacao">Digite sua senha para confirmar:</label>
                    <input type="password" id="senha-confirmacao" class="form-control" placeholder="Senha">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="confirmarAcao()">Confirmar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
    .table td {
        white-space: nowrap;
        padding: 5px 8px;
    }
    .table thead th {
        background-color: #007bff !important;
        border-color: #0056b3 !important;
        color: white !important;
        font-weight: 100;
    }
    .form-control-sm {
        height: calc(1.5em + 0.5rem + 2px);
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        line-height: 1.5;
        border-radius: 0.2rem;
    }
    
    /* Estilos para a modal de calibração */
    .modal-lg .table th {
        background-color: #343a40 !important;
        color: white !important;
        border-color: #454d55 !important;
    }
    
    .input-group-text {
        background-color: #f8f9fa;
        border-color: #ced4da;
    }
    
    /* Cabeçalho centralizado */
    .text-center {
        text-align: center !important;
    }
    
    /* Estilos para validação de campos */
    .form-control.is-valid {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }
    
    .form-control.is-invalid {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }
    
    /* Estilo para campos de data */
    input[name^="calibracao_"] {
        font-family: 'Courier New', monospace;
        text-align: center;
        cursor: pointer;
    }
    
    /* Placeholder para campos de data */
    input[name^="calibracao_"]::placeholder {
        color: #6c757d;
        font-size: 0.8em;
    }
    
    /* Estilos para o datetime picker */
    .datetime-picker {
        background-color: #fff;
        cursor: pointer;
    }
    
    .datetime-picker:hover {
        background-color: #f8f9fa;
    }
    
    .datetime-picker:focus {
        background-color: #fff;
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    
    /* Estilo para o ícone do calendário */
    .input-group-text .fas.fa-calendar {
        color: #007bff;
        cursor: pointer;
    }
    
    .input-group-text:hover .fas.fa-calendar {
        color: #0056b3;
    }
    
    /* Responsividade para o modal */
    @media (max-width: 768px) {
        .modal-lg {
            max-width: 95%;
            margin: 10px;
        }
        
        .table-responsive {
            font-size: 0.875rem;
        }
    }
</style>
@stop

@section('js')
<script src="{{ asset('js/psp-rm.js') }}"></script>
@stop
