/**
 * JavaScript para funcionalidade R.D. & M.M. (psp-rm)
 * Refatorado a partir do arquivo cr_pst03.js
 *
 * @author Sistema SGCR
 */

// Variáveis globais
let acaoPendente = null;
let dadosPendentes = null;

// Função para mostrar alertas
function mostrarAlerta(mensagem, tipo = 'info') {
    const alertContainer = document.getElementById('alert-container');
    const alertId = 'alert-' + Date.now();
    
    const alertHtml = `
        <div id="${alertId}" class="alert alert-${tipo} alert-dismissible fade show" role="alert">
            ${mensagem}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;
    
    alertContainer.innerHTML = alertHtml;
    
    // Auto-remover após 5 segundos
    setTimeout(() => {
        const alert = document.getElementById(alertId);
        if (alert) {
            alert.remove();
        }
    }, 5000);
}

// Função para validar campos de busca
function validarCamposBusca() {
    const lote = document.getElementById('txtlote').value.trim();
    
    if (lote === '') {
        mostrarAlerta('INFORME O LOTE!', 'warning');
        document.getElementById('txtlote').focus();
        return false;
    }
    
    return true;
}

// Função para buscar produtos
function buscarProdutos() {
    if (!validarCamposBusca()) {
        return false;
    }
    
    const categoria = document.getElementById('cmbarea').value;
    const lote = document.getElementById('txtlote').value.trim();
    
    // Mostrar loading
    const tbody = document.getElementById('tbody-produtos');
    tbody.innerHTML = '<tr><td colspan="6" class="text-center"><i class="fas fa-spinner fa-spin"></i> Buscando produtos...</td></tr>';
    
    // Fazer requisição AJAX
    fetch(`/psp-rm/listar-produtos?categoria=${categoria}&lote=${lote}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.data && data.data.length > 0) {
                renderizarProdutos(data.data);
            } else {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center">NENHUM REGISTRO ENCONTRADO</td></tr>';
            }
        } else {
            mostrarAlerta(data.message || 'Erro ao buscar produtos', 'danger');
            tbody.innerHTML = '<tr><td colspan="6" class="text-center">Erro na busca</td></tr>';
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        mostrarAlerta('Erro interno do servidor', 'danger');
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">Erro na busca</td></tr>';
    });
}

// Função para renderizar produtos na tabela
function renderizarProdutos(produtos) {
    const tbody = document.getElementById('tbody-produtos');
    
    let html = '';
    produtos.forEach((produto, index) => {
        html += `
            <tr>
                <td>${produto.prod_cod510 || ''}</td>
                <td>
                    <input type="text" class="form-control form-control-sm" 
                           value="${produto.Num_Producoes || 0}" 
                           size="5" maxlength="4" 
                           onchange="verificarValor(this, '${produto.prod_cod510}', '${produto.lote || ''}', ${produto.categoria || 3})"
                           onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                </td>
                <td>${produto.p100dtcl || ''}</td>
                <td class="text-right">${produto.partidas || 0}</td>
                <td class="text-center">${produto.pst_serie || ''}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-outline-primary" 
                            onclick="abrirCalibracao('${produto.prod_cod510}', '${produto.lote || ''}', ${produto.categoria || 3})">
                        <i class="fas fa-cog"></i>
                    </button>
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

// Função para verificar valor do campo de produções
function verificarValor(campo, produto, lote, categoria) {
    const valor = parseInt(campo.value) || 0;
    
    if (valor < 0) {
        campo.value = 0;
        mostrarAlerta('Valor deve ser maior ou igual a zero', 'warning');
        return;
    }
    
    // Preparar dados para atualização
    dadosPendentes = {
        produto: produto,
        lote: lote,
        categoria: categoria,
        num_producoes: valor
    };
    
    acaoPendente = 'atualizar_producoes';
    
    // Abrir modal de senha
    $('#modal-senha').modal('show');
}

// Função para abrir modal de calibração
function abrirCalibracao(produto, lote, categoria) {
    // Preencher campos hidden
    document.getElementById('produto-calibracao').value = produto;
    document.getElementById('lote-calibracao').value = lote;
    document.getElementById('categoria-calibracao').value = categoria;
    
    // Preencher campos de exibição
    document.getElementById('produto-display').value = produto;
    document.getElementById('lote-display').value = lote;
    
    // Limpar formulário
    document.getElementById('form-calibracao').reset();
    
    // Gerar linhas da tabela de calibração (A, B, C, D, E)
    gerarLinhasCalibracao();
    
    // Buscar dados de calibração existentes
    fetch(`/psp-rm/abrir-calibracao?produto=${produto}&lote=${lote}&categoria=${categoria}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.data && data.data.length > 0) {
            // Preencher com dados existentes
            preencherDadosCalibracao(data.data);
        }
    })
    .catch(error => {
        console.error('Erro ao buscar dados de calibração:', error);
    });
    
    // Abrir modal
    $('#modal-calibracao').modal('show');
}

// Função para salvar calibração
function salvarCalibracao() {
    const produto = document.getElementById('produto-calibracao').value;
    const lote = document.getElementById('lote-calibracao').value;
    const categoria = document.getElementById('categoria-calibracao').value;
    
    const dadosCalibracao = {
        pst_serie: document.getElementById('pst-serie').value,
        pst_calibracao: document.getElementById('pst-calibracao').value,
        pst_producao: document.getElementById('pst-producao').value,
        pst_observacao: document.getElementById('pst-observacao').value
    };
    
    // Preparar dados para atualização
    dadosPendentes = {
        produto: produto,
        lote: lote,
        categoria: categoria,
        dados_calibracao: dadosCalibracao
    };
    
    acaoPendente = 'atualizar_calibracao';
    
    // Fechar modal de calibração
    $('#modal-calibracao').modal('hide');
    
    // Abrir modal de senha
    $('#modal-senha').modal('show');
}

// Função para confirmar ação após validação de senha
function confirmarAcao() {
    const senha = document.getElementById('senha-confirmacao').value;
    
    if (!senha) {
        mostrarAlerta('Digite sua senha', 'warning');
        return;
    }
    
    // Fechar modal de senha
    $('#modal-senha').modal('hide');
    
    // Limpar senha
    document.getElementById('senha-confirmacao').value = '';
    
    if (acaoPendente === 'atualizar_producoes') {
        atualizarProducoes(senha);
    } else if (acaoPendente === 'atualizar_calibracao') {
        atualizarCalibracao(senha);
    }
}

// Função para atualizar produções
function atualizarProducoes(senha) {
    const dados = dadosPendentes;
    dados.senha = senha;
    
    fetch('/psp-rm/atualizar-producoes', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        body: JSON.stringify(dados)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarAlerta('Produções atualizadas com sucesso', 'success');
            // Recarregar produtos
            buscarProdutos();
        } else {
            mostrarAlerta(data.message || 'Erro ao atualizar produções', 'danger');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        mostrarAlerta('Erro interno do servidor', 'danger');
    });
}

// Função para atualizar calibração
function atualizarCalibracao(senha) {
    const dados = dadosPendentes;
    dados.senha = senha;
    
    fetch('/psp-rm/atualizar-calibracao', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        body: JSON.stringify(dados)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarAlerta('Calibração atualizada com sucesso', 'success');
            // Recarregar produtos
            buscarProdutos();
        } else {
            mostrarAlerta(data.message || 'Erro ao atualizar calibração', 'danger');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        mostrarAlerta('Erro interno do servidor', 'danger');
    });
}

// Função para gerar linhas da tabela de calibração
function gerarLinhasCalibracao() {
    const tbody = document.getElementById('calibracao-tbody');
    tbody.innerHTML = '';
    
    const series = ['A', 'B', 'C', 'D', 'E'];
    
    series.forEach(serie => {
        const row = `
            <tr>
                <td class="text-center align-middle">${serie}</td>
                <td>
                    <div class="input-group">
                        <input type="text" class="form-control form-control-sm" 
                               name="calibracao_${serie}" 
                               placeholder="dd/MM/yyyy HH:mm"
                               maxlength="16">
                        <div class="input-group-append">
                            <span class="input-group-text">
                                <i class="fas fa-calendar"></i>
                            </span>
                        </div>
                    </div>
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm" 
                           name="observacao_${serie}" 
                           placeholder="Observação"
                           maxlength="100">
                </td>
            </tr>
        `;
        tbody.innerHTML += row;
    });
}

// Função para preencher dados de calibração existentes
function preencherDadosCalibracao(dados) {
    dados.forEach(calibracao => {
        const serie = calibracao.pst_serie;
        if (serie) {
            const inputCalibracao = document.querySelector(`input[name="calibracao_${serie}"]`);
            const inputObservacao = document.querySelector(`input[name="observacao_${serie}"]`);
            
            if (inputCalibracao) {
                inputCalibracao.value = calibracao.pst_calibracao || '';
            }
            if (inputObservacao) {
                inputObservacao.value = calibracao.pst_observacao || '';
            }
        }
    });
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Permitir busca ao pressionar Enter no campo lote
    document.getElementById('txtlote').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            buscarProdutos();
        }
    });
    
    // Limpar variáveis ao fechar modais
    $('#modal-senha').on('hidden.bs.modal', function() {
        acaoPendente = null;
        dadosPendentes = null;
        document.getElementById('senha-confirmacao').value = '';
    });
    
    $('#modal-calibracao').on('hidden.bs.modal', function() {
        document.getElementById('form-calibracao').reset();
    });
});
