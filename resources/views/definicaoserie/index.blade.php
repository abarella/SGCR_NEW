@extends('adminlte::page')

@section('title', 'Definição de Série')

@section('content_header')
    <h5 class="m-0">Definição de Série</h5>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form id="formDefinicaoSerie">
                    @csrf
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="cmbprod">Produto:</label>
                                <select id="cmbprod" name="cmbprod" class="form-control">
                                    <option value="0" {{ old('cmbprod') == 0 ? 'selected' : '' }}>Selecione um produto</option>
                                    @foreach($produtos as $produto)
                                    <option value="{{ $produto['codigo'] }}" {{ old('cmbprod') == $produto['codigo'] ? 'selected' : '' }}>
                                        {{ $produto['descricao'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="cmbLote">Lote:</label>
                                <div id="loteContainer">
                                    <select id="cmbLote" name="cmbLote" class="form-control">
                                        <option value=""></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="button" class="btn btn-primary btn-block" onclick="pesquisaListaSerie()">
                                    <i class="fas fa-search"></i> Pesquisar
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-success" onclick="chamaDefinirIntervalo()">
                                <i class="fas fa-list"></i> Definir por Intervalo de Atividade
                            </button>
                            <button type="button" class="btn btn-info" onclick="chamaDefinirIntervaloLote()">
                                <i class="fas fa-layer-group"></i> Definir por Intervalo de Lote
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table id="tblistadefinicao" class="table table-bordered table-striped">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th>Lote/Número</th>
                                        <th>Médico Responsável</th>
                                        <th>UF</th>
                                        <th>Atividade(mCi)</th>
                                        <th>Série</th>
                                        <th>Produção</th>
                                        <th>Calibração</th>
                                        <th>Observação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($listaSerie) && count($listaSerie) > 0)
                                        @foreach($listaSerie as $item)
                                            <tr>
                                                <td>{{ $item->numero }}</td>
                                                <td>{{ $item->medico ?? '' }}</td>
                                                <td>{{ $item->uf ?? '' }}</td>
                                                <td>{{ $item->atividade ?? '' }}</td>
                                                <td>
                                                    <input type="text" class="form-control p110serie" 
                                                           value="{{ $item->serie ?? '' }}" 
                                                           data-chve="{{ $item->numero }}" 
                                                           style="width: 80px;">
                                                </td>
                                                <td>{{ $item->producao ?? '' }}</td>
                                                <td>{{ $item->calibracao ?? '' }}</td>
                                                <td>{{ $item->observacao ?? '' }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="8" class="alert alert-warning mt-3 text-center">Nenhum registro encontrado.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <label for="txtSenha">Senha:</label>
                        <input type="password" id="txtSenha" name="txtSenha" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <br>
                        <button type="button" class="btn btn-success" onclick="definirSerieTable(event)">
                            <i class="fas fa-save"></i> Gravar Série
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="cdusuario" value="{{ Auth::id() }}">

@push('js')
<script src="{{ asset('js/definicaoserie/index.js') }}"></script>
@endpush
@stop
