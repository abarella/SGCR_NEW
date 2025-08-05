@extends('adminlte::page')

@section('title', 'Definição de Série - Intervalo por Lote/Número')

@section('content_header')
    <h5 class="m-0">Definição de Série - Intervalo por Lote/Número</h5>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                @if(isset($mensagem))
                    <div class="alert alert-warning text-center">
                        {{ $mensagem }}
                    </div>
                @endif
                <form id="formDefinicaoSerieIntervaloLote">
                    @csrf

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="txtProduto">Produto:</label>
                            <input type="text" id="txtProduto" name="txtProduto" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="txtLote">Lote:</label>
                            <input type="text" id="txtLote" name="txtLote" class="form-control" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="cmbSerie">Série:</label>
                            <select id="cmbSerie" name="cmbSerie" class="form-control">
                                @if(isset($series))
                                    @foreach($series as $serie)
                                        <option value="{{ $serie->serie }}">{{ $serie->serie }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="cmbLoteIni">Lote/Número Inicial:</label>
                            <select id="cmbLoteIni" class="form-control">
                                @for ($i = 1; $i <= $numero; $i++)
                                    <option value="{{ $i }}" {{ $i == 1 ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>                        
                        <div class="col-md-4">
                            <label for="cmbLoteFim">Lote/Número Final:</label>
                            <select id="cmbLoteFim" class="form-control">
                                @for ($i = 1; $i <= $numero; $i++)
                                    <option value="{{ $i }}" {{ $i == 1 ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>                        
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label>Filtro:</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="rdFiltro" id="rdParaTodos" value="S" checked>
                                <label class="form-check-label" for="rdParaTodos">Para Todos</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="rdFiltro" id="rdSemSerie" value="N">
                                <label class="form-check-label" for="rdSemSerie">Sem Série</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="cmbTecnico">Técnico Operador:</label>
                            <select id="cmbTecnico" name="cmbTecnico" class="form-control">
                                <option value="">Selecione um técnico</option>
                                @if(isset($tecnicos))
                                    @foreach($tecnicos as $tecnico)
                                        <option value="{{ $tecnico->cdusuario }}">{{ $tecnico->nome }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                    </div>
                    <div class="row mb-3">                        
                        <div class="col-md-4">
                            <label for="txtSenha">Senha:</label>
                            <input type="password" id="txtSenha" name="txtSenha" class="form-control" required>
                        </div>

                        <div class="col-md-4 d-flex align-items-end">
                            <div>
                                <button type="button" class="btn btn-success mr-2" onclick="definirSerieAtividadeLoteTable(event)">
                                    <i class="fas fa-save"></i> Gravar Série por Lote/Número
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                                    <i class="fas fa-arrow-left"></i> Voltar
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="cdusuario" value="{{ Auth::id() }}">

@push('js')
<script src="{{ asset('js/definicaoserie/intervalo-lote.js') }}"></script>
@endpush
@stop
