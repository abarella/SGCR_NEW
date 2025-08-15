@extends('adminlte::page')

@section('title', $title ?? 'Escala Semanal')

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
                            <input type="datetime-local" name="txDataExecucao" id="txDataExecucao" class="form-control" required />
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
                            <button type="submit" class='btn btn-primary' id="InserirEscalaSenanal" name="InserirEscalaSenanal" onclick="fu_popDisp()">
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
                                        <th style="text-align:center;width:25px;">Item</th>
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

@push('scripts')
<script>
const lote = sessionStorage.getItem('lote') || '';
const produto = sessionStorage.getItem('produto') || '';
const dtini = sessionStorage.getItem('dtini') || '';
const dtate = sessionStorage.getItem('dtate') || '';

function fu_add5dias(date, days) {
    var result = new Date(date);
    result.setDate(result.getDate() + days);
    ano = result.getFullYear();
    mes = result.getMonth() + 1;
    dia = result.getDate();
    if (dia < 10) dia = '0' + dia;
    if (mes < 10) mes = '0' + mes;
    document.getElementById('txPeriodoATE').value = ano + '-' + mes + '-' + dia;
}

function fuEditaEscala(p1, p2, p3, p4, p5, p6, p7, p8, p9) {
    p2 = p2.split("¬").join(" ");
    p3 = p3.split("¬").join(" ");
    p4 = p4.split("¬").join(" ");
    p5 = p5.substr(6, 10) + "-" + p5.substr(3, 2) + "-" + p5.substr(0, 2);
    p6 = p6.substr(6, 10) + "-" + p6.substr(3, 2) + "-" + p6.substr(0, 2);
    p8 = p8.split("¬").join(" ");
    p8 = p8.substr(6, 4) + "-" + p8.substr(3, 2) + "-" + p8.substr(0, 2) + p8.substr(10, 6);

    document.getElementById('nr_ID').value = p1;
    document.getElementById('txtLotes').value = p2;
    document.getElementById('selTarefas').value = p3;
    document.getElementById('cmbprod').value = p4;
    document.getElementById('txPeriodoINI').value = p5;
    document.getElementById('txPeriodoATE').value = p6;
    document.getElementById('txDataExecucao').value = p8;
    document.getElementById('selTipProc').value = p9;

    document.getElementById('txtLotes').readOnly = true;
    document.getElementById('cmbprod').disabled = true;
    document.getElementById('selTarefas').disabled = true;
    document.getElementById('txPeriodoINI').readOnly = true;
    document.getElementById('txPeriodoATE').readOnly = true;

    atualizaCombos();

    var element = document.getElementById("CancelaUpdate");
    element.classList.remove("d-none");
}

function fu_cancelaUpdate() {
    var element = document.getElementById("CancelaUpdate");
    element.classList.add("d-none");
    document.getElementById("nr_ID").value = "";
    document.getElementById("txtLotes").removeAttribute('readonly');
    document.getElementById("txPeriodoINI").removeAttribute('readonly');
    document.getElementById("txPeriodoATE").removeAttribute('readonly');
    document.getElementById('cmbprod').disabled = false;
    document.getElementById('selTarefas').disabled = false;
}

function fuDeleta(id) {
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
}

function fu_popDisp() {
    document.getElementById('txtLotes').readOnly = false;
    document.getElementById('cmbprod').disabled = false;
    document.getElementById('selTarefas').disabled = false;
    document.getElementById('txPeriodoINI').readOnly = false;
    document.getElementById('txPeriodoATE').readOnly = false;

    document.getElementById('txtdisponiveis').value = "";
    Array.from(document.querySelector("#assoc").options).forEach(function(option_element) {
        let option_value = option_element.value;
        document.getElementById('txtdisponiveis').value += option_value + ",";
    });
    document.getElementById('txtdisponiveis').value = document.getElementById('txtdisponiveis').value.slice(0, -1);
}

function fu_duplicar() {
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
}

function sortOptions2() {
    var allOptions = $("#dispon option");
    allOptions.sort(function(op1, op2) {
        var text1 = $(op1).text().toLowerCase();
        var text2 = $(op2).text().toLowerCase();
        return (text1 < text2) ? -1 : 1;
    });
    allOptions.appendTo("#dispon");
}

function sortOptions1() {
    var allOptions = $("#assoc option");
    allOptions.sort(function(op1, op2) {
        var text1 = $(op1).text().toLowerCase();
        var text2 = $(op2).text().toLowerCase();
        return (text1 < text2) ? -1 : 1;
    });
    allOptions.appendTo("#assoc");
}

function getreturnassoc(par) {
    var dados = par.split(',');

    $('#assoc').empty();
    $('#dispon').empty();

    for (let i = 0; i < dados.length; i++) {
        optionText = dados[i + 2];
        optionValue = dados[i + 1];
        if (dados[i] == 'assoc') {
            $('#assoc').append(new Option(optionText, optionValue));
        } else {
            $('#dispon').append(new Option(optionText, optionValue));
        }
        i = i + 2;
    }
}

function atualizaCombos() {
    _l = document.getElementById('txtLotes').value;
    _t = document.getElementById('selTarefas').value;
    
    $.ajax({
        url: '{{ route("esc-ct.usuarios-associados") }}',
        type: "POST",
        data: {
            _token: '{{ csrf_token() }}',
            lote: _l,
            tarefa: _t
        },
        success: function(result) {
            if (result.success) {
                getreturnassoc(result.usuarios);
            }
        }
    });
}

$(document).ready(function() {
    // Setup - add a text input to each footer cell
    $('#tblista thead tr')
        .clone(true)
        .addClass('filters')
        .appendTo('#tblista thead');

    var table = $('#tblista').DataTable({
        orderCellsTop: true,
        fixedHeader: true,
        "language": {
            "url": "/js/datatables-pt-br.json"
        },
        initComplete: function() {
            var api = this.api();

            // For each column
            api
                .columns()
                .eq(0)
                .each(function(colIdx) {
                    // Set the header cell to contain the input element
                    var cell = $('.filters th').eq(
                        $(api.column(colIdx).header()).index()
                    );
                    var title = $(cell).text();
                    $(cell).html('<input type="text" placeholder="' + title + '" />');

                    // On every keypress in this input
                    $(
                        'input',
                        $('.filters th').eq($(api.column(colIdx).header()).index())
                    )
                        .off('keyup change')
                        .on('change', function(e) {
                            // Get the search value
                            $(this).attr('title', $(this).val());
                            var regexr = '({search})';

                            var cursorPosition = this.selectionStart;
                            // Search the column for that value
                            api
                                .column(colIdx)
                                .search(
                                    this.value != ''
                                        ? regexr.replace('{search}', '(((' + this.value + ')))')
                                        : '',
                                    this.value != '',
                                    this.value == ''
                                )
                                .draw();
                        })
                        .on('keyup', function(e) {
                            e.stopPropagation();

                            $(this).trigger('change');
                            $(this)
                                .focus()[0]
                                .setSelectionRange(cursorPosition, cursorPosition);
                        });
                });
        },
    });

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
        
        var formData = new FormData(this);
        var url = '{{ route("esc-ct.store") }}';
        
        if (document.getElementById('nr_ID').value) {
            url = '{{ route("esc-ct.update") }}';
        }
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
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
                toastr.error('Erro ao processar solicitação');
            }
        });
    });

    // Carregar valores salvos no sessionStorage
    document.getElementById('txtLotes').value = lote;
    document.getElementById('cmbprod').value = produto;
    document.getElementById('txPeriodoINI').value = dtini;
    document.getElementById('txPeriodoATE').value = dtate;
    
    // Carregar dados iniciais dos usuários se houver valores nos campos
    if (lote && produto && document.getElementById('selTarefas').value) {
        atualizaCombos();
    }
});
</script>
@endpush
