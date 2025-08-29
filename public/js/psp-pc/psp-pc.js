/**
 * JavaScript para funcionalidades do PSP-PC (Pastas Não Concluídas)
 * Baseado nos arquivos originais: cr_pst03.js, cr_calibracao.js
 */

// Variáveis globais
let ordem = 1;
let produtos = [];
let pastaAtual = null;

// Inicialização
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 PSP-PC: Inicializando sistema...');
    inicializarSistema();
});

/**
 * Inicializa o sistema
 */
function inicializarSistema() {
    console.log('🔧 PSP-PC: Configurando sistema...');
    configurarEventos();
    
    // Carregar produtos primeiro, depois a lista
    carregarProdutos().then(() => {
        console.log('✅ PSP-PC: Produtos carregados, carregando lista...');
        carregarLista();
    }).catch(error => {
        console.error('⚠️ PSP-PC: Erro ao carregar produtos, tentando carregar lista mesmo assim:', error);
        carregarLista();
    });
}

/**
 * Configura eventos da página
 */
function configurarEventos() {
    console.log('🔧 PSP-PC: Configurando eventos...');
    
    // Filtros
    const cmbProduto = document.getElementById('cmbProduto');
    const txtPstNumero = document.getElementById('txtPstNumero');
    
    if (cmbProduto) {
        cmbProduto.addEventListener('change', () => {
            console.log('🔄 PSP-PC: Produto alterado, recarregando lista...');
            carregarLista();
        });
    }
    
    if (txtPstNumero) {
        txtPstNumero.addEventListener('change', () => {
            console.log('🔄 PSP-PC: Pasta alterada, recarregando lista...');
            carregarLista();
        });
        txtPstNumero.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                carregarLista();
            }
        });
    }
}

/**
 * Carrega produtos para o combo
 */
function carregarProdutos() {
    console.log('🔄 PSP-PC: Carregando produtos...');
    
    const combo = document.getElementById('cmbProduto');
    if (!combo) {
        return Promise.reject('Combo não encontrado');
    }
    
    return fetch('/psp-pc/produtos')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('✅ PSP-PC: Produtos carregados com sucesso:', data.data.length);
                
                combo.innerHTML = '<option value="">Todos os produtos</option>';
                
                data.data.forEach(produto => {
                    const option = document.createElement('option');
                    option.value = produto.codigo;
                    option.textContent = produto.nome_comercial;
                    combo.appendChild(option);
                });
                
                produtos = data.data;
                return data.data;
            } else {
                throw new Error(data.message || 'Erro desconhecido');
            }
        })
        .catch(error => {
            console.error('❌ PSP-PC: Erro ao carregar produtos:', error);
            throw error;
        });
}

/**
 * Carrega lista de pastas
 */
function carregarLista(pagina = 1) {
    console.log('🔄 PSP-PC: Carregando lista de pastas, página:', pagina);
    
    const tbody = document.getElementById('tabelaPastasBody');
    if (!tbody) {
        console.error('❌ PSP-PC: Tbody da tabela não encontrado');
        return;
    }
    
    tbody.innerHTML = '<tr><td colspan="13" class="text-center"><i class="fas fa-spinner fa-spin"></i> Carregando...</td></tr>';
    
    const produto = document.getElementById('cmbProduto')?.value || '';
    const pasta = document.getElementById('txtPstNumero')?.value || '';
    
    const params = new URLSearchParams();
    if (produto) params.append('produto', produto);
    if (pasta) params.append('pasta', pasta);
    if (pagina > 1) params.append('pagina', pagina);
    
    const url = `/psp-pc/lista?${params.toString()}`;
    console.log('🔧 PSP-PC: Fazendo requisição para:', url);
    
    fetch(url)
        .then(response => {
            console.log('📡 PSP-PC: Resposta recebida:', response.status, response.statusText);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('📊 PSP-PC: Dados recebidos:', data);
            if (data.success) {
                console.log('✅ PSP-PC: Lista carregada com sucesso:', data.data.length, 'registros');
                preencherTabela(data.data);
            } else {
                throw new Error(data.message || 'Erro desconhecido');
            }
        })
        .catch(error => {
            console.error('❌ PSP-PC: Erro ao carregar lista:', error);
            tbody.innerHTML = '<tr><td colspan="13" class="text-center text-danger">Erro ao carregar dados: ' + error.message + '</td></tr>';
        });
}

/**
 * Preenche a tabela com os dados
 */
function preencherTabela(dados) {
    const tbody = document.getElementById('tabelaPastasBody');
    if (!tbody) return;
    
    tbody.innerHTML = '';
    
    if (!dados || dados.length === 0) {
        tbody.innerHTML = '<tr><td colspan="13" class="text-center">Nenhuma pasta encontrada</td></tr>';
        return;
    }
    
    dados.forEach((item, index) => {
        const tr = document.createElement('tr');
        
        // Coluna de Funções
        const tdFuncoes = document.createElement('td');
        tdFuncoes.innerHTML = gerarBotoesFuncoes(item);
        tr.appendChild(tdFuncoes);
        
        // Outras colunas
        tr.appendChild(criarCelula(item.pst_numero || ''));
        
        const tdProdutoHidden = document.createElement('td');
        tdProdutoHidden.style.display = 'none';
        tdProdutoHidden.textContent = item.pst_produto510 || item.nome_comercial || '';
        tr.appendChild(tdProdutoHidden);
        
        tr.appendChild(criarCelula(item.pst_produto510 || item.nome_comercial || ''));
        tr.appendChild(criarCelula(item.Lote || ''));
        tr.appendChild(criarCelula(item.pst_ano_lote || ''));
        tr.appendChild(criarCelula(item.pst_ano || ''));
        tr.appendChild(criarCelula(item.pst_registro || ''));
        tr.appendChild(criarCelula(item.pst_previsaoproducao || ''));
        
        const producaoRevisado = item.pessoaData || item.producao_revisadopor || '';
        tr.appendChild(criarCelula(producaoRevisado));
        
        tr.appendChild(criarCelula(item.pst_previsaocontrole || ''));
        
        const controleRevisado = item.pessoaData2 || item.controle_revisadopor || '';
        tr.appendChild(criarCelula(controleRevisado));
        
        tr.appendChild(criarCelula(item.status || ''));
        
        tbody.appendChild(tr);
    });
    
    console.log('✅ PSP-PC: Tabela preenchida com sucesso');
}

/**
 * Cria uma célula da tabela
 */
function criarCelula(texto) {
    const td = document.createElement('td');
    td.textContent = texto || '';
    return td;
}

/**
 * Gera botões de funções para cada linha
 */
function gerarBotoesFuncoes(item) {
    const pasta = item.pst_numero || '';
    const produto = item.pst_produto510 || item.nome_comercial || '';
    const lote = item.Lote || '';
    const data = item.pst_registro || '';
    const observacao = item.pst_observacao || '';
    const status = item.status || '';
    const prodstatus = item.status_producao || '';
    const ano = item.pst_ano || '';
    
    // Escapar valores
    const pastaEsc = pasta.replace(/'/g, "\\'");
    const produtoEsc = produto.replace(/'/g, "\\'");
    const loteEsc = lote.replace(/'/g, "\\'");
    const dataEsc = data.replace(/'/g, "\\'");
    const observacaoEsc = observacao.replace(/'/g, "\\'");
    const statusEsc = status.replace(/'/g, "\\'");
    const prodstatusEsc = prodstatus.replace(/'/g, "\\'");
    const anoEsc = ano.replace(/'/g, "\\'");
    
    return `
        <div class="botoes-container" style="display: flex; gap: 1px; justify-content: center; align-items: center;">
            <button type="button" class="btn btn-sm btn-outline-primary" 
                    onclick="abrirModalDocumentacao('${pastaEsc}', '${produtoEsc}', '${loteEsc}', '${dataEsc}', '${observacaoEsc}', '${statusEsc}', '${prodstatusEsc}')" 
                    title="Registrar documentação">
                <i class="fas fa-edit"></i>
            </button>
            <button type="button" class="btn btn-sm btn-outline-warning" 
                    onclick="abrirModalOcorrencias('${pastaEsc}', '${produtoEsc}', '${loteEsc}', '${statusEsc}', '${prodstatusEsc}', '${anoEsc}')" 
                    title="Registrar ocorrências">
                <i class="fas fa-cog"></i>
            </button>
            <button type="button" class="btn btn-sm btn-outline-info" 
                    onclick="abrirModalLocalizar('${pastaEsc}', '${produtoEsc}', '${loteEsc}')" 
                    title="Localizar">
                <i class="fas fa-search"></i>
            </button>
        </div>
    `;
}

/**
 * Troca a ordem da lista
 */
function trocarOrdem(novaOrdem) {
    console.log('🔄 PSP-PC: Alterando ordem para:', novaOrdem);
    ordem = novaOrdem;
    carregarLista();
}

/**
 * Mostra mensagem para o usuário
 */
function mostrarMensagem(texto, tipo = 'info') {
    console.log(`💬 PSP-PC: Mensagem [${tipo}]:`, texto);
    
    const mensagens = document.getElementById('mensagens');
    const mensagemTexto = document.getElementById('mensagem-texto');
    
    if (mensagens && mensagemTexto) {
        mensagemTexto.textContent = texto;
        mensagens.className = `alert alert-${tipo}`;
        mensagens.style.display = 'block';
        
        setTimeout(() => {
            esconderMensagem();
        }, 5000);
    }
}

/**
 * Esconde mensagem
 */
function esconderMensagem() {
    const mensagens = document.getElementById('mensagens');
    if (mensagens) {
        mensagens.style.display = 'none';
    }
}

// Funções dos modais
function abrirModalDocumentacao(pasta, produto, lote, data, observacao, status, prodstatus) {
    console.log('🔧 PSP-PC: Abrindo modal de documentação para pasta:', pasta);
    pastaAtual = pasta;
    
    $.ajax({
        url: '/psp-pc/documentacao',
        method: 'GET',
        data: { pasta: pasta },
        success: function(response) {
            if (response.success) {
                $('#conteudoDocumentacao').html(response.view);
                $('#modalDocumentacao').modal('show');
            } else {
                mostrarMensagem('Erro ao carregar dados da pasta: ' + response.message, 'danger');
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ PSP-PC: Erro ao abrir modal de documentação:', error);
            mostrarMensagem('Erro ao carregar dados da pasta', 'danger');
        }
    });
}

function abrirModalOcorrencias(pasta, produto, lote, status, prodstatus, ano) {
    console.log('🔧 PSP-PC: Abrindo modal de ocorrências para pasta:', pasta);
    pastaAtual = pasta;
    
    $.ajax({
        url: '/psp-pc/ocorrencias',
        method: 'GET',
        data: { pasta: pasta },
        success: function(response) {
            if (response.success) {
                $('#conteudoOcorrencias').html(response.view);
                $('#modalOcorrencias').modal('show');
            } else {
                mostrarMensagem('Erro ao carregar dados da pasta: ' + response.message, 'danger');
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ PSP-PC: Erro ao abrir modal de ocorrências:', error);
            mostrarMensagem('Erro ao carregar dados da pasta', 'danger');
        }
    });
}

function abrirModalLocalizar(pasta, produto, lote) {
    console.log('🔧 PSP-PC: Abrindo modal de localização para pasta:', pasta);
    pastaAtual = pasta;
    
    $.ajax({
        url: '/psp-pc/localizar',
        method: 'GET',
        data: { pasta: pasta },
        success: function(response) {
            if (response.success) {
                $('#conteudoLocalizar').html(response.view);
                $('#modalLocalizar').modal('show');
            } else {
                mostrarMensagem('Erro ao carregar dados da pasta: ' + response.message, 'danger');
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ PSP-PC: Erro ao abrir modal de localização:', error);
            mostrarMensagem('Erro ao carregar dados da pasta', 'danger');
        }
    });
}

// Função para manter botões alinhados
function manterBotoesAlinhados() {
    const containers = document.querySelectorAll('.botoes-container');
    containers.forEach(container => {
        container.style.display = 'flex';
        container.style.flexDirection = 'row';
        container.style.gap = '1px';
        container.style.justifyContent = 'center';
        container.style.alignItems = 'center';
        container.style.width = '100%';
        container.style.height = '36px';
        
        const botoes = container.querySelectorAll('.btn');
        botoes.forEach((botao, index) => {
            botao.style.width = '36px';
            botao.style.height = '36px';
            botao.style.margin = '0';
            botao.style.padding = '6px 10px';
            botao.classList.remove('me-1', 'me-2', 'me-3', 'ms-1', 'ms-2', 'ms-3', 'm-1', 'm-2', 'm-3');
        });
    });
}

// Executar manutenção dos botões periodicamente
setInterval(manterBotoesAlinhados, 3000);

// Exportar funções para uso global
window.carregarLista = carregarLista;
window.trocarOrdem = trocarOrdem;
window.abrirModalDocumentacao = abrirModalDocumentacao;
window.abrirModalOcorrencias = abrirModalOcorrencias;
window.abrirModalLocalizar = abrirModalLocalizar;
window.esconderMensagem = esconderMensagem;
