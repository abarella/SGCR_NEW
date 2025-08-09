@extends('adminlte::page')

@section('title', 'PSP-PS - Detalhes da Pasta')

@section('content_header')
    <h5 class="m-0">Pasta Nº {{ $pasta->pst_numero ?? 'N/A' }}
        @if(isset($pasta->pst_produto510))
            - Produto: {{ $pasta->pst_produto510 }}
        @endif
        @if(isset($pasta->Lote))
            - Lote: {{ $pasta->Lote }}
        @endif
    </h5>
@stop

@section('content')


<div class="row">
    <!-- Lista 1 - PPST_LISTA2 (Registros/Observação) -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title">Registros</h6>
            </div>
            <div class="card-body">
                @if(isset($lista2Data) && count($lista2Data) > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-sm">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Data</th>
                                    <th>Usuário</th>
                                    <th>Registros</th>
                                    <th>Observação</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lista2Data as $item)
                                    <tr>
                                        <td>{{ isset($item->data) ? $item->data : '' }}</td>
                                        <td>{{ isset($item->nome) ? $item->nome : '' }}</td>
                                        <td>{{ isset($item->ocorrencia) ? $item->ocorrencia : '' }}</td>
                                        <td>{{ isset($item->pstfase_obs) ? $item->pstfase_obs : '' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        Nenhum registro encontrado.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Lista 2 - PPST_LISTA3 (Ocorrências) -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title">Ocorrências</h6>
            </div>
            <div class="card-body">
                @if(isset($lista3Data) && count($lista3Data) > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-sm">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Data</th>
                                    <th>Usuário</th>
                                    <th>Ocorrência</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lista3Data as $item)
                                    <tr>
                                        <td>{{ isset($item->data) ? $item->data : '' }}</td>
                                        <td>{{ isset($item->nome) ? $item->nome : '' }}</td>
                                        <td>{{ isset($item->ocorrencia) ? $item->ocorrencia : '' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        Nenhum registro encontrado.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Botões de Ação -->
<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <a href="{{ route('psp-ps.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
    </div>
</div>

@stop

@section('css')
<style>
    .table th {
        font-size: 0.85rem;
        font-weight: bold;
    }
    .table td {
        font-size: 0.8rem;
    }
    .card-header h6 {
        margin: 0;
        font-weight: bold;
    }
    /* Cabeçalho da tabela azul com letra branca */
    .thead-dark th {
        background-color: #007bff !important;
        border-color: #0056b3 !important;
        color: white !important;
    }
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Adiciona classes para melhorar a responsividade
    $('.table-responsive').addClass('table-sm');

    // Log para debug
    console.log('Lista 2:', @json($lista2Data ?? []));
    console.log('Lista 3:', @json($lista3Data ?? []));
});
</script>
@stop
