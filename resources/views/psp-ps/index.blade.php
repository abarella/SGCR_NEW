@extends('adminlte::page')

@section('title', 'PSP-PS')

@section('content_header')
    <h5 class="m-0">PSP-PS - Pasta de Serviço</h5>
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
                <input type="number" id="pasta" class="form-control" maxlength="10">
            </div>
        </div>



        @php
            $heads = [
                ['label' => 'Ações', 'width' => 10],
                ['label' => 'Pasta'],
                ['label' => 'Produto'],
                ['label' => 'Lote'],
                ['label' => 'Autorizado em'],
                ['label' => 'Prev. Controle'],
                ['label' => 'Prev. Produção'],
                ['label' => 'Produção Revisado Por'],
                ['label' => 'Controle Revisado Por'],
                ['label' => 'Status'],
                ['label' => 'Status Produção'],
                ['label' => 'Obs Produção'],
                ['label' => 'Obs Controle'],
                ['label' => 'Observação'],
            ];

            $config = [
                'processing' => true,
                'serverSide' => false,
                'responsive' => true,
                'stateSave' => true,
                'lengthMenu' => [[5, 10, 25, 50, -1], ['5 registros','10 registros', '25 registros', '50 registros', 'Todos']],
                'columns' => [
                    ['data' => 'acoes', 'orderable' => false, 'className' => 'text-nowrap'],
                    ['data' => 'pst_numero', 'className' => 'text-nowrap'],
                    ['data' => 'nome_comercial', 'className' => 'text-nowrap'],
                    ['data' => 'lote', 'className' => 'text-nowrap'],
                    ['data' => 'registro', 'className' => 'text-nowrap'],
                    ['data' => 'pst_previsaocontrole', 'className' => 'text-nowrap'],
                    ['data' => 'pst_previsaoproducao', 'className' => 'text-nowrap'],
                    ['data' => 'producao_revisadopor', 'className' => 'text-nowrap'],
                    ['data' => 'controle_revisadopor', 'className' => 'text-nowrap'],
                    ['data' => 'status', 'className' => 'text-nowrap'],
                    ['data' => 'status_producao', 'className' => 'text-nowrap'],
                    ['data' => 'obs_producao', 'className' => 'text-nowrap'],
                    ['data' => 'obs_controle', 'className' => 'text-nowrap'],
                    ['data' => 'pst_observacao', 'className' => 'text-nowrap'],
                ],
                'order' => [[1, 'desc']],
                'language' => ['url' => 'https://cdn.datatables.net/plug-ins/2.0.7/i18n/pt-BR.json'],
                'dom' => '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>><"row"<"col-sm-12"tr>><"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                'buttons' => ['excel', 'pdf', 'print'],
            ];
        @endphp

        <x-adminlte-datatable id="grid-pastas" :heads="$heads" xhead-theme="light" :config="$config"
            display striped hoverable bordered compact compressed with-buttons>
            <tbody id="table-body">
                <!-- Dados serão inseridos aqui via JavaScript -->
            </tbody>
        </x-adminlte-datatable>
    </div>
</div>
@stop

@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugin', true)

@section('css')
<style>
    .dataTables_wrapper .table td {
        white-space: nowrap;
        padding: 5px 8px;
    }
</style>
@stop

@section('js')
<script>
$(function() {
    // Pega a instância já inicializada do DataTable
    let dataTable = $('#grid-pastas').DataTable();

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
                dataTable.clear().draw();
            },
            success: function(response) {
                console.log('Resposta recebida:', response);

                if (response.data && response.data.length > 0) {
                    console.log('Dados encontrados:', response.data.length, 'registros');
                    dataTable.clear().rows.add(response.data).draw();
                } else {
                    console.log('Nenhum dado encontrado na resposta');
                    dataTable.clear().draw();
                    //$('#table-body').html('<tr><td colspan="12" class="text-center">Nenhum registro encontrado</td></tr>');
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
                $('#table-body').html(`<tr><td colspan="12" class="text-center text-danger">${errorMessage}</td></tr>`);
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
