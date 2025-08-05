@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var checkAll = document.getElementById('checkAll');
        if (checkAll) {
            checkAll.addEventListener('change', function() {
                var checkboxes = document.querySelectorAll('input[name="ids[]"]');
                for (var i = 0; i < checkboxes.length; i++) {
                    checkboxes[i].checked = checkAll.checked;
                }
            });
        }
    });
</script>
@endpush
@extends('adminlte::page')
@section('title', 'Pastas - Altera Datas')
@section('content_header')
    <h5 class="m-0">Pastas - Altera Datas</h5>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var checkAll = document.getElementById('checkAll');
        if (checkAll) {
            checkAll.addEventListener('change', function() {
                var checkboxes = document.querySelectorAll('input[name="ids[]"]');
                for (var i = 0; i < checkboxes.length; i++) {
                    checkboxes[i].checked = checkAll.checked;
                }
            });
        }
    });
</script>
@endsection
@section('content')
<form method="GET" action="{{ route('psp-ad.index') }}" class="mb-3">
    <div class="row">
        <div class="col-md-2">
            <label>Lote</label>
            <input type="text" name="lote" class="form-control" value="{{ $filtros['lote'] ?? '' }}">
        </div>
        <div class="col-md-2">
            <label>Série</label>
            <input type="text" name="serie" class="form-control" value="{{ $filtros['serie'] ?? '' }}">
        </div>
        <div class="col-md-3">
            <label>Produto</label>
            <select name="produto" class="form-control">
                <option value="">Selecione...</option>
                @php
                    // Função para normalizar espaços
                    $normalize = function($str) {
                        return preg_replace('/\s+/', ' ', trim($str));
                    };
                    $filtroProduto = isset($filtros['produto']) ? $normalize($filtros['produto']) : '';
                @endphp
                @foreach($produtos as $prod)
                    @php $codigoNormalizado = $normalize($prod['codigo']); @endphp
                    <option value="{{ $prod['codigo'] }}" @if($filtroProduto === $codigoNormalizado) selected @endif>{{ $prod['codigo'] }} </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 align-self-end">
            <button type="submit" class="btn btn-primary">Buscar</button>
        </div>
    </div>
</form>
@if((is_array($pedidos) && count($pedidos)) || (is_object($pedidos) && method_exists($pedidos, 'count') && $pedidos->count()))
<form method="POST" action="{{ route('psp-ad.atualizar') }}">
    @csrf
    <div style="overflow-x: auto; max-height: 350px;">
    <table class="table table-bordered table-striped table-sm compact-table" style="min-width: 100px;">

        <thead class="btn-primary">
            <tr>
                <th><input type="checkbox" id="checkAll"></th>
                <th>Nr. Pedido</th>
                <th>Lote/Número</th>
                <th>Cliente</th>
                <th>Médico Responsável</th>
                <th>Data de Fracionamento</th>
                <th>Data de Calibração</th>
                <th>Data de Validade</th>
                <th>Ativ. Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pedidos as $pedido)
            <tr>
                <td><input type="checkbox" name="ids[]" value="{{ $pedido->id }}"></td>
                <td>{{ $pedido->nr_pedido }}</td>
                <td>{{ $pedido->lote }}</td>
                <td>{{ $pedido->cliente }}</td>
                <td>{{ $pedido->medico }}</td>
                <td>{{ $pedido->data_fracionamento }}</td>
                <td>{{ $pedido->data_calibracao }}</td>
                <td>{{ $pedido->data_validade }}</td>
                <td>{{ $pedido->atividade_total }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
    <div class="row mb-3">
        <div class="col-md-3">
            <label>Data de Fracionamento</label>
            <input type="datetime-local" name="data_fracionamento" class="form-control" required>
        </div>
        <div class="col-md-3">
            <label>Data de Calibração</label>
            <input type="datetime-local" name="data_calibracao" class="form-control" required>
        </div>
        <div class="col-md-2 align-self-end">
            <button type="submit" class="btn btn-success">Confirmar</button>
        </div>
    </div>
</form>
@if(is_object($pedidos) && method_exists($pedidos, 'links'))
    {{ $pedidos->links() }}
@endif
@else
    <div class="alert alert-warning mt-3">Nenhum registro encontrado.</div>
@endif
@stop
