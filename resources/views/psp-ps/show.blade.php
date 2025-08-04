@extends('adminlte::page')

@section('title', 'PSP-PS - Detalhes')

@section('content_header')
    <h1>Detalhes da Pasta {{ $pasta->pst_numero }}</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <dl class="row">
                    <dt class="col-sm-4">Número da Pasta</dt>
                    <dd class="col-sm-8">{{ $pasta->pst_numero }}</dd>

                    <dt class="col-sm-4">Produto</dt>
                    <dd class="col-sm-8">{{ $pasta->nome_comercial }}</dd>

                    <dt class="col-sm-4">Lote</dt>
                    <dd class="col-sm-8">{{ $pasta->lote }}</dd>
                </dl>
            </div>
            <div class="col-md-6">
                <dl class="row">
                    <dt class="col-sm-4">Registro</dt>
                    <dd class="col-sm-8">{{ $pasta->registro }}</dd>

                    <dt class="col-sm-4">Previsão Controle</dt>
                    <dd class="col-sm-8">{{ \Carbon\Carbon::parse($pasta->pst_previsaocontrole)->format('d/m/Y') }}</dd>

                    <dt class="col-sm-4">Previsão Produção</dt>
                    <dd class="col-sm-8">{{ \Carbon\Carbon::parse($pasta->pst_previsaoproducao)->format('d/m/Y') }}</dd>
                </dl>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <dl class="row">
                    <dt class="col-sm-2">Status</dt>
                    <dd class="col-sm-10">{{ $pasta->status }}</dd>

                    <dt class="col-sm-2">Status Produção</dt>
                    <dd class="col-sm-10">{{ $pasta->status_producao }}</dd>

                    <dt class="col-sm-2">Obs. Produção</dt>
                    <dd class="col-sm-10">{{ $pasta->pst_obsp }}</dd>

                    <dt class="col-sm-2">Obs. Controle</dt>
                    <dd class="col-sm-10">{{ $pasta->pst_obsc }}</dd>

                    <dt class="col-sm-2">Observação</dt>
                    <dd class="col-sm-10">{{ $pasta->pst_observacao }}</dd>
                </dl>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <a href="{{ route('psp-ps.index') }}" class="btn btn-secondary">Voltar</a>

                @can('edit-psp-ps')
                    @if(session('cdgrupo') == 6)
                        <a href="{{ route('psp-ps.edit', $pasta->pst_numero) }}"
                           class="btn btn-primary">Alterar</a>
                    @endif
                @endcan

                @can('edit-psp-ps-doc')
                    @if(in_array(session('cdgrupo'), [2,3,4,5,6]))
                        <a href="{{ route('psp-ps.edit-doc', $pasta->pst_numero) }}"
                           class="btn btn-info">Alterar Data de Entrega</a>
                    @endif
                @endcan
            </div>
        </div>
    </div>
</div>
@stop
