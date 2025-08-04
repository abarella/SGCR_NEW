@extends('adminlte::page')

@section('title', 'Editar Alteração de Estoque')

@section('content_header')
    <h1>Editar Alteração de Estoque #{{ $alteracao->id }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Editar Alteração de Estoque</h3>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('prl-ae.update', $alteracao) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="data_alteracao">Data de Alteração *</label>
                            <input type="date" name="data_alteracao" id="data_alteracao" class="form-control @error('data_alteracao') is-invalid @enderror" value="{{ old('data_alteracao', $alteracao->data_alteracao->format('Y-m-d')) }}" required>
                            @error('data_alteracao')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="produto">Produto *</label>
                            <input type="text" name="produto" id="produto" class="form-control @error('produto') is-invalid @enderror" value="{{ old('produto', $alteracao->produto) }}" required>
                            @error('produto')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="lote">Lote *</label>
                            <input type="text" name="lote" id="lote" class="form-control @error('lote') is-invalid @enderror" value="{{ old('lote', $alteracao->lote) }}" required>
                            @error('lote')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tipo_alteracao">Tipo de Alteração *</label>
                            <select name="tipo_alteracao" id="tipo_alteracao" class="form-control @error('tipo_alteracao') is-invalid @enderror" required>
                                <option value="">Selecione...</option>
                                <option value="entrada" {{ old('tipo_alteracao', $alteracao->tipo_alteracao) == 'entrada' ? 'selected' : '' }}>Entrada</option>
                                <option value="saida" {{ old('tipo_alteracao', $alteracao->tipo_alteracao) == 'saida' ? 'selected' : '' }}>Saída</option>
                                <option value="ajuste" {{ old('tipo_alteracao', $alteracao->tipo_alteracao) == 'ajuste' ? 'selected' : '' }}>Ajuste</option>
                            </select>
                            @error('tipo_alteracao')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="quantidade_atual">Quantidade Atual *</label>
                            <input type="number" step="0.01" name="quantidade_atual" id="quantidade_atual" class="form-control @error('quantidade_atual') is-invalid @enderror" value="{{ old('quantidade_atual', $alteracao->quantidade_atual) }}" required>
                            @error('quantidade_atual')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="quantidade_nova">Quantidade Nova *</label>
                            <input type="number" step="0.01" name="quantidade_nova" id="quantidade_nova" class="form-control @error('quantidade_nova') is-invalid @enderror" value="{{ old('quantidade_nova', $alteracao->quantidade_nova) }}" required>
                            @error('quantidade_nova')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="unidade">Unidade *</label>
                            <select name="unidade" id="unidade" class="form-control @error('unidade') is-invalid @enderror" required>
                                <option value="">Selecione...</option>
                                <option value="mg" {{ old('unidade', $alteracao->unidade) == 'mg' ? 'selected' : '' }}>mg</option>
                                <option value="g" {{ old('unidade', $alteracao->unidade) == 'g' ? 'selected' : '' }}>g</option>
                                <option value="kg" {{ old('unidade', $alteracao->unidade) == 'kg' ? 'selected' : '' }}>kg</option>
                                <option value="ml" {{ old('unidade', $alteracao->unidade) == 'ml' ? 'selected' : '' }}>ml</option>
                                <option value="l" {{ old('unidade', $alteracao->unidade) == 'l' ? 'selected' : '' }}>l</option>
                                <option value="mCi" {{ old('unidade', $alteracao->unidade) == 'mCi' ? 'selected' : '' }}>mCi</option>
                                <option value="MBq" {{ old('unidade', $alteracao->unidade) == 'MBq' ? 'selected' : '' }}>MBq</option>
                                <option value="GBq" {{ old('unidade', $alteracao->unidade) == 'GBq' ? 'selected' : '' }}>GBq</option>
                            </select>
                            @error('unidade')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="responsavel">Responsável</label>
                            <select name="responsavel" id="responsavel" class="form-control @error('responsavel') is-invalid @enderror">
                                <option value="">Selecione...</option>
                                @foreach($responsaveis as $responsavel)
                                    <option value="{{ $responsavel->id }}" {{ old('responsavel', $alteracao->responsavel) == $responsavel->id ? 'selected' : '' }}>
                                        {{ $responsavel->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('responsavel')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="motivo">Motivo da Alteração *</label>
                    <textarea name="motivo" id="motivo" class="form-control @error('motivo') is-invalid @enderror" rows="3" required>{{ old('motivo', $alteracao->motivo) }}</textarea>
                    @error('motivo')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="observacoes">Observações</label>
                    <textarea name="observacoes" id="observacoes" class="form-control @error('observacoes') is-invalid @enderror" rows="3">{{ old('observacoes', $alteracao->observacoes) }}</textarea>
                    @error('observacoes')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Atualizar
                    </button>
                    <a href="{{ route('prl-ae.show', $alteracao) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </form>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
        });
    </script>
@stop 