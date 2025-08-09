@extends('adminlte::page')

@section('title', 'PSP-PS - Editar')

@section('css')
<style>
    /* Estilo para tabs personalizados */
    .nav-tabs .nav-link {
        color: #007bff !important; /* Azul para tabs inativos */
        background-color: #f8f9fa !important;
        border-color: #dee2e6 #dee2e6 #fff !important;
    }

    .nav-tabs .nav-link.active {
        color: #dc3545 !important; /* Vermelho para tab ativo */
        background-color: #fff !important;
        border-color: #dee2e6 #dee2e6 #dc3545 !important;
        border-bottom-color: #fff !important;
    }

    .nav-tabs .nav-link:hover {
        border-color: #e9ecef #e9ecef #dee2e6 !important;
    }

    .nav-tabs .nav-link.active:hover {
        border-color: #dee2e6 #dee2e6 #dc3545 !important;
    }

    /* Ícones coloridos */
    .nav-tabs .nav-link i {
        color: inherit;
    }
    .tab-content {
        padding-top: 20px;
    }
</style>
@stop

@php
    // Função helper para converter data dd/mm/yyyy para Y-m-d
    function convertDate($dateString) {
        if (empty($dateString)) return '';

        // Verifica se já está no formato Y-m-d
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateString)) {
            return $dateString;
        }

        // Converte dd/mm/yyyy para Y-m-d
        if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $dateString, $matches)) {
            return $matches[3] . '-' . $matches[2] . '-' . $matches[1];
        }

        return '';
    }
@endphp

@section('content_header')
    <h5 class="m-0">Editar Pasta Nº {{ $pasta->pst_numero }} Produto: {{ $pasta->pst_produto510 }} Lote: {{ $pasta->Lote }} </h5>
@stop

@section('content')
<form id="editForm" action="{{ route('psp-ps.update', $pasta->pst_numero) }}" method="POST">
    @csrf
    @method('PUT')

    <input type="hidden" name="active_tab" id="active_tab" value="controle">

    <div class="card">

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Previsão Controle</label>
                        <input type="date" name="pst_previsaocontrole"
                               class="form-control @error('pst_previsaocontrole') is-invalid @enderror"
                               value="{{ old('pst_previsaocontrole', isset($pasta_controle->previsao) ? convertDate($pasta_controle->previsao) : '') }}">
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
                               value="{{ old('pst_previsaoproducao', isset($pasta_producao->previsao) ? convertDate($pasta_producao->previsao) : '') }}">
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
                                    <option value="{{ $revisor->cdusuario }}" {{ old('pst_revisadoporc', isset($pasta_controle->revisadopor) ? $pasta_controle->revisadopor : '') == $revisor->cdusuario ? 'selected' : '' }}>
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
                                    <option value="{{ $revisor->cdusuario }}" {{ old('pst_revisadopor', isset($pasta_producao->revisadopor) ? $pasta_producao->revisadopor : '') == $revisor->cdusuario ? 'selected' : '' }}>
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
                        <label>Observação (Controle)</label>
                        <textarea name="pst_observacao_controle" class="form-control @error('pst_observacao_controle') is-invalid @enderror" rows="3">{{ old('pst_observacao_controle', isset($pasta_controle->pst_obsc) ? $pasta_controle->pst_obsc : '') }}</textarea>
                        @error('pst_observacao_controle')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Observação (Produção)</label>
                        <textarea name="pst_observacao_producao" class="form-control @error('pst_observacao_producao') is-invalid @enderror" rows="3">{{ old('pst_observacao_producao', isset($pasta_producao->pst_obsp) ? $pasta_producao->pst_obsp : '') }}</textarea>
                        @error('pst_observacao_producao')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Situação da Pasta</label>
                        <select name="cmdStsControle" class="form-control @error('cmdStsControle') is-invalid @enderror">
                            <option value="">Selecione...</option>
                            @isset($statusList)
                                @foreach($statusList as $status)
                                    <option value="{{ $status->status_codigo }}" {{ old('cmdStsControle', isset($pasta_controle->pststs_codigo) ? $pasta_controle->pststs_codigo : '') == $status->status_codigo ? 'selected' : '' }}>
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
                        <label>Situação da Produção</label>
                        <select name="cmdStsProducao" class="form-control @error('cmdStsProducao') is-invalid @enderror">
                            <option value="">Selecione...</option>
                            @isset($producaoStatus)
                                @foreach($producaoStatus as $status)
                                    <option value="{{ $status->pstprod_status }}" {{ old('cmdStsProducao', isset($pasta_producao->pstprod_status) ? $pasta_producao->pstprod_status : '') == $status->pstprod_status ? 'selected' : '' }}>
                                        {{ $status->pstprod_descricao }}
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
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Senha</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <div>
                            <a href="{{ route('psp-ps.index') }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Salvar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Atualiza o tab ativo quando o usuário clica
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var target = $(e.target).attr("href");
        if (target === '#controle') {
            $('#active_tab').val('controle');
        } else if (target === '#producao') {
            $('#active_tab').val('producao');
        }
    });

    $('#editForm').on('submit', function(e) {
        e.preventDefault();

        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        var originalText = submitBtn.text();

        submitBtn.prop('disabled', true).text('Salvando...');

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    var Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });

                    Toast.fire({
                        icon: 'success',
                        title: response.message
                    });

                    setTimeout(function() {
                        window.location.href = response.redirect;
                    }, 1500);
                } else {
                    var Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 4000,
                        timerProgressBar: true
                    });

                    Toast.fire({
                        icon: 'error',
                        title: response.message
                    });
                }
            },
            error: function(xhr) {
                var message = 'Erro ao processar a requisição.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }

                var Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true
                });

                Toast.fire({
                    icon: 'error',
                    title: message
                });
            },
            complete: function() {
                submitBtn.prop('disabled', false).text(originalText);
            }
        });
    });
});
</script>
@stop
