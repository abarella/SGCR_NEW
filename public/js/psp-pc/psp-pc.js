/**
 * JavaScript para funcionalidades do PSP-PC (Pastas Não Concluídas)
 */

// Variáveis globais
var ordem = 1;
var produtos = [];
var pastaAtual = null;

// Inicialização
console.log('🚀 PSP-PC: Script carregado');

document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 PSP-PC: DOM carregado, inicializando sistema...');
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
        txtPstNumero.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                console.log('🔄 PSP-PC: Enter pressionado, recarregando lista...');
                carregarLista();
            }
        });
    }
    
    // Botão de pesquisa
    const btnPesquisar = document.getElementById('btnPesquisar');
    if (btnPesquisar) {
        btnPesquisar.addEventListener('click', () => {
            console.log('🔄 PSP-PC: Botão pesquisar clicado, recarregando lista...');
            carregarLista();
        });
    }
}

/**
 * Carrega lista de produtos
 */
function carregarProdutos() {
    console.log('🔧 PSP-PC: Carregando produtos...');
    
    return fetch('/psp-pc/produtos')
        .then(response => {
            console.log('📡 PSP-PC: Resposta de produtos recebida:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('✅ PSP-PC: Produtos carregados:', data);
            
            if (data.success) {
                produtos = data.data || [];
                const cmbProduto = document.getElementById('cmbProduto');
                
                if (cmbProduto) {
                    cmbProduto.innerHTML = '<option value="">Todos os produtos</option>';
                    
                    if (Array.isArray(produtos)) {
                        produtos.forEach(produto => {
                            const option = document.createElement('option');
                            option.value = produto.produto_codigo || '';
                            option.textContent = produto.nome_comercial || '';
                            cmbProduto.appendChild(option);
                        });
                    }
                }
            }
        })
        .catch(error => {
            console.error('❌ PSP-PC: Erro ao carregar produtos:', error);
            throw error;
        });
}

/**
 * Carrega lista de pastas não concluídas
 */
function carregarLista() {
    console.log('🔧 PSP-PC: Carregando lista de pastas...');
    
    const cmbProduto = document.getElementById('cmbProduto');
    const txtPstNumero = document.getElementById('txtPstNumero');
    
    const produto = cmbProduto ? cmbProduto.value : '';
    const pasta = txtPstNumero ? txtPstNumero.value : '';
    
    console.log('🔍 PSP-PC: Filtros aplicados:', { produto, pasta });
    
    fetch('/psp-pc/lista?produto=' + encodeURIComponent(produto) + '&pasta=' + encodeURIComponent(pasta), {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        console.log('📡 PSP-PC: Resposta de lista recebida:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('✅ PSP-PC: Lista carregada:', data);
        
        if (data.success) {
            preencherTabela(data.data || []);
        } else {
            console.error('❌ PSP-PC: Erro ao carregar lista:', data.message);
        }
    })
    .catch(error => {
        console.error('❌ PSP-PC: Erro ao carregar lista:', error);
    });
}

/**
 * Preenche a tabela com os dados
 */
function preencherTabela(data) {
    console.log('🔧 PSP-PC: Preenchendo tabela com', data.length, 'itens');
    
    const tbody = document.getElementById('tabelaPastasBody');
    if (!tbody) {
        console.error('❌ PSP-PC: Tbody não encontrado');
        return;
    }
    
    tbody.innerHTML = '';
    
    if (Array.isArray(data) && data.length > 0) {
        data.forEach((item, index) => {
            const tr = document.createElement('tr');
            
            tr.appendChild(criarCelula(gerarBotoesFuncoes(item)));
            tr.appendChild(criarCelula(item.pst_numero || ''));
            tr.appendChild(criarCelula(item.nome_comercial || item.pst_produto510 || ''));
            tr.appendChild(criarCelula(item.Lote || ''));
            tr.appendChild(criarCelula(item.pst_ano_lote || ''));
            tr.appendChild(criarCelula(item.pst_ano || ''));
            tr.appendChild(criarCelula(item.pst_registro || ''));
            tr.appendChild(criarCelula(item.pst_previsaoproducao || ''));
            tr.appendChild(criarCelula(item.producao_revisadopor || item.pessoaData || ''));
            tr.appendChild(criarCelula(item.pst_previsaocontrole || ''));
            tr.appendChild(criarCelula(item.controle_revisadopor || item.pessoaData2 || ''));
            tr.appendChild(criarCelula(item.status || ''));
            
            tbody.appendChild(tr);
        });
        
        console.log('✅ PSP-PC: Tabela preenchida com sucesso');
    } else {
        console.log('⚠️ PSP-PC: Nenhum dado para exibir');
        const tr = document.createElement('tr');
        const td = document.createElement('td');
        td.colSpan = 12;
        td.textContent = 'Nenhuma pasta encontrada';
        td.style.textAlign = 'center';
        tr.appendChild(td);
        tbody.appendChild(tr);
    }
}

/**
 * Cria uma célula da tabela
 */
function criarCelula(texto) {
    const td = document.createElement('td');
    td.innerHTML = texto || '';
    return td;
}

/**
 * Gera botões de funções para cada linha
 */
function gerarBotoesFuncoes(item) {
    console.log('🔧 PSP-PC: Gerando botões para item:', item);
    
    const pasta = item.pst_numero || '';
    const produto = item.pst_produto510 || item.nome_comercial || '';
    const lote = item.Lote || '';
    const data = item.pst_registro || '';
    const observacao = item.pst_observacao || '';
    const status = item.status || '';
    const prodstatus = item.status_producao || '';
    const ano = item.pst_ano || '';
    
    // Escapar valores para uso em onclick
    const pastaEsc = (pasta || '').replace(/'/g, "\\'");
    const produtoEsc = (produto || '').replace(/'/g, "\\'");
    const loteEsc = (lote || '').replace(/'/g, "\\'");
    const dataEsc = (data || '').replace(/'/g, "\\'");
    const observacaoEsc = (observacao || '').replace(/'/g, "\\'");
    const statusEsc = (status || '').replace(/'/g, "\\'");
    const prodstatusEsc = (prodstatus || '').replace(/'/g, "\\'");
    const anoEsc = (ano || '').replace(/'/g, "\\'");
    
    const html = `
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
    
    return html;
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
    if (mensagens) {
        mensagens.innerHTML = `
            <div class="alert alert-${tipo === 'erro' ? 'danger' : tipo === 'sucesso' ? 'success' : 'info'} alert-dismissible fade show" role="alert">
                ${texto}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        mensagens.style.display = 'block';
        
        // Auto-hide após 5 segundos
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
function abrirModalDocumentacao(pasta) {
    console.log('🔧 PSP-PC: Abrindo modal de documentação para pasta:', pasta);
    
    // Se pasta for uma string, criar objeto com os dados
    if (typeof pasta === 'string') {
        pastaAtual = {
            pst_numero: pasta,
            pst_produto510: arguments[1] || '',
            Lote: arguments[2] || '',
            pst_registro: arguments[3] || '',
            pst_observacao: arguments[4] || '',
            status: arguments[5] || '',
            prodstatus: arguments[6] || ''
        };
    } else {
        pastaAtual = pasta;
    }
    
    // Preencher informações da pasta
    document.getElementById('infoProduto').textContent = pastaAtual.pst_produto510 || '';
    document.getElementById('infoLote').textContent = pastaAtual.Lote || '';
    
    // Preencher campos ocultos
    document.getElementById('txtPasta').value = pastaAtual.pst_numero;
    document.getElementById('txtStatus').value = pastaAtual.status || '';
    document.getElementById('txtProdStatus').value = pastaAtual.prodstatus || '';
    
    // Limpar formulário
    document.getElementById('formDocumentacao').reset();
    
    // Mostrar modal primeiro
    $('#modalDocumentacao').modal('show');
    
    // Carregar combos após o modal estar visível
    setTimeout(() => {
        carregarCombosDocumentacao();
    }, 100);
}

function carregarCombosDocumentacao() {
    console.log('🔧 PSP-PC: Carregando combos do modal de documentação...');
    
    // Carregar situação da produção
    fetch('/psp-pc/status-producao')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const cmbSituacaoProducao = document.getElementById('cmbSituacaoProducao');
            if (cmbSituacaoProducao) {
                cmbSituacaoProducao.innerHTML = '<option value="">Selecione...</option>';
                
                if (data.data && Array.isArray(data.data)) {
                    data.data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.pstprod_status || '';
                        option.textContent = item.pstprod_descricao || '';
                        cmbSituacaoProducao.appendChild(option);
                    });
                }
            }
        }
    })
    .catch(error => {
        console.error('❌ PSP-PC: Erro ao carregar situação produção:', error);
    });
    
    // Carregar situação da pasta
    fetch('/psp-pc/status-pasta')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const cmbSituacaoPasta = document.getElementById('cmbSituacaoPasta');
            if (cmbSituacaoPasta) {
                cmbSituacaoPasta.innerHTML = '<option value="">Selecione...</option>';
                
                if (data.data && Array.isArray(data.data)) {
                    data.data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.pst_status || '';
                        option.textContent = item.pst_descricao || '';
                        cmbSituacaoPasta.appendChild(option);
                    });
                }
            }
        }
    })
    .catch(error => {
        console.error('❌ PSP-PC: Erro ao carregar situação pasta:', error);
    });
    
    // Carregar usuários revisores
    fetch('/psp-pc/usuarios-revisores')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const cmbUsuarioRevisor = document.getElementById('cmbUsuarioRevisor');
            if (cmbUsuarioRevisor) {
                cmbUsuarioRevisor.innerHTML = '<option value="">Selecione...</option>';
                
                if (data.data && Array.isArray(data.data)) {
                    data.data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.usuario_codigo || '';
                        option.textContent = item.usuario_nome || '';
                        cmbUsuarioRevisor.appendChild(option);
                    });
                }
            }
        }
    })
    .catch(error => {
        console.error('❌ PSP-PC: Erro ao carregar usuários revisores:', error);
    });
}

function abrirModalOcorrencias(pasta, produto, lote, status, prodstatus, ano) {
    console.log('🔧 PSP-PC: Abrindo modal de ocorrências para pasta:', pasta);
    pastaAtual = pasta;
    
    // Carregar conteúdo via AJAX
    fetch('/psp-pc/ocorrencias', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.text())
    .then(html => {
        document.getElementById('conteudoOcorrencias').innerHTML = html;
        $('#modalOcorrencias').modal('show');
    })
    .catch(error => {
        console.error('❌ PSP-PC: Erro ao carregar modal de ocorrências:', error);
        $('#modalOcorrencias').modal('show');
    });
}

function abrirModalLocalizar(pasta, produto, lote) {
    console.log('🔧 PSP-PC: Abrindo modal de localização para pasta:', pasta);
    pastaAtual = pasta;
    
    // Carregar conteúdo via AJAX
    fetch('/psp-pc/localizar', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.text())
    .then(html => {
        document.getElementById('conteudoLocalizar').innerHTML = html;
        $('#modalLocalizar').modal('show');
    })
    .catch(error => {
        console.error('❌ PSP-PC: Erro ao carregar modal de localização:', error);
        $('#modalLocalizar').modal('show');
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
        botoes.forEach(botao => {
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

// Configurar eventos do modal
document.addEventListener('DOMContentLoaded', function() {
    // Evento para mudança nos radio buttons
    document.addEventListener('change', 'input[name="tipoDoc"]', function() {
        if (pastaAtual) {
            listarValores();
        }
    });
    
    // Evento para abrir modal
    document.addEventListener('show.bs.modal', '#modalDocumentacao', function() {
        if (pastaAtual) {
            carregarCombosDocumentacao();
        }
    });
    
    // Evento para fechar modal
    document.addEventListener('hidden.bs.modal', '#modalDocumentacao', function() {
        pastaAtual = null;
        document.getElementById('formDocumentacao').reset();
    });
});

// Função para gravar documentação
function gravarDocumentacao() {
    console.log('🔧 PSP-PC: Gravando documentação...');
    
    // Validar campos obrigatórios
    const tipoDoc = document.querySelector('input[name="tipoDoc"]:checked');
    const dataEntrega = document.getElementById('txtDataEntrega').value;
    const situacaoProducao = document.getElementById('cmbSituacaoProducao').value;
    const situacaoPasta = document.getElementById('cmbSituacaoPasta').value;
    const usuarioRevisor = document.getElementById('cmbUsuarioRevisor').value;
    const observacao = document.getElementById('txtObservacao').value;
    const senha = document.getElementById('txtSenha').value;
    
    if (!tipoDoc) {
        alert('Selecione o tipo de documentação');
        return false;
    }
    
    if (!dataEntrega) {
        alert('Informe a data de entrega');
        return false;
    }
    
    if (!situacaoProducao) {
        alert('Selecione a situação da produção');
        return false;
    }
    
    if (!situacaoPasta) {
        alert('Selecione a situação da pasta');
        return false;
    }
    
    if (!usuarioRevisor) {
        alert('Selecione quem revisou');
        return false;
    }
    
    if (!observacao) {
        alert('Informe a observação');
        return false;
    }
    
    if (!senha) {
        alert('Informe a senha');
        return false;
    }
    
    // Preparar dados
    const dados = {
        pst_ano: dataEntrega.substring(0, 4),
        pst_numero: pastaAtual.pst_numero,
        pst_status: situacaoPasta,
        pst_prodstatus: situacaoProducao,
        pst_de: tipoDoc.value,
        pst_revisadopor: usuarioRevisor,
        pst_doc_data: dataEntrega,
        pst_observacao: observacao,
        cdusuario: document.getElementById('txtCDusuario').value,
        senha: senha
    };
    
    console.log('🔧 PSP-PC: Dados para envio:', dados);
    
    // Enviar dados
    fetch('/psp-pc/documentacao-salvar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(dados)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message || 'Documentação salva com sucesso!');
            document.getElementById('txtSenha').value = '';
            
            // Fechar modal e recarregar lista
            $('#modalDocumentacao').modal('hide');
            carregarLista();
        } else {
            alert('Erro: ' + (data.message || 'Erro ao salvar documentação'));
        }
    })
    .catch(error => {
        console.error('❌ PSP-PC: Erro ao salvar documentação:', error);
        alert('Erro ao salvar documentação. Verifique o console para mais detalhes.');
    });
}

// Função para listar valores baseado no tipo de documentação
function listarValores() {
    if (!pastaAtual) return;
    
    const tipoDoc = document.querySelector('input[name="tipoDoc"]:checked');
    if (!tipoDoc) return;
    
    console.log('🔧 PSP-PC: Listando valores para tipo:', tipoDoc.value);
    
    fetch('/psp-pc/ppst-lista4', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            pst_numero: pastaAtual.pst_numero,
            tipo: tipoDoc.value
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.data) {
            const dados = data.data;
            
            // Preencher data de entrega baseado no tipo
            if (tipoDoc.value === 'P') {
                // Documentação Produção
                if (dados.docum_receb) {
                    document.getElementById('txtDataEntrega').value = dados.docum_receb;
                }
                if (dados.pst_obsp) {
                    document.getElementById('txtObservacao').value = dados.pst_obsp || '';
                }
            } else if (tipoDoc.value === 'C') {
                // Documentação Controle
                if (dados.docum_reca) {
                    document.getElementById('txtDataEntrega').value = dados.docum_reca;
                }
                if (dados.pst_obsc) {
                    document.getElementById('txtObservacao').value = dados.pst_obsc || '';
                }
            }
            
            // Preencher outros campos
            if (dados.pstprod_status) {
                document.getElementById('cmbSituacaoProducao').value = dados.pstprod_status;
            }
            if (dados.revisadopor) {
                document.getElementById('cmbUsuarioRevisor').value = dados.revisadopor;
            }
            if (dados.pststs_codigo) {
                document.getElementById('cmbSituacaoPasta').value = dados.pststs_codigo;
            }
        }
    })
    .catch(error => {
        console.error('❌ PSP-PC: Erro ao carregar dados da documentação:', error);
    });
}

// Exportar funções para uso global
window.carregarLista = carregarLista;
window.trocarOrdem = trocarOrdem;
window.abrirModalDocumentacao = abrirModalDocumentacao;
window.abrirModalOcorrencias = abrirModalOcorrencias;
window.abrirModalLocalizar = abrirModalLocalizar;
window.esconderMensagem = esconderMensagem;
window.gravarDocumentacao = gravarDocumentacao;
window.listarValores = listarValores;

console.log('✅ PSP-PC: Script carregado com sucesso');
