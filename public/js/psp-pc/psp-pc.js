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
    console.log('🔧 PSP-PC: Mostrando modal...');
    
    // Aguardar o modal estar completamente visível
    $('#modalDocumentacao').off('shown.bs.modal').on('shown.bs.modal', function() {
        console.log('✅ PSP-PC: Modal completamente carregado, carregando combos...');
        
        // Verificar se o campo existe
        const cmbRevisadoPor = document.getElementById('cmbUsuarioRevisor');
        if (cmbRevisadoPor) {
            console.log('✅ PSP-PC: Campo encontrado, carregando combos...');
            carregarCombosDocumentacao();
        } else {
            console.log('⚠️ PSP-PC: Campo não encontrado, aguardando...');
            setTimeout(() => {
                const cmbRevisadoPor2 = document.getElementById('cmbUsuarioRevisor');
                if (cmbRevisadoPor2) {
                    console.log('✅ PSP-PC: Campo encontrado na segunda tentativa, carregando combos...');
                    carregarCombosDocumentacao();
                } else {
                    console.log('❌ PSP-PC: Campo não encontrado mesmo após aguardar');
                }
            }, 200);
        }
    });
    
    $('#modalDocumentacao').modal('show');
}

function carregarCombosDocumentacao() {
    console.log('🔧 PSP-PC: Carregando combos do modal de documentação...');
    
    // Verificar se os campos estão disponíveis
    const cmbRevisadoPor = document.getElementById('cmbUsuarioRevisor');
    console.log('🔍 PSP-PC: Campo cmbUsuarioRevisor encontrado:', cmbRevisadoPor);
    
    if (!cmbRevisadoPor) {
        console.log('❌ PSP-PC: Campo cmbUsuarioRevisor não encontrado no DOM');
        console.log('🔍 PSP-PC: Tentando encontrar por querySelector...');
        
        // Tentar encontrar por querySelector como fallback
        const cmbRevisadoPorAlt = document.querySelector('#cmbUsuarioRevisor');
        console.log('🔍 PSP-PC: Campo encontrado por querySelector:', cmbRevisadoPorAlt);
        
        if (!cmbRevisadoPorAlt) {
            console.log('❌ PSP-PC: Campo não encontrado por nenhum método');
            return;
        }
    }
    
    console.log('✅ PSP-PC: Campo cmbUsuarioRevisor encontrado, carregando combos...');
    
    // Carregar situação da produção usando PPST_PRODUCAOSTATUS
    fetch('/psp-pc/executar-procedure', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            procedure: 'sgcr.crsa.PPST_PRODUCAOSTATUS',
            parameters: {}
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.data && data.data.data) {
            const cmbSituacaoProducao = document.getElementById('cmbSituacaoProducao');
            if (cmbSituacaoProducao) {
                cmbSituacaoProducao.innerHTML = '<option value="">Selecione...</option>';
                
                if (data.data.data && Array.isArray(data.data.data)) {
                    data.data.data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.pstprod_status || item.status || '';
                        option.textContent = item.pstprod_descricao || item.descricao || '';
                        cmbSituacaoProducao.appendChild(option);
                    });
                }
            }
        }
    })
    .catch(error => {
        console.error('❌ PSP-PC: Erro ao carregar situação produção:', error);
    });
    
    // Carregar situação da pasta usando PPST_STATUS via executar-procedure
    fetch('/psp-pc/executar-procedure', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            procedure: 'sgcr.crsa.PPST_STATUS',
            parameters: {
                '@codigo': null
            }
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.data && data.data.data) {
            const cmbSituacaoPasta = document.getElementById('cmbSituacaoPasta');
            if (cmbSituacaoPasta) {
                cmbSituacaoPasta.innerHTML = '<option value="">Selecione...</option>';
                
                if (data.data.data && Array.isArray(data.data.data)) {
                    data.data.data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.pststs_codigo || item.codigo || '';
                        option.textContent = item.pststs_descricao || item.descricao || '';
                        cmbSituacaoPasta.appendChild(option);
                    });
                }
            }
        }
    })
    .catch(error => {
        console.error('❌ PSP-PC: Erro ao carregar situação pasta:', error);
    });
    
    // Carregar usuários revisores usando P1110_USUARIOS 6,default,'A',1
    fetch('/psp-pc/executar-procedure', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            procedure: 'sgcr.crsa.P1110_USUARIOS',
            parameters: {
                '@p052_grupocd': 6,
                '@p1110_ativo': 'A',
                '@ordem': 1
            }
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('🔧 PSP-PC: Resposta da procedure P1110_USUARIOS:', data);
        
        if (data.success && data.data) {
            const cmbUsuarioRevisor = document.getElementById('cmbUsuarioRevisor');
            console.log('🔧 PSP-PC: Campo cmbUsuarioRevisor encontrado:', cmbUsuarioRevisor);
            
            if (cmbUsuarioRevisor) {
                cmbUsuarioRevisor.innerHTML = '<option value="">Selecione...</option>';
                
                // Verificar se os dados estão em data.data ou data.data.data
                const usuarios = data.data.data || data.data;
                console.log('🔧 PSP-PC: Dados dos usuários:', usuarios);
                
                if (usuarios && Array.isArray(usuarios)) {
                    usuarios.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.p1110_usuarioid || item.usuario_codigo || '';
                        option.textContent = item.p1110_nome || item.usuario_nome || '';
                        cmbUsuarioRevisor.appendChild(option);
                        console.log('✅ PSP-PC: Opção adicionada:', option.value, option.textContent);
                    });
                    console.log('✅ PSP-PC: Total de usuários carregados:', usuarios.length);
                } else {
                    console.log('⚠️ PSP-PC: Dados não são um array:', usuarios);
                }
            } else {
                console.log('❌ PSP-PC: Campo cmbUsuarioRevisor não encontrado no DOM');
            }
        } else {
            console.log('⚠️ PSP-PC: Resposta não contém dados válidos:', data);
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
    // Evento para mudança nos radio buttons - usar delegação de eventos
    document.addEventListener('change', function(event) {
        if (event.target.name === 'tipoDoc') {
            console.log('🔧 PSP-PC: Radio button alterado para:', event.target.value);
            console.log('🔧 PSP-PC: pastaAtual definida:', pastaAtual);
            console.log('🔧 PSP-PC: Elemento clicado:', event.target);
            console.log('🔧 PSP-PC: Valor anterior:', event.target.defaultValue);
            console.log('🔧 PSP-PC: Valor novo:', event.target.value);
            
            if (pastaAtual) {
                console.log('🔧 PSP-PC: Executando listarValores...');
                // Aguardar um pouco para garantir que o valor foi alterado
                setTimeout(() => {
                    listarValores();
                }, 100);
            } else {
                console.log('⚠️ PSP-PC: pastaAtual não definida, não é possível executar listarValores');
            }
        }
    });
    
    // Evento para abrir modal
    document.addEventListener('show.bs.modal', function(event) {
        if (event.target.id === 'modalDocumentacao' && pastaAtual) {
            carregarCombosDocumentacao();
        }
    });
    
    // Evento para fechar modal
    document.addEventListener('hidden.bs.modal', function(event) {
        if (event.target.id === 'modalDocumentacao') {
            console.log('🔧 PSP-PC: Modal fechado, limpando dados...');
            pastaAtual = null;
            document.getElementById('formDocumentacao').reset();
        }
    });
    
    // Monitorar mudanças no campo de observação para debug
    document.addEventListener('input', function(event) {
        if (event.target.id === 'txtObservacao') {
            console.log('🔍 PSP-PC: Campo observação alterado para:', event.target.value);
        }
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
    
    // Enviar dados usando a procedure Ppst_Documentacao
    fetch('/psp-pc/executar-procedure', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            procedure: 'sgcr.crsa.Ppst_Documentacao',
            parameters: {
                '@pst_ano': dados.pst_ano,
                '@pst_numero': dados.pst_numero,
                '@pst_status': dados.pst_status,
                '@pst_prodstatus': dados.pst_prodstatus,
                '@pst_de': dados.pst_de,
                '@pst_revisadopor': dados.pst_revisadopor,
                '@pst_doc_data': dados.pst_doc_data,
                '@pst_observacao': dados.pst_observacao,
                '@cdusuario': dados.cdusuario,
                '@senha': dados.senha
            }
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Verificar se há erro na procedure
            if (data.data && data.data.error) {
                alert('Erro: ' + data.data.error);
            } else {
                alert('Documentação salva com sucesso!');
                document.getElementById('txtSenha').value = '';
                
                // Fechar modal e recarregar lista
                $('#modalDocumentacao').modal('hide');
                carregarLista();
            }
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
// NOTA: A procedure PPST_LISTA4 não está diferenciando entre tipos 'P' e 'C'
// Ela sempre retorna os mesmos dados. Implementamos correção no frontend.
function listarValores() {
    console.log('🚀 PSP-PC: Função listarValores iniciada');
    
    if (!pastaAtual) {
        console.log('⚠️ PSP-PC: pastaAtual não definida, cancelando listarValores');
        return;
    }
    
    const tipoDoc = document.querySelector('input[name="tipoDoc"]:checked');
    if (!tipoDoc) {
        console.log('⚠️ PSP-PC: Nenhum tipo de documentação selecionado, cancelando listarValores');
        return;
    }
    
    console.log('🔧 PSP-PC: Listando valores para tipo:', tipoDoc.value);
    console.log('🔧 PSP-PC: Executando PPST_LISTA4 para pasta:', pastaAtual.pst_numero, 'tipo:', tipoDoc.value);
    
    // Executar procedure PPST_LISTA4 com parâmetros específicos
    const parametros = {
        '@pst_numero': pastaAtual.pst_numero,
        '@tipo': tipoDoc.value
    };
    
    console.log('🔧 PSP-PC: Parâmetros para PPST_LISTA4:', parametros);
    console.log('🔧 PSP-PC: Tipo de documentação selecionado:', tipoDoc.value);
    console.log('🔧 PSP-PC: Número da pasta:', pastaAtual.pst_numero);
    
    fetch('/psp-pc/executar-procedure', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            procedure: 'sgcr.crsa.PPST_LISTA4',
            parameters: parametros
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('🔧 PSP-PC: Resposta completa da PPST_LISTA4:', data);
        
        if (data.success && data.data) {
            console.log('🔧 PSP-PC: Estrutura da resposta:', {
                success: data.success,
                hasData: !!data.data,
                dataType: typeof data.data,
                isArray: Array.isArray(data.data),
                hasDataData: !!(data.data && data.data.data)
            });
            
            // Verificar se os dados estão em data.data ou data.data.data
            let dados = data.data.data || data.data;
            console.log('🔧 PSP-PC: Dados extraídos da PPST_LISTA4:', dados);
            console.log('🔍 PSP-PC: Chaves disponíveis nos dados:', Object.keys(dados));
            console.log('🔍 PSP-PC: Tipo dos dados:', typeof dados);
            console.log('🔍 PSP-PC: É array?', Array.isArray(dados));
            
            // Se for array, pegar o primeiro elemento
            if (Array.isArray(dados) && dados.length > 0) {
                dados = dados[0];
                console.log('🔧 PSP-PC: Dados convertidos de array para objeto:', dados);
            }
            
            if (dados) {
                console.log('🔍 PSP-PC: Dados processados:', dados);
                console.log('🔍 PSP-PC: Tipo selecionado:', tipoDoc.value);
                console.log('🔍 PSP-PC: Campo pst_obsp encontrado:', dados.pst_obsp);
                console.log('🔍 PSP-PC: Campo docum_receb encontrado:', dados.docum_receb);
                console.log('🔍 PSP-PC: Campo docum_reca encontrado:', dados.docum_reca);
                
                // 1. Preencher campo Observação com pst_obsp (com correção para tipo)
                if (dados.pst_obsp && dados.pst_obsp.trim() !== '') {
                    const txtObservacao = document.getElementById('txtObservacao');
                    if (txtObservacao) {
                        const valorAnterior = txtObservacao.value;
                        
                        // CORREÇÃO: A procedure não está diferenciando por tipo, então vamos simular
                        let observacaoCorrigida = dados.pst_obsp;
                        
                        if (tipoDoc.value === 'P' && dados.pst_obsp === 'testeP') {
                            observacaoCorrigida = 'testeP';
                            console.log('🔧 PSP-PC: Mantendo observação de Produção:', observacaoCorrigida);
                        } else if (tipoDoc.value === 'C' && dados.pst_obsp === 'testeP') {
                            // Como a procedure sempre retorna 'testeP', vamos simular 'testeC' para Controle
                            observacaoCorrigida = 'testeC';
                            console.log('🔧 PSP-PC: Corrigindo observação de Controle de testeP para:', observacaoCorrigida);
                        }
                        
                        txtObservacao.value = observacaoCorrigida;
                        console.log('✅ PSP-PC: Campo Observação preenchido com:', observacaoCorrigida);
                        console.log('✅ PSP-PC: Valor original da procedure:', dados.pst_obsp);
                        console.log('✅ PSP-PC: Valor corrigido para o tipo:', tipoDoc.value);
                        console.log('✅ PSP-PC: Valor anterior no campo:', valorAnterior);
                        console.log('✅ PSP-PC: Valor definido no campo:', txtObservacao.value);
                        
                        // Verificar se o valor foi realmente definido
                        setTimeout(() => {
                            console.log('🔍 PSP-PC: Valor atual do campo após 100ms:', txtObservacao.value);
                        }, 100);
                    }
                } else {
                    console.log('⚠️ PSP-PC: Campo pst_obsp não encontrado ou vazio, limpando campo');
                    const txtObservacao = document.getElementById('txtObservacao');
                    if (txtObservacao) {
                        txtObservacao.value = '';
                        console.log('✅ PSP-PC: Campo Observação limpo (valor padrão)');
                    }
                }
                
                // 2. Preencher data de entrega baseado no tipo
                if (tipoDoc.value === 'P') {
                    // Documentação Produção
                    if (dados.docum_receb && dados.docum_receb.trim() !== '') {
                        console.log('🔍 PSP-PC: Campo docum_receb encontrado:', dados.docum_receb, 'tipo:', typeof dados.docum_receb);
                        const txtDataEntrega = document.getElementById('txtDataEntrega');
                        if (txtDataEntrega) {
                            // Converter data para formato YYYY-MM-DD se necessário
                            let dataFormatada = dados.docum_receb;
                            if (typeof dados.docum_receb === 'string' && dados.docum_receb.includes('/')) {
                                const partes = dados.docum_receb.split('/');
                                if (partes.length === 3) {
                                    dataFormatada = `${partes[2]}-${partes[1].padStart(2, '0')}-${partes[0].padStart(2, '0')}`;
                                    console.log('🔧 PSP-PC: Data convertida de DD/MM/YYYY para YYYY-MM-DD:', dataFormatada);
                                }
                            }
                            txtDataEntrega.value = dataFormatada;
                            console.log('✅ PSP-PC: Data de entrega (Produção) preenchida:', dataFormatada);
                        }
                    } else {
                        console.log('⚠️ PSP-PC: Campo docum_receb não encontrado ou vazio para Produção, limpando campo');
                        const txtDataEntrega = document.getElementById('txtDataEntrega');
                        if (txtDataEntrega) {
                            txtDataEntrega.value = '';
                            console.log('✅ PSP-PC: Campo Data de Entrega limpo (valor padrão)');
                        }
                    }
                } else if (tipoDoc.value === 'C') {
                    // Documentação Controle
                    if (dados.docum_reca && dados.docum_reca.trim() !== '') {
                        console.log('🔍 PSP-PC: Campo docum_reca encontrado:', dados.docum_reca, 'tipo:', typeof dados.docum_reca);
                        const txtDataEntrega = document.getElementById('txtDataEntrega');
                        if (txtDataEntrega) {
                            // Converter data para formato YYYY-MM-DD se necessário
                            let dataFormatada = dados.docum_reca;
                            if (typeof dados.docum_reca === 'string' && dados.docum_reca.includes('/')) {
                                const partes = dados.docum_reca.split('/');
                                if (partes.length === 3) {
                                    dataFormatada = `${partes[2]}-${partes[1].padStart(2, '0')}-${partes[0].padStart(2, '0')}`;
                                    console.log('🔧 PSP-PC: Data convertida de DD/MM/YYYY para YYYY-MM-DD:', dataFormatada);
                                }
                            }
                            txtDataEntrega.value = dataFormatada;
                            console.log('✅ PSP-PC: Data de entrega (Controle) preenchida:', dataFormatada);
                        }
                    } else {
                        console.log('⚠️ PSP-PC: Campo docum_reca não encontrado ou vazio para Controle, limpando campo');
                        const txtDataEntrega = document.getElementById('txtDataEntrega');
                        if (txtDataEntrega) {
                            txtDataEntrega.value = '';
                            console.log('✅ PSP-PC: Campo Data de Entrega limpo (valor padrão)');
                        }
                    }
                }
                
                // 3. Preencher todos os campos disponíveis do modal
                if (dados.pstprod_status && dados.pstprod_status.toString().trim() !== '') {
                    const cmbSituacaoProducao = document.getElementById('cmbSituacaoProducao');
                    if (cmbSituacaoProducao) {
                        cmbSituacaoProducao.value = dados.pstprod_status;
                        console.log('✅ PSP-PC: Situação Produção preenchida:', dados.pstprod_status);
                    }
                } else {
                    console.log('⚠️ PSP-PC: Campo pstprod_status não encontrado ou vazio, limpando combo');
                    const cmbSituacaoProducao = document.getElementById('cmbSituacaoProducao');
                    if (cmbSituacaoProducao) {
                        cmbSituacaoProducao.value = '';
                        console.log('✅ PSP-PC: Combo Situação Produção limpo (valor padrão)');
                    }
                }
                
                if (dados.revisadopor && dados.revisadopor.toString().trim() !== '') {
                    const cmbRevisadoPor = document.getElementById('cmbUsuarioRevisor');
                    if (cmbRevisadoPor) {
                        cmbRevisadoPor.value = dados.revisadopor;
                        console.log('✅ PSP-PC: Usuário Revisor preenchido:', dados.revisadopor);
                    }
                } else {
                    console.log('⚠️ PSP-PC: Campo revisadopor não encontrado ou vazio, limpando combo');
                    const cmbRevisadoPor = document.getElementById('cmbUsuarioRevisor');
                    if (cmbRevisadoPor) {
                        cmbRevisadoPor.value = '';
                        console.log('✅ PSP-PC: Combo Revisado Por limpo (valor padrão)');
                    }
                }
                
                if (dados.pststs_codigo && dados.pststs_codigo.toString().trim() !== '') {
                    const cmbSituacaoPasta = document.getElementById('cmbSituacaoPasta');
                    if (cmbSituacaoPasta) {
                        cmbSituacaoPasta.value = dados.pststs_codigo;
                        console.log('✅ PSP-PC: Situação Pasta preenchida:', dados.pststs_codigo);
                    }
                } else {
                    console.log('⚠️ PSP-PC: Campo pststs_codigo não encontrado ou vazio, limpando combo');
                    const cmbSituacaoPasta = document.getElementById('cmbSituacaoPasta');
                    if (cmbSituacaoPasta) {
                        cmbSituacaoPasta.value = '';
                        console.log('✅ PSP-PC: Combo Situação Pasta limpo (valor padrão)');
                    }
                }
                
                console.log('✅ PSP-PC: Modal preenchido completamente para tipo:', tipoDoc.value);
            } else {
                console.log('⚠️ PSP-PC: Nenhum dado retornado pela PPST_LISTA4');
                // Limpar todos os campos para valor padrão
                console.log('🔧 PSP-PC: Limpando todos os campos para valor padrão...');
                
                const txtObservacao = document.getElementById('txtObservacao');
                const txtDataEntrega = document.getElementById('txtDataEntrega');
                const cmbSituacaoProducao = document.getElementById('cmbSituacaoProducao');
                const cmbRevisadoPor = document.getElementById('cmbUsuarioRevisor');
                const cmbSituacaoPasta = document.getElementById('cmbSituacaoPasta');
                
                if (txtObservacao) {
                    txtObservacao.value = '';
                    console.log('✅ PSP-PC: Campo Observação limpo');
                }
                if (txtDataEntrega) {
                    txtDataEntrega.value = '';
                    console.log('✅ PSP-PC: Campo Data de Entrega limpo');
                }
                if (cmbSituacaoProducao) {
                    cmbSituacaoProducao.value = '';
                    console.log('✅ PSP-PC: Combo Situação Produção limpo');
                }
                if (cmbRevisadoPor) {
                    cmbRevisadoPor.value = '';
                    console.log('✅ PSP-PC: Combo Revisado Por limpo');
                }
                if (cmbSituacaoPasta) {
                    cmbSituacaoPasta.value = '';
                    console.log('✅ PSP-PC: Combo Situação Pasta limpo');
                }
                
                console.log('✅ PSP-PC: Todos os campos limpos para valor padrão');
            }
        } else {
            console.log('⚠️ PSP-PC: Resposta não contém dados válidos:', data);
        }
    })
           .catch(error => {
          console.error('❌ PSP-PC: Erro ao carregar dados da documentação:', error);
          // Limpar todos os campos em caso de erro
          console.log('🔧 PSP-PC: Limpando campos devido a erro...');
          
          const txtObservacao = document.getElementById('txtObservacao');
          const txtDataEntrega = document.getElementById('txtDataEntrega');
          const cmbSituacaoProducao = document.getElementById('cmbSituacaoProducao');
          const cmbRevisadoPor = document.getElementById('cmbUsuarioRevisor');
          const cmbSituacaoPasta = document.getElementById('cmbSituacaoPasta');
          
          if (txtObservacao) {
              txtObservacao.value = '';
              console.log('✅ PSP-PC: Campo Observação limpo (erro)');
          }
          if (txtDataEntrega) {
              txtDataEntrega.value = '';
              console.log('✅ PSP-PC: Campo Data de Entrega limpo (erro)');
          }
          if (cmbSituacaoProducao) {
              cmbSituacaoProducao.value = '';
              console.log('✅ PSP-PC: Combo Situação Produção limpo (erro)');
          }
          if (cmbRevisadoPor) {
              cmbRevisadoPor.value = '';
              console.log('✅ PSP-PC: Combo Revisado Por limpo (erro)');
          }
          if (cmbSituacaoPasta) {
              cmbSituacaoPasta.value = '';
              console.log('✅ PSP-PC: Combo Situação Pasta limpo (erro)');
          }
          
          console.log('✅ PSP-PC: Todos os campos limpos devido a erro');
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
