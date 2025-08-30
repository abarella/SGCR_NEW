<!-- Conteúdo do Modal de Localização -->
<div class="container-fluid p-0">
    <!-- Mensagens -->
    <div id="mensagens-loc" class="alert" style="display: none;">
        <span id="mensagem-texto-loc"></span>
        <button type="button" class="close" onclick="esconderMensagemLoc()">
            <span>&times;</span>
        </button>
    </div>

    <!-- Botão Voltar -->
    <div class="mb-3">
        <button type="button" class="btn btn-secondary" onclick="voltarParaLista()">
            <i class="fas fa-arrow-left"></i> Voltar para Lista
        </button>
    </div>

    <!-- Informações da Pasta -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Informações da Pasta</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Pasta:</label>
                        <input type="text" class="form-control" value="{{ $pasta }}" readonly>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Produto:</label>
                        <input type="text" class="form-control" value="{{ $produto }}" readonly>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Lote:</label>
                        <input type="text" class="form-control" value="{{ $lote }}" readonly>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="button" class="btn btn-primary btn-block" onclick="buscarInformacoes()">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resultados da Busca -->
    <div id="resultadosBusca" style="display: none;">
        <!-- Informações Gerais -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Informações Gerais</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Número da Pasta:</label>
                            <input type="text" id="infoPasta" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Produto:</label>
                            <input type="text" id="infoProduto" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Lote:</label>
                            <input type="text" id="infoLote" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Ano:</label>
                            <input type="text" id="infoAno" class="form-control" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Data de Registro:</label>
                            <input type="text" id="infoDataRegistro" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Status:</label>
                            <input type="text" id="infoStatus" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Status Produção:</label>
                            <input type="text" id="infoStatusProducao" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Observações:</label>
                            <input type="text" id="infoObservacoes" class="form-control" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Documentação -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Documentação</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Documentação de Produção:</label>
                            <input type="text" id="infoDocProducao" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Revisado por:</label>
                            <input type="text" id="infoProducaoRevisado" class="form-control" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Documentação de Controle:</label>
                            <input type="text" id="infoDocControle" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Revisado por:</label>
                            <input type="text" id="infoControleRevisado" class="form-control" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Histórico de Alterações -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Histórico de Alterações</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Campo</th>
                                <th>Valor Anterior</th>
                                <th>Valor Novo</th>
                                <th>Usuário</th>
                            </tr>
                        </thead>
                        <tbody id="historicoBody">
                            <!-- Dados serão carregados via JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Ações -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Ações</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <button type="button" class="btn btn-primary btn-block" onclick="abrirDocumentacao()">
                            <i class="fas fa-edit"></i> Documentação
                        </button>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-warning btn-block" onclick="abrirOcorrencias()">
                            <i class="fas fa-cog"></i> Ocorrências
                        </button>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-info btn-block" onclick="imprimirRelatorio()">
                            <i class="fas fa-print"></i> Imprimir
                        </button>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-success btn-block" onclick="exportarDados()">
                            <i class="fas fa-download"></i> Exportar
                        </button>
                    </div>
                </div>
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
                <p class="mt-2">Carregando informações...</p>
            </div>
        </div>
    </div>
</div>

<style>
    .form-control[readonly] {
        background-color: #e9ecef;
    }
    
    .card {
        margin-bottom: 1rem;
    }
    
    .btn {
        margin-right: 0.5rem;
    }
    
    .table th {
        background-color: #f4f6f9;
    }
    
    .spinner-border {
        width: 3rem;
        height: 3rem;
    }
</style>

<script>
// Dados da pasta
const pastaData = {
    pasta: '{{ $pasta }}',
    produto: '{{ $produto }}',
    lote: '{{ $lote }}'
};

// Inicialização
document.addEventListener('DOMContentLoaded', function() {
    console.log('Modal de localização carregado para pasta:', pastaData.pasta);
});

// Buscar informações da pasta
function buscarInformacoes() {
    if (!pastaData.pasta) {
        mostrarMensagemLoc('Informe o número da pasta', 'warning');
        return;
    }
    
    mostrarCarregando();
    
    // Simular busca (aqui você implementaria a chamada real para a API)
    setTimeout(() => {
        // Dados mockados para demonstração
        const dados = {
            pasta: pastaData.pasta,
            produto: pastaData.produto,
            lote: pastaData.lote,
            ano: '2025',
            dataRegistro: '2025-01-15',
            status: 'Em Andamento',
            statusProducao: 'Pendente',
            observacoes: 'Pasta em análise para aprovação',
            docProducao: 'DOC-001',
            producaoRevisado: 'João Silva',
            docControle: 'CTRL-001',
            controleRevisado: 'Maria Santos'
        };
        
        preencherInformacoes(dados);
        preencherHistorico();
        esconderCarregando();
        document.getElementById('resultadosBusca').style.display = 'block';
        
    }, 1500);
}

// Preencher informações da pasta
function preencherInformacoes(dados) {
    document.getElementById('infoPasta').value = dados.pasta;
    document.getElementById('infoProduto').value = dados.produto;
    document.getElementById('infoLote').value = dados.lote;
    document.getElementById('infoAno').value = dados.ano;
    document.getElementById('infoDataRegistro').value = dados.dataRegistro;
    document.getElementById('infoStatus').value = dados.status;
    document.getElementById('infoStatusProducao').value = dados.statusProducao;
    document.getElementById('infoObservacoes').value = dados.observacoes;
    document.getElementById('infoDocProducao').value = dados.docProducao;
    document.getElementById('infoProducaoRevisado').value = dados.producaoRevisado;
    document.getElementById('infoDocControle').value = dados.docControle;
    document.getElementById('infoControleRevisado').value = dados.controleRevisado;
}

// Preencher histórico de alterações
function preencherHistorico() {
    const tbody = document.getElementById('historicoBody');
    const historico = [
        {
            data: '2025-01-15 10:30:00',
            campo: 'Status',
            valorAnterior: 'Pendente',
            valorNovo: 'Em Andamento',
            usuario: 'João Silva'
        },
        {
            data: '2025-01-14 14:20:00',
            campo: 'Observações',
            valorAnterior: '',
            valorNovo: 'Pasta em análise para aprovação',
            usuario: 'Maria Santos'
        }
    ];
    
    tbody.innerHTML = '';
    historico.forEach(item => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${item.data}</td>
            <td>${item.campo}</td>
            <td>${item.valorAnterior || '-'}</td>
            <td>${item.valorNovo}</td>
            <td>${item.usuario}</td>
        `;
        tbody.appendChild(tr);
    });
}

// Funções das ações
function abrirDocumentacao() {
    // Aqui você implementaria a abertura da documentação
    console.log('Abrindo documentação para pasta:', pastaData.pasta);
    mostrarMensagemLoc('Funcionalidade de documentação em desenvolvimento', 'info');
}

function abrirOcorrencias() {
    // Aqui você implementaria a abertura das ocorrências
    console.log('Abrindo ocorrências para pasta:', pastaData.pasta);
    mostrarMensagemLoc('Funcionalidade de ocorrências em desenvolvimento', 'info');
}

function imprimirRelatorio() {
    // Aqui você implementaria a impressão
    console.log('Imprimindo relatório para pasta:', pastaData.pasta);
    mostrarMensagemLoc('Funcionalidade de impressão em desenvolvimento', 'info');
}

function exportarDados() {
    // Aqui você implementaria a exportação
    console.log('Exportando dados da pasta:', pastaData.pasta);
    mostrarMensagemLoc('Funcionalidade de exportação em desenvolvimento', 'info');
}

// Utilitários
function mostrarMensagemLoc(texto, tipo) {
    const mensagensLoc = document.getElementById('mensagens-loc');
    const textoMensagemLoc = document.getElementById('mensagem-texto-loc');
    
    mensagensLoc.className = `alert alert-${tipo}`;
    textoMensagemLoc.textContent = texto;
    mensagensLoc.style.display = 'block';
    
    setTimeout(() => {
        esconderMensagemLoc();
    }, 5000);
}

function esconderMensagemLoc() {
    document.getElementById('mensagens-loc').style.display = 'none';
}

function mostrarCarregando() {
    $('#modalCarregando').modal('show');
}

function esconderCarregando() {
    $('#modalCarregando').modal('hide');
}

function voltarParaLista() {
    // Fechar modal e voltar para lista principal
    if (window.parent && window.parent.voltarParaLista) {
        window.parent.voltarParaLista();
    } else {
        // Fallback: fechar modal
        $('#modalLocalizar').modal('hide');
    }
}
</script>
