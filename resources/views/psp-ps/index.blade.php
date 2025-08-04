@extends('adminlte::page')

@section('title', 'PSP-PS')

@section('content_header')
    <h1>PSP-PS - Pasta de Serviço</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-3">
                <label>Mês</label>
                <select id="mes" class="form-control">
                    <option value="">Todos</option>
                    @php
                        $meses = [
                            1 => 'Janeiro',
                            2 => 'Fevereiro',
                            3 => 'Março',
                            4 => 'Abril',
                            5 => 'Maio',
                            6 => 'Junho',
                            7 => 'Julho',
                            8 => 'Agosto',
                            9 => 'Setembro',
                            10 => 'Outubro',
                            11 => 'Novembro',
                            12 => 'Dezembro'
                        ];
                    @endphp
                    @foreach($meses as $num => $nome)
                        <option value="{{ $num }}" {{ $num == date('n') ? 'selected' : '' }}>{{ $nome }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label>Ano</label>
                <select id="ano" class="form-control">
                    @foreach(range(date('Y')-5, date('Y')) as $ano)
                        <option value="{{ $ano }}" {{ $ano == date('Y') ? 'selected' : '' }}>{{ $ano }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label>Status</label>
                <select id="status" class="form-control">
                    <option value="">Todos</option>
                </select>
            </div>
            <div class="col-md-3">
                <label>Nº Pasta</label>
                <input type="text" id="pasta" class="form-control" maxlength="10">
            </div>
        </div>

        

        <div class="table-responsive">
            <table id="grid-pastas" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Ações</th>
                        <th>Pasta</th>
                        <th>Produto</th>
                        <th>Lote</th>
                        <th>Registro</th>
                        <th>Prev. Controle</th>
                        <th>Prev. Produção</th>
                        <th>Status</th>
                        <th>Status Produção</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    <!-- Dados serão inseridos aqui via JavaScript -->
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
$(function() {
    // Carrega status
    $.get("{{ route('psp-ps.status') }}", function(data) {
        console.log('Dados recebidos:', data);
        if (Array.isArray(data)) {
            data.forEach(function(item) {
                $('#status').append(`<option value="${item.pststs_codigo}">${item.pststs_descricao}</option>`);
            });
        } else {
            console.error('Dados recebidos não são um array:', data);
        }
    });

    // Função para carregar os dados
    function carregarDados() {
        const params = {
            mes: $('#mes').val() || '',
            ano: $('#ano').val() || new Date().getFullYear(),
            tipo: $('#status').val() || '',
            pst_numero: $('#pasta').val() || '',
            ordem: 0,
            grupo: '{{ session('cdgrupo') }}'
        };

        console.log('Parâmetros da requisição:', params);
        console.log('Grupo da sessão:', '{{ session('cdgrupo') }}');

        $.ajax({
            url: "{{ route('psp-ps.lista') }}",
            type: 'GET',
            data: params,
            beforeSend: function() {
                console.log('Iniciando requisição para:', "{{ route('psp-ps.lista') }}");
                $('#table-body').html('<tr><td colspan="9" class="text-center">Carregando...</td></tr>');
            },
            success: function(response) {
                console.log('Resposta recebida:', response);
                $('#table-body').empty();

                if (response.data && response.data.length > 0) {
                    console.log('Dados encontrados:', response.data.length, 'registros');
                    response.data.forEach(function(item, index) {
                        console.log('Item', index, ':', item);
                        $('#table-body').append(`
                            <tr>
                                <td>${item.acoes || ''}</td>
                                <td>${item.pst_numero || ''}</td>
                                <td>${item.nome_comercial || ''}</td>
                                <td>${item.lote || ''}</td>
                                <td>${item.registro || ''}</td>
                                <td>${item.pst_previsaocontrole || ''}</td>
                                <td>${item.pst_previsaoproducao || ''}</td>
                                <td>${item.status || ''}</td>
                                <td>${item.status_producao || ''}</td>
                            </tr>
                        `);
                    });
                } else {
                    console.log('Nenhum dado encontrado na resposta');
                    $('#table-body').html('<tr><td colspan="9" class="text-center">Nenhum registro encontrado</td></tr>');
                }
            },
            error: function(xhr, error, thrown) {
                console.error('Erro na requisição:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    error: error,
                    thrown: thrown
                });
                let errorMessage = 'Erro ao carregar os dados: ';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage += xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    errorMessage += xhr.responseText;
                } else {
                    errorMessage += error;
                }
                $('#table-body').html(`<tr><td colspan="9" class="text-center text-danger">${errorMessage}</td></tr>`);
            }
        });
    }

    // Carrega dados iniciais
    carregarDados();

    // Eventos de filtro
    $('#mes, #ano, #status, #pasta').change(function() {
        carregarDados();
    });

    // Botão de teste
    $('#btn-test').click(function() {
        $.get("{{ route('psp-ps.test') }}", function(data) {
            console.log('Dados de teste:', data);
            alert('Verifique o console para ver os dados de teste');
        }).fail(function(xhr, status, error) {
            console.error('Erro no teste:', xhr.responseText);
            alert('Erro no teste: ' + error);
        });
    });
});
</script>
@stop
