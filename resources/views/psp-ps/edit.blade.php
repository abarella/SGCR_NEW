@extends('adminlte::page')

@section('title', 'PSP-PS - Editar')

@section('content_header')
    <h1>Editar Pasta {{ $pasta->pst_numero }}</h1>
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
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Observações</label>
                        <textarea name="pst_observacao"
                                  class="form-control @error('pst_observacao') is-invalid @enderror"
                                  rows="3">{{ old('pst_observacao', $pasta->pst_observacao) }}</textarea>
                        @error('pst_observacao')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <a href="{{ route('psp-ps.show', $pasta->pst_numero) }}"
                       class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </div>
        </div>
    </div>
</form>
@stop
