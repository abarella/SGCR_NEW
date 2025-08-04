
@extends('adminlte::page')

@section('title', 'Consulta Blindagem XPasta')

@section('content_header')
    <h5 class="m-0">Consulta Blindagem XPasta</h5>
    <meta name="base-url" content="{{ url('/') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@stop

@section('content')
<form method="GET" action="{{ url('bxp') }}" class="form-data-table align-center" id="form-bxp">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="nrlote">Nr Lote</label>
                            <input type="text" name="nrlote" id="nrlote" class="form-control" value="{{ old('nrlote', $nrlote) }}">
                        </div>
                        <div class="col-md-4">
                            <label for="nrserie">Nr Série (opcional)</label>
                            <input type="text" name="nrserie" id="nrserie" class="form-control" value="{{ old('nrserie', $nrserie) }}">
                        </div>
                        <div class="col-md-4 align-self-end">
                            <button type="submit" class="btn btn-primary">Buscar</button>
                            @if(!empty($resultados))
                                @php
                                    $contadorDestacados = 0;
                                    foreach($resultados as $row) {
                                        $rowArr = (array)$row;
                                        $rgCastelo = $rowArr['RgSaida_Castelo'] ?? '';
                                        $rgPasta = $rowArr['RgSaida_Pasta'] ?? '';
                                        $destacar = empty($rgCastelo) || empty($rgPasta) && $rgCastelo !== $rgPasta;
                                        if($destacar) $contadorDestacados++;
                                    }
                                @endphp
                                @if($contadorDestacados > 0)
                                    <span class="btn bg-danger" title="Linhas destacadas">{{ $contadorDestacados }} Registros com problemas </span>
                                @endif
                            @endif
                        </div>
                    </div>

                    @if(!empty($resultados))
                        @php
                            $heads = [];
                            if (!empty($resultados[0])) {
                                foreach ((array)$resultados[0] as $col => $val) {
                                    $heads[] = [ 'label' => $col ];
                                }
                            }
                            $config = [
                                'responsive' => true,
                                'stateSave' => true,
                                'lengthMenu' => [[5, 10, 25, 50, -1], ['5 registros','10 registros', '25 registros', '50 registros', 'Todos']],
                                'paging' => true,
                                'language' => [ 'url' => 'https://cdn.datatables.net/plug-ins/2.0.7/i18n/pt-BR.json' ],
                            ];
                        @endphp
                        <x-adminlte-datatable id="bxp-table" :heads="$heads" head-theme="light" :config="$config" striped hoverable bordered compact compressed with-buttons>
                            @foreach($resultados as $row)
                                @php
                                    $destacar = false; // sempre inicializa
                                    $rowArr = (array)$row;
                                    $rgCastelo = $rowArr['RgSaida_Castelo'] ?? '';
                                    $rgPasta = $rowArr['RgSaida_Pasta'] ?? '';
                                    $destacar = empty($rgCastelo) || empty($rgPasta) && $rgCastelo !== $rgPasta;
                                @endphp
                                <tr @if($destacar) class="table-danger" @endif>
                                    @foreach($rowArr as $val)
                                        <td>{{ $val }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </x-adminlte-datatable>
                    @elseif(request()->has('nrlote'))
                        <div class="alert alert-warning mt-3">Nenhum resultado encontrado.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</form>
@stop
