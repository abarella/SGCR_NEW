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
                           onchange="verificarValor(this, '${produto.prod_cod510}', '${document.getElementById('txtlote').value}', ${produto.categoria || 3})"
                           onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                </td>
                <td>${produto.p100dtcl || ''}</td>
                <td class="text-right">${produto.partidas || 0}</td>
                <td class="text-center">${produto.pst_serie || ''}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-outline-primary" 
                            onclick="abrirCalibracao('${produto.prod_cod510}', '${document.getElementById('txtlote').value}', ${produto.categoria || 3})">
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
    
    // Preencher campos de exibição (remover espaços em branco)
    document.getElementById('produto-display').value = produto.trim();
    document.getElementById('lote-display').value = lote.trim();
    
    // Limpar apenas os campos de calibração (não os campos de produto/lote)
    const tbody = document.getElementById('calibracao-tbody');
    tbody.innerHTML = '';
    
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
    const senha = document.getElementById('senha-calibracao').value;
    
    // Verificar se a senha foi informada
    if (!senha) {
        mostrarAlerta('Digite sua senha para confirmar', 'warning');
        document.getElementById('senha-calibracao').focus();
        return;
    }
    
    // Coletar dados de todas as séries que existem na tabela
    const dadosCalibracao = [];
    const tbody = document.getElementById('calibracao-tbody');
    const rows = tbody.querySelectorAll('tr');
    
    rows.forEach(row => {
        const inputs = row.querySelectorAll('input');
        if (inputs.length >= 3) {
            const inputPasta = inputs[0];
            const inputCalibracao = inputs[1];
            const inputObservacao = inputs[2];
            
            // Extrair o nome da série do nome do input
            const nomeInput = inputCalibracao.name;
            const serie = nomeInput.replace('calibracao_', '');
            
            const pasta = inputPasta.value.trim();
            const calibracao = inputCalibracao.value.trim();
            const observacao = inputObservacao.value.trim();
            
            // Converter valor do datetime-local para formato dd/MM/yyyy HH:mm
            let dataFormatada = '';
            if (calibracao) {
                try {
                    const data = new Date(calibracao);
                    if (!isNaN(data.getTime())) {
                        const dia = String(data.getDate()).padStart(2, '0');
                        const mes = String(data.getMonth() + 1).padStart(2, '0');
                        const ano = data.getFullYear();
                        const hora = String(data.getHours()).padStart(2, '0');
                        const minuto = String(data.getMinutes()).padStart(2, '0');
                        dataFormatada = `${dia}/${mes}/${ano} ${hora}:${minuto}`;
                    } else {
                        mostrarAlerta(`Data inválida para série ${serie}`, 'warning');
                        inputCalibracao.focus();
                        return;
                    }
                } catch (e) {
                    mostrarAlerta(`Erro ao processar data para série ${serie}`, 'warning');
                    inputCalibracao.focus();
                    return;
                }
            }
            
            // Só incluir se houver dados para salvar
            if (calibracao || observacao || pasta) {
                dadosCalibracao.push({
                    pst_serie: serie,
                    pst_calibracao: dataFormatada, // Usar a data formatada
                    pst_producao: '', // Campo não usado na tabela atual
                    pst_numero: pasta, // Campo pasta
                    pst_observacao: observacao
                });
            }
        }
    });
    
    // Verificar se há dados para salvar
    if (dadosCalibracao.length === 0) {
        mostrarAlerta('Nenhum dado de calibração foi informado', 'warning');
        return;
    }
    
    // Preparar dados para atualização
    dadosPendentes = {
        produto: produto,
        lote: lote,
        categoria: categoria,
        dados_calibracao: dadosCalibracao,
        senha: senha
    };
    
    acaoPendente = 'atualizar_calibracao';
    
    // Executar diretamente (sem abrir modal de senha)
    atualizarCalibracao(senha);
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
    
    // Validar dados antes de enviar
    if (!dados.produto || !dados.lote || !dados.categoria || dados.num_producoes === undefined) {
        mostrarAlerta('Dados incompletos para atualização', 'warning');
        return;
    }
    
    // Truncar produto para máximo 10 caracteres
    dados.produto = dados.produto.substring(0, 10);
    
    // Truncar senha para máximo 6 caracteres
    dados.senha = dados.senha.substring(0, 6);
    
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
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            mostrarAlerta(data.message || 'Produções atualizadas com sucesso', 'success');
            // Recarregar produtos
            buscarProdutos();
            // Fechar modal
            $('#modal-senha').modal('hide');
        } else {
            mostrarAlerta(data.message || 'Erro ao atualizar produções', 'danger');
        }
    })
    .catch(error => {
        console.error('Erro na requisição:', error);
        
        if (error.message.includes('HTTP 422')) {
            mostrarAlerta('Dados inválidos. Verifique os campos.', 'warning');
        } else if (error.message.includes('HTTP 500')) {
            mostrarAlerta('Erro interno do servidor. Tente novamente.', 'danger');
        } else {
            mostrarAlerta('Erro na comunicação com o servidor: ' + error.message, 'danger');
        }
    });
}

// Função para atualizar calibração
function atualizarCalibracao(senha) {
    const dados = dadosPendentes;
    // A senha já está em dadosPendentes.senha, não precisa sobrescrever
    
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
            // Fechar modal
            $('#modal-calibracao').modal('hide');
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
    
    // Aguardar os dados da procedure serem carregados
    // As linhas serão preenchidas pela função preencherDadosCalibracao
}

// Função para preencher dados de calibração
function preencherDadosCalibracao(dados) {
    const tbody = document.getElementById('calibracao-tbody');
    tbody.innerHTML = '';
    
    if (!dados || dados.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center">Nenhuma série autorizada encontrada</td></tr>';
        return;
    }
    
            // Gerar linhas baseadas nos dados da procedure
        dados.forEach(calibracao => {
            const serie = calibracao.pst_serie || calibracao.serie || '';
            const pasta = calibracao.pst_numero || '';
            let dataCalibracao = calibracao.pst_calibracao || '';
            const observacao = calibracao.pst_observacao || '';
            
            // Converter data para formato aceito pelo input datetime-local (YYYY-MM-DDTHH:mm)
            if (dataCalibracao) {
                try {
                    // Se a data está no formato dd/MM/yyyy HH:mm, converter para YYYY-MM-DDTHH:mm
                    if (dataCalibracao.match(/^\d{2}\/\d{2}\/\d{4} \d{2}:\d{2}$/)) {
                        const [data, hora] = dataCalibracao.split(' ');
                        const [dia, mes, ano] = data.split('/');
                        const [h, m] = hora.split(':');
                        dataCalibracao = `${ano}-${mes.padStart(2, '0')}-${dia.padStart(2, '0')}T${h.padStart(2, '0')}:${m.padStart(2, '0')}`;
                    }
                    // Se a data está no formato dd/MM/yyyy, converter para YYYY-MM-DDTHH:mm (adicionar hora 00:00)
                    else if (dataCalibracao.match(/^\d{2}\/\d{2}\/\d{4}$/)) {
                        const [dia, mes, ano] = dataCalibracao.split('/');
                        dataCalibracao = `${ano}-${mes.padStart(2, '0')}-${dia.padStart(2, '0')}T00:00`;
                    }
                } catch (e) {
                    dataCalibracao = '';
                }
            }
            
            const row = `
            <tr>
                <td class="text-center align-middle">${serie}</td>
                                 <td>
                     <input type="text" class="form-control form-control-sm"
                            id="pasta_${serie}"
                            name="pasta_${serie}"
                            value="${pasta}"
                            placeholder="Pasta"
                            maxlength="10"
                            readonly>
                 </td>
                <td>
                    <input type="datetime-local" class="form-control form-control-sm"
                           id="calibracao_${serie}"
                           name="calibracao_${serie}"
                           value="${dataCalibracao}"
                           step="900">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm"
                           name="observacao_${serie}"
                           value="${observacao}"
                           placeholder="Observação"
                           maxlength="100">
                </td>
            </tr>
        `;
        tbody.innerHTML += row;
    });
    
    // Não é mais necessário inicializar datetime pickers
    // Os campos agora usam input nativo datetime-local
}

// Função para inicializar datetime pickers - REMOVIDA
// Agora usamos input nativo datetime-local

// Função para validar formato de data - REMOVIDA
// O input datetime-local faz a validação automaticamente

// Funções de validação de data - REMOVIDAS
// O input datetime-local faz a validação automaticamente

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
        // Limpar apenas os campos de calibração, não os campos de produto/lote
        const tbody = document.getElementById('calibracao-tbody');
        tbody.innerHTML = '';
        document.getElementById('senha-calibracao').value = '';
    });
    
    // Verificação do TempusDominus removida - agora usamos input nativo datetime-local
});
