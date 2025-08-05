// Funções globais para serem acessíveis pelos botões
window.atualizaLoteDefinicao = function() {
    const produtoSelect = document.getElementById('cmbprod');
    if (!produtoSelect) {
        console.error("Erro: 'produto' não encontrado no DOM.");
        return;
    }

    const produtoSelecionado = produtoSelect.value;
    console.log("Produto selecionado:", produtoSelecionado);

    if (!produtoSelecionado || produtoSelecionado === "0") {
        console.log("Nenhum produto selecionado, saindo da função.");
        return;
    }

    // Limpar o select antes de enviar a requisição
    document.getElementById('loteContainer').innerHTML = 
        "<select id='cmbLote' name='cmbLote' class='form-control'><option value='0'>Carregando...</option></select>";

    // Fazer a requisição AJAX para o servidor
    fetch('/dfv-ds/carregar-lotes', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            produto: produtoSelecionado
        })
    })
    .then(response => response.json())
    .then(data => {
        let html = '<select id="cmbLote" name="cmbLote" class="form-control">';
        
        data.lotes.forEach(lote => {
            html += `<option value="${lote.lote}">${lote.lote}</option>`;
        });
        
        html += '</select>';
        document.getElementById('loteContainer').innerHTML = html;
    })
    .catch(error => {
        console.error('Erro:', error);
        document.getElementById('loteContainer').innerHTML = 
            "<select id='cmbLote' name='cmbLote' class='form-control'><option value='0'>Erro ao carregar</option></select>";
    });
};

window.pesquisaListaSerie = function() {
    const produtoSelect = document.getElementById('cmbprod');
    const loteSelect = document.getElementById('cmbLote');

    if (!produtoSelect || !loteSelect) {
        console.error('Campos de pesquisa não encontrados.');
        return;
    }

    const produtoSelecionado = produtoSelect.value.trim();
    const loteSelecionado = loteSelect.value.trim();

    if (produtoSelecionado == 0 || loteSelecionado == 0) {
        alert("Por favor, selecione o produto e o lote.");
        return;
    }

    fetch('/dfv-ds/pesquisar-lista-serie', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            produto: produtoSelecionado,
            lote: loteSelecionado
        })
    })
    .then(response => response.json())
    .then(data => {
        // Atualizar a tabela com os dados
        const tbody = document.querySelector('#tblistadefinicao tbody');
        tbody.innerHTML = '';
        
        data.lista.forEach(item => {
            const row = `
                <tr>
                    <td>${item.numero}</td>
                    <td>${item.medico || ''}</td>
                    <td>${item.uf || ''}</td>
                    <td>${item.atividade || ''}</td>
                    <td>
                        <input type="text" class="form-control p110serie" value="${item.serie || ''}" 
                               data-chve="${item.chve}" style="width: 80px;">
                    </td>
                    <td>${item.producao || ''}</td>
                    <td>${item.calibracao || ''}</td>
                    <td>${item.observacao || ''}</td>
                </tr>
            `;
            tbody.innerHTML += row;
        });
    })
    .catch(error => {
        console.error('Erro na requisição AJAX:', error);
        alert('Erro ao pesquisar lista de série.');
    });
};

window.definirSerieTable = function(event) {
    event.preventDefault();

    const cdusuario = document.getElementById('cdusuario');
    const senha = document.getElementById('txtSenha');

    if (!cdusuario || !senha) {
        alert("Erro: Campos não encontrados.");
        return;
    }

    if (!senha.value.trim()) {
        alert("Por favor, informe a senha.");
        return;
    }

    // Coletar dados de todas as linhas da tabela
    const tabela = document.getElementById('tblistadefinicao');
    const linhas = tabela.querySelectorAll('tbody tr');
    const dados = [];
    let verifica = 0;

    linhas.forEach(linha => {
        const inputSerie = linha.querySelector('.p110serie');
        console.log('Item processado:', { 
            serie: inputSerie.value.trim(), 
            chve: inputSerie.dataset.chve 
          });
          
        if (inputSerie && inputSerie.value.trim() !== '') {
            verifica = 1;
            dados.push({
                chve: inputSerie.dataset.chve,
                serie: inputSerie.value.trim()
            });
        }
    });

    if (verifica === 0) {
        alert("Nenhum valor foi inserido para a gravação.");
        return;
    }

    fetch('/dfv-ds/definir-multiplas-series', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            dados: dados,
            senha: senha.value
        })
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.message.includes('sucesso') || data.message.includes('alterados')) {
            window.location.reload();
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert("Erro ao gravar as séries. Tente novamente.");
    });
};

window.validarCampos = function() {
    const cmbprod = document.getElementById('cmbprod');
    const cmbLote = document.getElementById('cmbLote');

    if (cmbprod.value == 0) {
        alert("INFORME O PRODUTO.");
        cmbprod.focus();
        return false;
    }
    if (cmbLote.value == 0) {
        alert("INFORME O LOTE.");
        cmbLote.focus();
        return false;
    }

    return true;
};

window.chamaDefinirIntervalo = function() {
    if (!validarCampos()) {
        return false;
    }

    const cmbprod = document.getElementById('cmbprod');
    const cmbLote = document.getElementById('cmbLote');

    const url = "/dfv-ds/intervalo?PRODUTO=" + cmbprod.value + "&LOTE=" + cmbLote.value;
    window.location.href = url;
};

window.chamaDefinirIntervaloLote = async function () {
    if (!validarCampos()) {
        return false;
    }

    const cmbprod = document.getElementById('cmbprod');
    const cmbLote = document.getElementById('cmbLote');

    const produto = encodeURIComponent(cmbprod.value);
    const lote = encodeURIComponent(cmbLote.value);

    try {
        const response = await fetch(`/definicao-serie/buscar-numero?produto=${produto}&lote=${lote}`);
        const data = response.json();

        let numero = parseInt(data.numero);
        if (isNaN(numero) || numero == null) {
            numero = 1;
        }

        // Redireciona com PRODUTO, LOTE e NUMERO
        const url = `/dfv-ds/intervalo-lote?PRODUTO=${produto}&LOTE=${lote}&NUMERO=${numero}`;
        window.location.href = url;

    } catch (error) {
        alert("Erro ao buscar número. Tente novamente.");
        console.error(error);
    }
};

window.buscarSerie = function() {
    const produtoSelect = document.getElementById('cmbprod');
    const loteSelect = document.getElementById('cmbLote');

    if (!produtoSelect || !loteSelect) {
        alert("Por favor, selecione o produto e o lote.");
        return;
    }

    const produto = produtoSelect.value;
    const lote = loteSelect.value;

    if (produto == 0 || lote == 0) {
        alert("Por favor, selecione o produto e o lote.");
        return;
    }

    fetch('/dfv-ds/buscar-serie', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            produto: produto,
            lote: lote
        })
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
    })
    .catch(error => {
        console.error('Erro:', error);
        alert("Erro ao buscar série.");
    });
};

// Inicialização quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', function() {
    const produtoSelect = document.getElementById('cmbprod');
    
    if (produtoSelect) {
        produtoSelect.addEventListener('change', atualizaLoteDefinicao);
    }
}); 