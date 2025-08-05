// Funções globais para serem acessíveis pelos botões
window.obterParametro = function(nomeParametro) {
    const urlParams = new URLSearchParams(window.location.search);
    const valor = urlParams.get(nomeParametro);
    return valor;
};

window.definirSerieAtividadeLoteTable = function(event) {
    event.preventDefault();

    if (!validarCampos()) {
        return;
    }

    let forca;
    const produto = document.getElementById('txtProduto').value;
    const lote = document.getElementById('txtLote').value;
    const serie = document.getElementById('cmbSerie').value;
    const tipo = 2;
    const inicio = document.getElementById('cmbLoteIni').value;
    const fim = document.getElementById('cmbLoteFim').value;
    const rdFiltro = document.getElementsByName('rdFiltro');
    const cdusuario = document.getElementById('cdusuario').value;
    const senha = document.getElementById('txtSenha').value;

    for (let i = 0; i < rdFiltro.length; i++) {
        if (rdFiltro[i].checked) {
            forca = rdFiltro[i].value;
            break;
        }
    }

    fetch('/dfv-ds/definir-serie-intervalo-lote', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            produto: produto,
            lote: lote,
            serie: serie,
            inicio: inicio,
            fim: fim,
            forca: forca,
            senha: senha
        })
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.message.includes('sucesso')) {
            window.history.back();
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert("Erro ao definir a série por lote. Tente novamente.");
    });
};

window.paraTodos = function() {
    const rdFiltro = document.getElementsByName('rdFiltro');
    rdFiltro[0].checked = true;
    rdFiltro[1].checked = false;
};

window.semSerie = function() {
    const rdFiltro = document.getElementsByName('rdFiltro');
    rdFiltro[0].checked = false;
    rdFiltro[1].checked = true;
};

window.validarCampos = function() {
    // Validar Produto
    const produto = document.getElementById('txtProduto').value;
    if (produto.trim() === "") {
        alert("INFORME O PRODUTO!");
        return false;
    }

    // Validar Lote
    const lote = document.getElementById('txtLote').value;
    if (lote.trim() === "") {
        alert("INFORME O LOTE!");
        return false;
    }

    // Validar Série
    const serie = document.getElementById('cmbSerie').value;
    if (serie == 0) {
        alert("INFORME A SÉRIE!");
        return false;
    }

    // Validar Lote/Número Inicial
    const inicio = document.getElementById('cmbLoteIni').value;
    if (inicio.trim() === "") {
        alert("INFORME O LOTE/NÚMERO INICIAL!");
        return false;
    }

    // Validar Lote/Número Final
    const fim = document.getElementById('cmbLoteFim').value;
    if (fim.trim() === "") {
        alert("INFORME O LOTE/NÚMERO FINAL!");
        return false;
    }

    // Validar se Lote/Número Final é maior que Inicial
    if (parseFloat(fim) <= parseFloat(inicio)) {
        alert("O LOTE/NÚMERO FINAL DEVE SER MAIOR QUE O INICIAL!");
        return false;
    }

    // Validar Senha
    const senha = document.getElementById('txtSenha').value;
    if (senha.trim() === "") {
        alert("INFORME A SENHA!");
        return false;
    }

    // Validar Técnico
    const tecnico = document.getElementById('cmbTecnico').value;
    if (tecnico.trim() === "") {
        alert("INFORME O TÉCNICO OPERADOR!");
        return false;
    }

    return true;
};

// Inicialização quando o DOM estiver carregado
document.addEventListener("DOMContentLoaded", function() {
    let txtProduto = document.getElementById('txtProduto');
    let txtLote = document.getElementById('txtLote');

    if (txtProduto) {
        txtProduto.value = obterParametro('PRODUTO');
        console.log("Produto atribuído:", txtProduto.value);
    } else {
        console.warn("Elemento txtProduto não encontrado no DOM.");
    }

    if (txtLote) {
        txtLote.value = obterParametro('LOTE');
        console.log("Lote atribuído:", txtLote.value);
    } else {
        console.warn("Elemento txtLote não encontrado no DOM.");
    }
}); 