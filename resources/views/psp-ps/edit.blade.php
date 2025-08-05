@extends('adminlte::page')

@section('title', 'PSP-PS - Editar')

@section('content_header')
    <h5 class="m-0">Editar Pasta Nº {{ $pasta->pst_numero }} Produto: {{ $pasta->pst_produto510 }} Lote: {{ $pasta->Lote }} </h5>
@stop

@section('content')
<form action="{{ route('psp-ps.update', $pasta->pst_numero) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Previsão Controle</label>
                        <input type="date" name="pst_previsaocontrole"
                               class="form-control @error('pst_previsaocontrole') is-invalid @enderror"
                               value="{{ old('pst_previsaocontrole', \Carbon\Carbon::parse($pasta->pst_previsaocontrole)->format('Y-m-d')) }}">
                        @error('pst_previsaocontrole')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Previsão Produção</label>
                        <input type="date" name="pst_previsaoproducao"
                               class="form-control @error('pst_previsaoproducao') is-invalid @enderror"
                               value="{{ old('pst_previsaoproducao', \Carbon\Carbon::parse($pasta->pst_previsaoproducao)->format('Y-m-d')) }}">
                        @error('pst_previsaoproducao')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Revisado por (Controle)</label>
                        <select name="pst_revisadoporc" class="form-control @error('pst_revisadoporc') is-invalid @enderror">
                            <option value="">Selecione...</option>
                            @isset($revisadores)
                                @foreach($revisadores as $revisor)
                                    <option value="{{ $revisor->cdusuario }}" {{ old('pst_revisadoporc', $pasta->pst_revisadoporc ?? '') == $revisor->cdusuario ? 'selected' : '' }}>
                                        {{ $revisor->nome }}
                                    </option>
                                @endforeach
                            @endisset
                        </select>
                        @error('pst_revisadoporc')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Revisado por (Produção)</label>
                        <select name="pst_revisadopor" class="form-control @error('pst_revisadopor') is-invalid @enderror">
                            <option value="">Selecione...</option>
                            @isset($revisadores)
                                @foreach($revisadores as $revisor)
                                    <option value="{{ $revisor->cdusuario }}" {{ old('pst_revisadopor', $pasta->pst_revisadopor ?? '') == $revisor->cdusuario ? 'selected' : '' }}>
                                        {{ $revisor->nome }}
                                    </option>
                                @endforeach
                            @endisset
                        </select>
                        @error('pst_revisadopor')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Situação (Controle)</label>
                        <select name="cmbSitControle" class="form-control @error('cmbSitControle') is-invalid @enderror">
                            <option value="">Selecione...</option>
                            @isset($producaoStatus)
                                @foreach($producaoStatus as $status)
                                    <option value="{{ $status->pstprod_status }}" {{ old('cmbSitControle', $pasta->controle_situacao ?? '') == $status->pstprod_status ? 'selected' : '' }}>
                                        {{ $status->pstprod_descricao }}
                                    </option>
                                @endforeach
                            @endisset
                        </select>
                        @error('cmbSitControle')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Situação (Produção)</label>
                        <select name="cmbSitProducao" class="form-control @error('cmbSitProducao') is-invalid @enderror">
                            <option value="">Selecione...</option>
                            @isset($producaoStatus)
                                @foreach($producaoStatus as $status)
                                    <option value="{{ $status->pstprod_status }}" {{ old('cmbSitProducao', $pasta->producao_situacao ?? '') == $status->pstprod_status ? 'selected' : '' }}>
                                        {{ $status->pstprod_descricao }}
                                    </option>
                                @endforeach
                            @endisset
                        </select>
                        @error('cmbSitProducao')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Status (Controle)</label>
                        <select name="cmdStsControle" class="form-control @error('cmdStsControle') is-invalid @enderror">
                            <option value="">Selecione...</option>
                            @isset($statusList)
                                @foreach($statusList as $status)
                                    <option value="{{ $status->status_codigo }}" {{ old('cmdStsControle', $pasta->controle_status ?? '') == $status->status_codigo ? 'selected' : '' }}>
                                        {{ $status->status_descricao }}
                                    </option>
                                @endforeach
                            @endisset
                        </select>
                        @error('cmdStsControle')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Status (Produção)</label>
                        <select name="cmdStsProducao" class="form-control @error('cmdStsProducao') is-invalid @enderror">
                            <option value="">Selecione...</option>
                            @isset($statusList)
                                @foreach($statusList as $status)
                                    <option value="{{ $status->status_codigo }}" {{ old('cmdStsProducao', $pasta->producao_status ?? '') == $status->status_codigo ? 'selected' : '' }}>
                                        {{ $status->status_descricao }}
                                    </option>
                                @endforeach
                            @endisset
                        </select>
                        @error('cmdStsProducao')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Observações (Controle)</label>
                        <textarea name="pst_observacao_controle"
                                  class="form-control @error('pst_observacao_controle') is-invalid @enderror"
                                  rows="3">{{ old('pst_observacao_controle', $pasta->pst_observacao_controle ?? '') }}</textarea>
                        @error('pst_observacao_controle')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Observações (Produção)</label>
                        <textarea name="pst_observacao_producao"
                                  class="form-control @error('pst_observacao_producao') is-invalid @enderror"
                                  rows="3">{{ old('pst_observacao_producao', $pasta->pst_observacao_producao ?? '') }}</textarea>
                        @error('pst_observacao_producao')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Senha</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <a href="{{ route('psp-ps.index') }}"
                       class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </div>
        </div>
    </div>
</form>
@stop
