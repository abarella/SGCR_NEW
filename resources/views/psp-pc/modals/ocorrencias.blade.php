<!-- Conteúdo do Modal de Ocorrências -->
<div class="container-fluid p-0">
    <!-- Mensagens -->
    <div id="mensagens-oc" class="alert" style="display: none;">
        <span id="mensagem-texto-oc"></span>
        <button type="button" class="close" onclick="esconderMensagemOc()">
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
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Pasta:</label>
                        <input type="text" class="form-control" value="{{ $pasta }}" readonly>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Produto:</label>
                        <input type="text" class="form-control" value="{{ $produto }}" readonly>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Lote:</label>
                        <input type="text" class="form-control" value="{{ $lote }}" readonly>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Status:</label>
                        <input type="text" class="form-control" value="{{ $status }}" readonly>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Status Produção:</label>
                        <input type="text" class="form-control" value="{{ $prodstatus }}" readonly>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Ano:</label>
                        <input type="text" class="form-control" value="{{ $ano }}" readonly>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Ocorrências -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Ocorrências Registradas</h5>
            <div class="card-tools">
                <button type="button" class="btn btn-primary btn-sm" onclick="abrirModalNovaOcorrencia()">
                    <i class="fas fa-plus"></i> Nova Ocorrência
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="tabelaOcorrencias" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Tipo</th>
                            <th>Descrição</th>
                            <th>Responsável</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="tabelaOcorrenciasBody">
                        <!-- Dados serão carregados via JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nova Ocorrência -->
<div class="modal fade" id="modalNovaOcorrencia" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nova Ocorrência</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formNovaOcorrencia">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="txtDataOcorrencia">Data da Ocorrência *</label>
                                <input type="date" id="txtDataOcorrencia" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cmbTipoOcorrencia">Tipo de Ocorrência *</label>
                                <select id="cmbTipoOcorrencia" class="form-control" required>
                                    <option value="">Selecione...</option>
                                    <option value="PROBLEMA">Problema</option>
                                    <option value="MELHORIA">Melhoria</option>
                                    <option value="DUVIDA">Dúvida</option>
                                    <option value="OUTRO">Outro</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="txtDescricaoOcorrencia">Descrição *</label>
                                <textarea id="txtDescricaoOcorrencia" class="form-control" rows="3" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="txtResponsavelOcorrencia">Responsável *</label>
                                <input type="text" id="txtResponsavelOcorrencia" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cmbStatusOcorrencia">Status *</label>
                                <select id="cmbStatusOcorrencia" class="form-control" required>
                                    <option value="">Selecione...</option>
                                    <option value="ABERTA">Aberta</option>
                                    <option value="EM_ANDAMENTO">Em Andamento</option>
                                    <option value="RESOLVIDA">Resolvida</option>
                                    <option value="FECHADA">Fechada</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="salvarNovaOcorrencia()">Salvar</button>
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
</style>

<script>
// Dados da pasta
const pastaData = {
    pasta: '{{ $pasta }}',
    produto: '{{ $produto }}',
    lote: '{{ $lote }}',
    status: '{{ $status }}',
    prodstatus: '{{ $prodstatus }}',
    ano: '{{ $ano }}'
};

// Inicialização
document.addEventListener('DOMContentLoaded', function() {
    carregarOcorrencias();
});

// Carregar ocorrências
function carregarOcorrencias() {
    // Aqui você implementaria a chamada para carregar as ocorrências
    // Por enquanto, vamos usar dados mockados
    const ocorrencias = [
        {
            data: '2025-01-15',
            tipo: 'PROBLEMA',
            descricao: 'Falha no sistema de impressão',
            responsavel: 'João Silva',
            status: 'RESOLVIDA'
        },
        {
            data: '2025-01-20',
            tipo: 'MELHORIA',
            descricao: 'Sugestão de melhoria na interface',
            responsavel: 'Maria Santos',
            status: 'EM_ANDAMENTO'
        }
    ];
    
    preencherTabelaOcorrencias(ocorrencias);
}

// Preencher tabela de ocorrências
function preencherTabelaOcorrencias(ocorrencias) {
    const tbody = document.getElementById('tabelaOcorrenciasBody');
    tbody.innerHTML = '';
    
    ocorrencias.forEach(ocorrencia => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${ocorrencia.data}</td>
            <td>${ocorrencia.tipo}</td>
            <td>${ocorrencia.descricao}</td>
            <td>${ocorrencia.responsavel}</td>
            <td><span class="badge badge-${getBadgeClass(ocorrencia.status)}">${ocorrencia.status}</span></td>
            <td>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="editarOcorrencia('${ocorrencia.data}')">
                    <i class="fas fa-edit"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="excluirOcorrencia('${ocorrencia.data}')">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

// Obter classe do badge baseado no status
function getBadgeClass(status) {
    switch(status) {
        case 'ABERTA': return 'warning';
        case 'EM_ANDAMENTO': return 'info';
        case 'RESOLVIDA': return 'success';
        case 'FECHADA': return 'secondary';
        default: return 'secondary';
    }
}

// Abrir modal nova ocorrência
function abrirModalNovaOcorrencia() {
    $('#modalNovaOcorrencia').modal('show');
}

// Salvar nova ocorrência
function salvarNovaOcorrencia() {
    const form = document.getElementById('formNovaOcorrencia');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    // Aqui você implementaria a chamada para salvar
    // Por enquanto, apenas mostra uma mensagem de sucesso
    
    mostrarMensagem('Ocorrência salva com sucesso!', 'success');
    $('#modalNovaOcorrencia').modal('hide');
    
    // Recarregar ocorrências
    carregarOcorrencias();
}

// Editar ocorrência
function editarOcorrencia(data) {
    console.log('Editando ocorrência de:', data);
    // Implementar edição
}

// Excluir ocorrência
function excluirOcorrencia(data) {
    if (confirm('Tem certeza que deseja excluir esta ocorrência?')) {
        console.log('Excluindo ocorrência de:', data);
        // Implementar exclusão
        carregarOcorrencias();
    }
}

// Utilitários
function mostrarMensagem(texto, tipo) {
    const mensagens = document.getElementById('mensagens-oc');
    const textoMensagem = document.getElementById('mensagem-texto-oc');
    
    mensagens.className = `alert alert-${tipo}`;
    textoMensagem.textContent = texto;
    mensagens.style.display = 'block';
    
    setTimeout(() => {
        esconderMensagem();
    }, 5000);
}

function esconderMensagem() {
    document.getElementById('mensagens-oc').style.display = 'none';
}

function voltarParaLista() {
    // Fechar modal e voltar para lista principal
    if (window.parent && window.parent.voltarParaLista) {
        window.parent.voltarParaLista();
    } else {
        // Fallback: fechar modal
        $('#modalOcorrencias').modal('hide');
    }
}
</script>
