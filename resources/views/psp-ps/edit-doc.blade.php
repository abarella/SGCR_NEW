@extends('adminlte::page')

@section('title', 'PSP-PS - Editar Documentação')

@section('content_header')
    <h5 class="m-0">Editar Pasta Nº {{ $pasta->pst_numero }} Produto: {{ $pasta->pst_produto510 }} Lote: {{ $pasta->Lote }} </h5>
@stop

@section('css')
<style>
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
    
    // Função helper para verificar propriedades de forma segura
    function getProperty($object, $property, $default = '') {
        if (!isset($object) || !is_object($object)) {
            return $default;
        }
        
        if (!property_exists($object, $property)) {
            return $default;
        }
        
        $value = $object->$property;
        return !empty($value) ? $value : $default;
    }
@endphp

@section('content')




<form id="editDocForm" action="{{ route('psp-ps.update-doc', $pasta->pst_numero) }}" method="POST">
    @csrf
    @method('PUT')
    
    <input type="hidden" name="active_tab" id="active_tab" value="controle">

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Data de Entrega (Controle)</label>
                        <input type="date" name="data_entrega_controle"
                               class="form-control @error('data_entrega_controle') is-invalid @enderror"
                               value="{{ old('data_entrega_controle', convertDate(getProperty($pasta_controle, 'previsao'))) }}">
                        @error('data_entrega_controle')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Data de Entrega (Produção)</label>
                        <input type="date" name="data_entrega_producao"
                               class="form-control @error('data_entrega_producao') is-invalid @enderror"
                               value="{{ old('data_entrega_producao', convertDate(getProperty($pasta_producao, 'previsao'))) }}">
                        @error('data_entrega_producao')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Observações (Controle)</label>
                        <textarea name="pst_obsc"
                                  class="form-control @error('pst_obsc') is-invalid @enderror"
                                  rows="3">{{ old('pst_obsc', getProperty($pasta_controle, 'pst_obsc')) }}</textarea>
                        @error('pst_obsc')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Observações (Produção)</label>
                        <textarea name="pst_obsp"
                                  class="form-control @error('pst_obsp') is-invalid @enderror"
                                  rows="3">{{ old('pst_obsp', getProperty($pasta_producao, 'pst_obsp')) }}</textarea>
                        @error('pst_obsp')
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
                                    <option value="{{ $status->status_codigo }}" {{ old('cmdStsControle', getProperty($pasta_controle, 'pststs_codigo')) == $status->status_codigo ? 'selected' : '' }}>
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
                                    @php
                                        $valorSelecionado = getProperty($pasta_producao, 'pstprod_status');
                                        $isSelected = ($valorSelecionado == $status->pstprod_status) || 
                                                     (empty($valorSelecionado) && $status->pstprod_status == '0');
                                    @endphp
                                    <option value="{{ $status->pstprod_status }}" {{ $isSelected ? 'selected' : '' }}>
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
                            <a href="{{ route('psp-ps.index') }}"
                               class="btn btn-secondary">Cancelar</a>
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
    // Atualiza o tab ativo quando clicado
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var target = $(e.target).attr("href");
        if (target === '#controle') {
            $('#active_tab').val('controle');
        } else if (target === '#producao') {
            $('#active_tab').val('producao');
        }
    });

    $('#editDocForm').on('submit', function(e) {
        e.preventDefault();
        
        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        var originalText = submitBtn.text();
        
        // Desabilita o botão e mostra loading
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
                    // Toast de sucesso
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
                    
                    // Redireciona após o toast
                    setTimeout(function() {
                        window.location.href = response.redirect;
                    }, 1500);
                } else {
                    // Toast de erro
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
                // Reabilita o botão
                submitBtn.prop('disabled', false).text(originalText);
            }
        });
    });
});
</script>
@stop 