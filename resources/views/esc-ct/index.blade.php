@extends('adminlte::page')

@section('title', $title ?? 'Escala Semanal')

@section('plugins.Datatables', true)
@section('plugins.Toasts', true)

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <!-- início do conteúdo -->
                <form id="formEscalaSemanal" method="POST">
                    @csrf
                    <input type='hidden' name='nr_ID' id='nr_ID' value="" />
                    <input type='hidden' name='txtdisponiveis' id='txtdisponiveis' value="" />

                    <div class="row">
                        <div class="col-md-2">
                            <label for="txtLotes">Lote:</label>
                            <input type='text' class='form-control' id='txtLotes' name='txtLotes' onchange="atualizaCombos()" required />
                        </div>
                        <div class="col-md-3">
                            <label for="cmbprod">Produto:</label>
                            {!! $produtos !!}
                        </div>
                        <div class="col-md-3">
                            <label for="selTipProc">Tipo de Processo:</label>
                            <select name="selTipProc" id="selTipProc" class="form-control" onchange="atualizaCombos()">
                                {!! $tiposProcesso !!}
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="txPeriodoINI">De:</label>
                            <input type="date" name="txPeriodoINI" id="txPeriodoINI" class="form-control" onchange="fu_add5dias(this.value,5)" required />
                        </div>
                        <div class="col-md-2">
                            <label for="txPeriodoATE">Até:</label>
                            <input type="date" name="txPeriodoATE" id="txPeriodoATE" class="form-control" required />
                        </div>
                    </div>

                    <br>

                    <div class="row">
                        <div class="col-md-5">
                            <label for="selTarefas">Tarefas:</label>
                            <select name="selTarefas" id="selTarefas" class="form-control" onchange="atualizaCombos()">
                                {!! $tarefas !!}
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="txDataExecucao">Data de Execução:</label>
                            <input type="date" name="txDataExecucao" id="txDataExecucao" class="form-control" required />
                        </div>
                    </div>

                    <br>

                    <div class="row">
                        <div class="col-md-5">
                            <label for="dispon">Disponíveis:</label>
                            <select name="dispon" id="dispon" size="10" style="height: 100%;" multiple class="form-control">
                                {!! $usuarios !!}
                            </select>
                        </div>
                        <div class="col-md-0 d-flex align-items-center">
                            <button type="button" id="doSnd" name="doSnd" class="btn btn-primary" aria-label="Left Align">
                                <span class="fa fa-arrow-right fa-lg" aria-hidden="true"></span>
                            </button>
                            &nbsp;
                            <button type="button" id="doRst" name="doRst" class="btn btn-primary" aria-label="Left Align">
                                <span class="fa fa-arrow-left fa-lg" aria-hidden="true"></span>
                            </button>
                        </div>
                        <div class="col-md-5">
                            <label for="assoc">Associados:</label>
                            <select name="assoc" id="assoc" size="10" style="height: 100%;" multiple class="form-control">
                            </select>
                        </div>
                    </div>

                    <br>

                    <div class="row">
                        <div class="col-md-3">
                            <label for="txtSenha">Senha:</label>
                            <input type="password" name="txtSenha" id="txtSenha" class="form-control" maxlength="6" size="7" required />
                        </div>
                        <div class="col-md-4">
                            <br>
                            <button type="submit" class='btn btn-primary' id="InserirEscalaSenanal" name="InserirEscalaSenanal">
                                Gravar
                            </button>
                            <button type="button" class='btn btn-danger d-none' id="CancelaUpdate" name="CancelaUpdate" onclick="fu_cancelaUpdate()">
                                Cancela Atualização
                            </button>
                        </div>
                        <div class="col-md-5 text-right">
                            <br>
                            <button type="button" class='btn btn-danger' id="DuplicarEscala" name="DuplicarEscala" onclick="fu_duplicar()">
                                Duplicar Escala
                            </button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <br><hr>
                            <table id="tblista" class="display compact table-striped table-bordered" style="width:100%; font-size:12px; font-family: Tahoma">
                                <thead style="background-color:#556295; color:#f2f3f7">
                                    <tr>
                                        <th style="width:70px"></th>
                                        <th>Lote</th>
                                        <th>Produto</th>
                                        <th>Tipo Processo</th>
                                        <th>Data Inc.</th>
                                        <th>Data Fin.</th>
                                        <th>Data Exec.</th>
                                        <th>Tarefa</th>
                                        <th>Responsáveis</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {!! $escalas !!}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <br><br>
                </form>
                <!-- fim do conteúdo -->
            </div>
        </div>
    </div>
</div>

<!-- Modal para edição -->
<div class="modal fade" id="modalEdicao" tabindex="-1" role="dialog" aria-labelledby="modalEdicaoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEdicaoLabel">Editar Escala Semanal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Conteúdo do modal será preenchido via JavaScript -->
            </div>
        </div>
    </div>
</div>

<script>
// Script executado imediatamente após o DOM
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOMContentLoaded - Script iniciando');

    // Aguardar jQuery estar disponível
    var checkJQuery = setInterval(function() {
        if (typeof $ !== 'undefined') {
            clearInterval(checkJQuery);
            console.log('jQuery encontrado, inicializando aplicação');
            initApp();
        }
    }, 100);
});

function initApp() {
    console.log('Inicializando aplicação');

    // Verificar se jQuery está carregado
    if (typeof $ === 'undefined') {
        console.error('jQuery não está carregado!');
        return;
    } else {
        console.log('jQuery versão:', $.fn.jquery);
    }

    // Verificar se DataTables está disponível
    if (typeof $.fn.DataTable === 'undefined') {
        console.error('DataTables não está carregado!');
    } else {
        console.log('DataTables carregado com sucesso');
    }

    // Verificar se toastr está disponível
    if (typeof toastr === 'undefined') {
        console.error('Toastr não está carregado!');
    } else {
        console.log('Toastr carregado com sucesso');
    }

    const lote = sessionStorage.getItem('lote') || '';
    const produto = sessionStorage.getItem('produto') || '';
    const dtini = sessionStorage.getItem('dtini') || '';
    const dtate = sessionStorage.getItem('dtate') || '';

    // Definir todas as funções no escopo global (window)
    window.fu_add5dias = function(date, days) {
        console.log('fu_add5dias chamada com:', date, days);

        // Verificar se o elemento destino existe
        var elementoDestino = document.getElementById('txPeriodoATE');
        if (!elementoDestino) {
            console.error('Elemento txPeriodoATE não encontrado');
            return;
        }
        
        // Garantir que o campo mantenha seu tipo 'date'
        elementoDestino.type = 'date';
        
        // Também garantir que o campo de origem mantenha seu tipo 'date'
        var elementoOrigem = document.getElementById('txPeriodoINI');
        if (elementoOrigem) {
            elementoOrigem.type = 'date';
        }

        // Verificar se a data foi fornecida
        if (!date || date === '') {
            console.log('Data vazia, limpando campo txPeriodoATE');
            elementoDestino.value = '';
            return;
        }

        try {
            var result = new Date(date);

            // Verificar se a data é válida
            if (isNaN(result.getTime())) {
                console.error('Data inválida:', date);
                return;
            }

            result.setDate(result.getDate() + days);

            var ano = result.getFullYear();
            var mes = result.getMonth() + 1;
            var dia = result.getDate();

            if (dia < 10) dia = '0' + dia;
            if (mes < 10) mes = '0' + mes;

            var dataFormatada = ano + '-' + mes + '-' + dia;
            console.log('Data calculada:', dataFormatada);

            // Garantir que o campo mantenha seu tipo 'date'
            elementoDestino.type = 'date';
            elementoDestino.value = dataFormatada;
            
            // Garantir que o campo de origem também mantenha seu tipo 'date'
            if (elementoOrigem) {
                elementoOrigem.type = 'date';
            }

        } catch (error) {
            console.error('Erro na função fu_add5dias:', error);
        }
    };

    window.atualizaCombos = function() {
        console.log('=== INÍCIO atualizaCombos ===');

        var _l = document.getElementById('txtLotes').value;
        var _t = document.getElementById('selTarefas').value;

        console.log('Lote:', _l, 'Tarefa:', _t);

        // Verificar se temos os dados necessários
        if (!_l || !_t) {
            console.log('Lote ou Tarefa vazio, limpando listas');
            $('#assoc').empty();
            $('#dispon').empty();
            return;
        }

        // Verificar se jQuery está disponível
        if (typeof $ === 'undefined') {
            console.error('jQuery não está disponível para atualizaCombos');
            return;
        }

        console.log('Fazendo requisição AJAX para buscar usuários...');
        console.log('URL da requisição:', '{{ route("esc-ct.usuarios-associados") }}');

        $.ajax({
            url: '{{ route("esc-ct.usuarios-associados") }}',
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                lote: _l,
                tarefa: _t
            },
            success: function(result) {
                console.log('=== RESPOSTA AJAX SUCESSO ===');
                console.log('Resposta completa:', result);
                console.log('result.success:', result.success);
                console.log('result.usuarios:', result.usuarios);
                console.log('Tipo de result.usuarios:', typeof result.usuarios);

                if (result.success) {
                    if (result.usuarios) {
                        console.log('Chamando getreturnassoc com:', result.usuarios);
                        getreturnassoc(result.usuarios);
                    } else {
                        console.log('result.usuarios está vazio ou null');
                        $('#assoc').empty();
                        $('#dispon').empty();
                    }
                } else {
                    console.error('Erro na resposta do servidor:', result);
                    // Limpar listas em caso de erro
                    $('#assoc').empty();
                    $('#dispon').empty();
                }
            },
            error: function(xhr, status, error) {
                console.log('=== RESPOSTA AJAX ERRO ===');
                console.error('Status HTTP:', xhr.status);
                console.error('Status Text:', xhr.statusText);
                console.error('Erro:', error);
                console.error('Status:', status);
                console.error('Resposta completa:', xhr.responseText);

                // Tentar fazer parse da resposta para ver se é JSON
                try {
                    var jsonResponse = JSON.parse(xhr.responseText);
                    console.error('Resposta JSON parseada:', jsonResponse);
                } catch (e) {
                    console.error('Resposta não é JSON válido');
                }

                // Limpar listas em caso de erro
                $('#assoc').empty();
                $('#dispon').empty();
            }
        });

        console.log('=== FIM atualizaCombos (requisição enviada) ===');
    };

    window.getreturnassoc = function(par) {
        console.log('=== INÍCIO getreturnassoc ===');
        console.log('Dados recebidos (tipo):', typeof par);
        console.log('Dados recebidos (valor):', par);
        console.log('Dados recebidos (length):', par ? par.length : 'null/undefined');

        // Verificar se jQuery está disponível
        if (typeof $ === 'undefined') {
            console.error('jQuery não está disponível para getreturnassoc');
            return;
        }

        // Verificar se os dados estão vazios
        if (!par || par.trim() === '') {
            console.log('Dados vazios recebidos, limpando combos');
            $('#assoc').empty();
            $('#dispon').empty();
            return;
        }

        var dados = par.split(',');
        console.log('Dados após split:', dados);
        console.log('Total de elementos após split:', dados.length);

        // Limpar os combos
        $('#assoc').empty();
        $('#dispon').empty();

        var countAssoc = 0;
        var countDispon = 0;

        // Processar os dados - formato: tipo,cdUsuario,Nome,tipo,cdUsuario,Nome...
        for (let i = 0; i < dados.length; i += 3) {
            if (i + 2 < dados.length) {
                var tipo = dados[i] ? dados[i].trim() : '';
                var cdUsuario = dados[i + 1] ? dados[i + 1].trim() : '';
                var nome = dados[i + 2] ? dados[i + 2].trim() : '';

                console.log(`Processando item ${Math.floor(i/3) + 1}:`, {tipo, cdUsuario, nome});

                // Verificar se todos os campos estão preenchidos
                if (tipo && cdUsuario && nome) {
                    // Verificar o tipo e adicionar ao combo correspondente
                    if (tipo === 'assoc') {
                        // Usuários associados vão para o combo "Associados"
                        $('#assoc').append(new Option(nome, cdUsuario));
                        countAssoc++;
                        console.log('Adicionado ao combo ASSOCIADOS:', nome);
                    } else if (tipo === 'dispon') {
                        // Usuários disponíveis vão para o combo "Disponíveis"
                        $('#dispon').append(new Option(nome, cdUsuario));
                        countDispon++;
                        console.log('Adicionado ao combo DISPONÍVEIS:', nome);
                    } else {
                        console.warn('Tipo desconhecido:', tipo);
                    }
                } else {
                    console.warn('Dados incompletos ignorados:', {tipo, cdUsuario, nome});
                }
            }
        }

        console.log('=== RESULTADO ===');
        console.log('Total Associados:', countAssoc);
        console.log('Total Disponíveis:', countDispon);
        console.log('Opções no combo Assoc:', $('#assoc option').length);
        console.log('Opções no combo Dispon:', $('#dispon option').length);

        // Ordenar as listas após preenchimento
        if (countAssoc > 0 || countDispon > 0) {
            sortOptions1(); // Ordenar lista de associados
            sortOptions2(); // Ordenar lista de disponíveis
        }

        console.log('=== FIM getreturnassoc ===');
    };

    window.fuEditaEscala = function(id, lote, tarefa, produto, datInicial, datFinal, datExec, tipoProcesso, idTipoProcesso) {
        console.log('=== INICIO fuEditaEscala ===');
        console.log('Parâmetros recebidos:', {
            id, lote, tarefa, produto,
            datInicial, datFinal, datExec,
            tipoProcesso, idTipoProcesso
        });

        try {
            // Função para converter data do SQL Server para HTML5 input
            function converteDataSQLParaHTML5(dataSql) {
                console.log('converteDataSQLParaHTML5 chamada com:', dataSql);
                if (!dataSql || dataSql === 'null' || dataSql === '') {
                    console.log('Data vazia ou nula, retornando string vazia');
                    return '';
                }

                try {
                    console.log('Convertendo data SQL:', dataSql);

                    // Para campos de data, sempre retornar apenas a parte da data (YYYY-MM-DD)
                    // independente se vem como datetime completo
                    if (dataSql.includes(' ')) {
                        const partes = dataSql.split(' ');
                        const data = partes[0]; // YYYY-MM-DD
                        console.log('Data convertida (extraindo apenas data):', data);
                        return data;
                    } else {
                        // Para apenas data (YYYY-MM-DD) - usar como está
                        console.log('Data convertida (apenas data):', dataSql);
                        return dataSql;
                    }
                } catch (error) {
                    console.error('Erro ao converter data SQL:', error, 'Data original:', dataSql);
                    return '';
                }
            }

            // Converter as datas
            const dataInicioHTML5 = converteDataSQLParaHTML5(datInicial);
            const dataFimHTML5 = converteDataSQLParaHTML5(datFinal);
            const dataExecHTML5 = converteDataSQLParaHTML5(datExec);
            
            console.log('=== DEBUG DATAS ===');
            console.log('datInicial (raw):', datInicial);
            console.log('datFinal (raw):', datFinal);
            console.log('datExec (raw):', datExec);
            console.log('dataInicioHTML5:', dataInicioHTML5);
            console.log('dataFimHTML5:', dataFimHTML5);
            console.log('dataExecHTML5:', dataExecHTML5);
            console.log('=== FIM DEBUG DATAS ===');

            // Preencher os campos do formulário
            var campoId = document.getElementById('nr_ID');
            var campoLote = document.getElementById('txtLotes');
            var campoProduto = document.getElementById('cmbprod');
            var campoDataInicio = document.getElementById('txPeriodoINI');
            var campoDataFim = document.getElementById('txPeriodoATE');
            var campoDataExec = document.getElementById('txDataExecucao');
            var campoTarefa = document.getElementById('selTarefas');
            var campoTipoProc = document.getElementById('selTipProc');

            // Verificar se todos os campos existem
            if (!campoId || !campoLote || !campoProduto || !campoDataInicio || !campoDataFim || !campoDataExec || !campoTarefa || !campoTipoProc) {
                console.error('Um ou mais campos não foram encontrados');
                toastr.error('Erro ao carregar dados para edição');
                return;
            }

            // Preencher campos com valores
            campoId.value = id;
            campoLote.value = lote;
            campoProduto.value = produto;
            campoTarefa.value = tarefa;
            campoTipoProc.value = idTipoProcesso;

            // Garantir que todos os campos de data sejam do tipo 'date'
            campoDataInicio.type = 'date';
            campoDataFim.type = 'date';
            campoDataExec.type = 'date';
            
            // Atribuir valores aos campos de data
            campoDataInicio.value = dataInicioHTML5;
            campoDataFim.value = dataFimHTML5;
            campoDataExec.value = dataExecHTML5;

            console.log('=== DEBUG CAMPOS PREENCHIDOS ===');
            console.log('ID:', campoId.value);
            console.log('Lote:', campoLote.value);
            console.log('Produto:', campoProduto.value);
            console.log('Tarefa:', campoTarefa.value);
            console.log('Tipo Processo:', campoTipoProc.value);
            console.log('Data Início:', campoDataInicio.value);
            console.log('Data Fim:', campoDataFim.value);
            console.log('Data Execução:', campoDataExec.value);
            console.log('=== FIM DEBUG CAMPOS ===');

            // Tornar os campos somente leitura durante edição
            campoLote.readOnly = true;
            campoProduto.disabled = true;
            campoTarefa.disabled = true;
            campoDataInicio.readOnly = true;
            campoDataFim.readOnly = true;

            // Criar campos hidden para enviar os valores dos campos desabilitados
            // Isso é necessário porque campos disabled não são enviados no FormData
            var campoProdutoHidden = document.getElementById('cmbprod_hidden');
            if (!campoProdutoHidden) {
                campoProdutoHidden = document.createElement('input');
                campoProdutoHidden.type = 'hidden';
                campoProdutoHidden.name = 'cmbprod';
                campoProdutoHidden.id = 'cmbprod_hidden';
                document.getElementById('formEscalaSemanal').appendChild(campoProdutoHidden);
            }
            campoProdutoHidden.value = produto;

            var campoTarefaHidden = document.getElementById('selTarefas_hidden');
            if (!campoTarefaHidden) {
                campoTarefaHidden = document.createElement('input');
                campoTarefaHidden.type = 'hidden';
                campoTarefaHidden.name = 'selTarefas';
                campoTarefaHidden.id = 'selTarefas_hidden';
                document.getElementById('formEscalaSemanal').appendChild(campoTarefaHidden);
            }
            campoTarefaHidden.value = tarefa;

            var campoTipoProcHidden = document.getElementById('selTipProc_hidden');
            if (!campoTipoProcHidden) {
                campoTipoProcHidden = document.createElement('input');
                campoTipoProcHidden.type = 'hidden';
                campoTipoProcHidden.name = 'selTipProc';
                campoTipoProcHidden.id = 'selTipProc_hidden';
                document.getElementById('formEscalaSemanal').appendChild(campoTipoProcHidden);
            }
            campoTipoProcHidden.value = idTipoProcesso;

            var campoDataInicioHidden = document.getElementById('txPeriodoINI_hidden');
            if (!campoDataInicioHidden) {
                campoDataInicioHidden = document.createElement('input');
                campoDataInicioHidden.type = 'hidden';
                campoDataInicioHidden.name = 'txPeriodoINI';
                campoDataInicioHidden.id = 'txPeriodoINI_hidden';
                document.getElementById('formEscalaSemanal').appendChild(campoDataInicioHidden);
            }
            campoDataInicioHidden.value = dataInicioHTML5;

            var campoDataFimHidden = document.getElementById('txPeriodoATE_hidden');
            if (!campoDataFimHidden) {
                campoDataFimHidden = document.createElement('input');
                campoDataFimHidden.type = 'hidden';
                campoDataFimHidden.name = 'txPeriodoATE';
                campoDataFimHidden.id = 'txPeriodoATE_hidden';
                document.getElementById('formEscalaSemanal').appendChild(campoDataFimHidden);
            }
            campoDataFimHidden.value = dataFimHTML5;

            // Atualizar combos para carregar usuários associados
            console.log('Chamando atualizaCombos para carregar usuários...');
            atualizaCombos();

            // Mostrar botão de cancelar atualização
            var element = document.getElementById("CancelaUpdate");
            if (element) {
                element.classList.remove("d-none");
            }

            console.log('=== FIM fuEditaEscala ===');
        } catch (error) {
            console.error('Erro na função fuEditaEscala:', error);
            toastr.error('Erro ao carregar dados para edição');
        }
    };

    window.fu_cancelaUpdate = function() {
        var element = document.getElementById("CancelaUpdate");
        element.classList.add("d-none");
        document.getElementById("nr_ID").value = "";
        document.getElementById("txtLotes").removeAttribute('readonly');
        document.getElementById("txPeriodoINI").removeAttribute('readonly');
        document.getElementById("txPeriodoATE").removeAttribute('readonly');
        document.getElementById('cmbprod').disabled = false;
        document.getElementById('selTarefas').disabled = false;
        
        // Remover campos hidden criados durante a edição
        var camposHidden = [
            'cmbprod_hidden',
            'selTarefas_hidden',
            'selTipProc_hidden',
            'txPeriodoINI_hidden',
            'txPeriodoATE_hidden'
        ];
        
        camposHidden.forEach(function(id) {
            var campo = document.getElementById(id);
            if (campo) {
                campo.remove();
            }
        });
        
        // Garantir que todos os campos de data mantenham seu tipo 'date'
        const campoDataExec = document.getElementById('txDataExecucao');
        const campoDataInicio = document.getElementById('txPeriodoINI');
        const campoDataFim = document.getElementById('txPeriodoATE');
        
        if (campoDataExec) {
            campoDataExec.type = 'date';
        }
        if (campoDataInicio) {
            campoDataInicio.type = 'date';
        }
        if (campoDataFim) {
            campoDataFim.type = 'date';
        }
    };

    window.fuDeleta = function(id) {
        if (confirm('Confirma a exclusão desta escala?')) {
            document.getElementById("nr_ID").value = id;

            $.ajax({
                url: '{{ route("esc-ct.destroy") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    nr_ID: id,
                    txtSenha: document.getElementById('txtSenha').value
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function() {
                    toastr.error('Erro ao excluir escala semanal');
                }
            });
        }
    };

    window.fu_popDisp = function() {
        console.log('fu_popDisp: Populando campo txtdisponiveis com usuários associados');

        try {
            // Verificar se o campo existe
            var campoDisponiveis = document.getElementById('txtdisponiveis');
            if (!campoDisponiveis) {
                console.error('Campo txtdisponiveis não encontrado');
                return false;
            }

            // Verificar se o combo de associados existe
            var comboAssoc = document.getElementById('assoc');
            if (!comboAssoc) {
                console.error('Combo de associados não encontrado');
                return false;
            }

            // Limpar o campo antes de popular
            campoDisponiveis.value = "";
            
            // Verificar se há opções selecionadas
            var opcoesSelecionadas = comboAssoc.options;
            console.log('Número de opções no combo associados:', opcoesSelecionadas.length);
            
            if (opcoesSelecionadas.length === 0) {
                console.warn('Nenhum usuário associado encontrado - isso pode causar erro de validação');
                // Em caso de atualização, não retornar false para permitir que o processo continue
                // mas logar o problema
                return true;
            }

            // Popular o campo com os valores das opções
            var valores = [];
            for (var i = 0; i < opcoesSelecionadas.length; i++) {
                var valor = opcoesSelecionadas[i].value;
                var texto = opcoesSelecionadas[i].text;
                console.log(`Opção ${i + 1}: valor="${valor}", texto="${texto}"`);
                
                if (valor && valor.trim() !== '') {
                    valores.push(valor.trim());
                } else {
                    console.warn(`Opção ${i + 1} tem valor vazio ou nulo`);
                }
            }

            // Juntar os valores com vírgula
            campoDisponiveis.value = valores.join(',');

            console.log('fu_popDisp: Campo txtdisponiveis populado com:', campoDisponiveis.value);
            console.log('Total de usuários associados:', valores.length);
            
            // Verificar se o campo foi populado corretamente
            if (campoDisponiveis.value.trim() === '') {
                console.error('Campo txtdisponiveis ficou vazio após população');
                return false;
            }
            
            return true;
        } catch (error) {
            console.error('Erro na função fu_popDisp:', error);
            return false;
        }
    };

    window.fu_duplicar = function() {
        let text = "Confirma a duplicação do Último lote?";
        if (confirm(text) == true) {
            $.ajax({
                url: '{{ route("esc-ct.duplicar") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    txtSenha: document.getElementById('txtSenha').value
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function() {
                    toastr.error('Erro ao duplicar escala semanal');
                }
            });
        }
    };

        // Funções de ordenação
    window.sortOptions2 = function() {
        console.log('sortOptions2 chamada - ordenando lista "Disponíveis"');

        if (typeof $ === 'undefined') {
            console.error('jQuery não está disponível para sortOptions2');
            return;
        }

        var allOptions = $("#dispon option");
        allOptions.sort(function(op1, op2) {
            var text1 = $(op1).text().toLowerCase();
            var text2 = $(op2).text().toLowerCase();
            return (text1 < text2) ? -1 : 1;
        });
        allOptions.appendTo("#dispon");
    };

    window.sortOptions1 = function() {
        console.log('sortOptions1 chamada - ordenando lista "Associados"');

        if (typeof $ === 'undefined') {
            console.error('jQuery não está disponível para sortOptions1');
            return;
        }

        var allOptions = $("#assoc option");
        allOptions.sort(function(op1, op2) {
            var text1 = $(op1).text().toLowerCase();
            var text2 = $(op2).text().toLowerCase();
            return (text1 < text2) ? -1 : 1;
        });
        allOptions.appendTo("#assoc");
    };

    // Aguardar que o DataTables esteja disponível antes de inicializar
    var checkDataTables = setInterval(function() {
        if (typeof $.fn.DataTable !== 'undefined') {
            clearInterval(checkDataTables);
            initDataTables();
        }
    }, 100);

    function initDataTables() {
        console.log('Inicializando DataTables');

        try {
            var table = $('#tblista').DataTable({
                "language": {
                    "url": "{{ asset('js/datatables-pt-br.json') }}"
                }
            });

            console.log('DataTables inicializado com sucesso');
        } catch (error) {
            console.error('Erro ao inicializar DataTables:', error);
        }
    }

    // Inicializar event listeners
    initEventListeners();

    function initEventListeners() {
        console.log('Inicializando event listeners');

        // Botões de transferência entre listas
        $('#doSnd').click(function() {
            $('#dispon option:selected').each(function() {
                $("<option/>").
                val($(this).val()).
                text($(this).text()).
                appendTo("#assoc");
                $(this).remove();
                sortOptions1();
            });
        });

        $('#doRst').click(function() {
            $('#assoc option:selected').each(function() {
                $("<option/>").
                val($(this).val()).
                text($(this).text()).
                appendTo("#dispon");
                $(this).remove();
                sortOptions2();
            });
        });

        // Submissão do formulário
        $('#formEscalaSemanal').on('submit', function(e) {
            e.preventDefault();

            console.log('=== INICIO SUBMISSAO FORMULARIO ===');

            try {
                // Log de todos os campos para debug
                console.log('=== DEBUG CAMPOS FORMULARIO ===');
                console.log('nr_ID:', document.getElementById('nr_ID').value);
                console.log('txtLotes:', document.getElementById('txtLotes').value);
                console.log('cmbprod:', document.getElementById('cmbprod').value);
                console.log('selTipProc:', document.getElementById('selTipProc').value);
                console.log('txPeriodoINI:', document.getElementById('txPeriodoINI').value);
                console.log('txPeriodoATE:', document.getElementById('txPeriodoATE').value);
                console.log('selTarefas:', document.getElementById('selTarefas').value);
                console.log('txDataExecucao:', document.getElementById('txDataExecucao').value);
                console.log('txtSenha:', document.getElementById('txtSenha').value);
                console.log('=== FIM DEBUG CAMPOS ===');

                // Validações básicas antes de enviar
                var lote = document.getElementById('txtLotes').value;
                var produto = document.getElementById('cmbprod').value;
                var tipoProcesso = document.getElementById('selTipProc').value;
                var dataInicio = document.getElementById('txPeriodoINI').value;
                var dataFim = document.getElementById('txPeriodoATE').value;
                var tarefa = document.getElementById('selTarefas').value;
                var senha = document.getElementById('txtSenha').value;

                // Verificar campos obrigatórios
                if (!lote || !produto || !tipoProcesso || !dataInicio || !dataFim || !tarefa || !senha) {
                    var camposVazios = [];
                    if (!lote) camposVazios.push('Lote');
                    if (!produto) camposVazios.push('Produto');
                    if (!tipoProcesso) camposVazios.push('Tipo de Processo');
                    if (!dataInicio) camposVazios.push('Data Início');
                    if (!dataFim) camposVazios.push('Data Fim');
                    if (!tarefa) camposVazios.push('Tarefa');
                    if (!senha) camposVazios.push('Senha');
                    
                    toastr.error('Campos obrigatórios não preenchidos: ' + camposVazios.join(', '));
                    return;
                }

                // Verificar se há usuários associados
                var comboAssoc = document.getElementById('assoc');
                if (!comboAssoc || comboAssoc.options.length === 0) {
                    toastr.error('Por favor, selecione pelo menos um usuário responsável');
                    return;
                }

                // Popula o campo txtdisponiveis antes de enviar
                var resultadoPopulacao = fu_popDisp();
                if (!resultadoPopulacao) {
                    toastr.error('Erro ao preparar dados dos usuários responsáveis');
                    return;
                }

                // Verificar se o campo foi populado corretamente
                var disponiveis = document.getElementById('txtdisponiveis').value;
                if (!disponiveis || disponiveis.trim() === '') {
                    toastr.error('Campo de usuários responsáveis está vazio');
                    return;
                }

                console.log('Campo txtdisponiveis populado com sucesso:', disponiveis);

                var formData = new FormData(this);
                var url = '{{ route("esc-ct.store") }}';

                if (document.getElementById('nr_ID').value) {
                    url = '{{ route("esc-ct.update") }}';
                    console.log('Modo UPDATE - ID:', document.getElementById('nr_ID').value);
                } else {
                    console.log('Modo INSERT');
                }

                console.log('URL:', url);
                console.log('Dados do formulário:', Object.fromEntries(formData));

                // Mostrar indicador de carregamento
                var btnSubmit = document.getElementById('InserirEscalaSenanal');
                var textoOriginal = btnSubmit.innerHTML;
                btnSubmit.innerHTML = 'Processando...';
                btnSubmit.disabled = true;

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        console.log('=== RESPOSTA AJAX SUCESSO ===', response);
                        
                        // Restaurar botão
                        btnSubmit.innerHTML = textoOriginal;
                        btnSubmit.disabled = false;
                        
                        if (response.success) {
                            toastr.success(response.message);

                            // Recarregar apenas o grid em vez de toda a página
                            recarregarGrid();

                            // Limpar formulário se foi inserção
                            if (!document.getElementById('nr_ID').value) {
                                document.getElementById('formEscalaSemanal').reset();
                                document.getElementById('assoc').innerHTML = '';
                                document.getElementById('txtdisponiveis').value = '';
                            }
                        } else {
                            toastr.error(response.message || 'Erro desconhecido');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('=== RESPOSTA AJAX ERRO ===');
                        console.error('Status:', status);
                        console.error('Error:', error);
                        console.error('Response:', xhr.responseText);
                        
                        // Restaurar botão
                        btnSubmit.innerHTML = textoOriginal;
                        btnSubmit.disabled = false;
                        
                        // Tentar fazer parse da resposta para mostrar erro mais específico
                        try {
                            var jsonResponse = JSON.parse(xhr.responseText);
                            if (jsonResponse.message) {
                                toastr.error(jsonResponse.message);
                            } else {
                                toastr.error('Erro ao processar solicitação');
                            }
                        } catch (e) {
                            toastr.error('Erro ao processar solicitação');
                        }
                    }
                });
            } catch (error) {
                console.error('Erro na submissão do formulário:', error);
                toastr.error('Erro interno na validação do formulário');
                
                // Restaurar botão em caso de erro
                var btnSubmit = document.getElementById('InserirEscalaSenanal');
                if (btnSubmit) {
                    btnSubmit.innerHTML = 'Gravar';
                    btnSubmit.disabled = false;
                }
            }
        });

        // Função para recarregar apenas o grid
        function recarregarGrid() {
            console.log('Recarregando grid...');
            $.ajax({
                url: '{{ route("esc-ct.data") }}',
                type: 'GET',
                success: function(response) {
                    if (response.data) {
                        // Atualizar apenas o tbody da tabela
                        $('#tblista tbody').html(response.data);
                        console.log('Grid recarregado com sucesso');
                    }
                },
                error: function() {
                    console.error('Erro ao recarregar grid');
                    toastr.error('Erro ao atualizar lista');
                }
            });
        }

        // Adicionar event listeners como backup para os campos com onchange
        $('#txtLotes').on('change', function() {
            console.log('Event listener jQuery disparado para txtLotes');
            atualizaCombos();
        });

        $('#selTipProc').on('change', function() {
            console.log('Event listener jQuery disparado para selTipProc');
            atualizaCombos();
        });

        $('#selTarefas').on('change', function() {
            console.log('Event listener jQuery disparado para selTarefas');
            atualizaCombos();
        });

        // Adicionar event listener para o campo txPeriodoINI como backup
        $('#txPeriodoINI').on('change', function() {
            console.log('Event listener jQuery disparado para txPeriodoINI');
            fu_add5dias(this.value, 5);
        });

        console.log('Event listeners inicializados');
    }



    // Carregar valores salvos no sessionStorage
    if (document.getElementById('txtLotes')) {
        document.getElementById('txtLotes').value = lote;
    }
    if (document.getElementById('cmbprod')) {
        document.getElementById('cmbprod').value = produto;
    }
    if (document.getElementById('txPeriodoINI')) {
        document.getElementById('txPeriodoINI').value = dtini;
    }
    if (document.getElementById('txPeriodoATE')) {
        document.getElementById('txPeriodoATE').value = dtate;
    }

    // Carregar dados iniciais dos usuários se houver valores nos campos
    if (lote && produto && document.getElementById('selTarefas') && document.getElementById('selTarefas').value) {
        atualizaCombos();
    }

    console.log('Aplicação inicializada com sucesso');
}
</script>

@endsection

@push('styles')
<style>
thead input {
    width: 100%;
}
table.dataTable tbody td {
    vertical-align: top;
}
</style>
@endpush
