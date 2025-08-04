@extends('adminlte::page')

@section('title', 'Editar Folha de Produção')

@section('content_header')
    <h1>Editar Folha de Produção #{{ $folha->id }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Editar Folha de Produção</h3>
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

            <form action="{{ route('prl-fp.update', $folha) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="data_producao">Data de Produção *</label>
                            <input type="date" name="data_producao" id="data_producao" class="form-control @error('data_producao') is-invalid @enderror" value="{{ old('data_producao', $folha->data_producao->format('Y-m-d')) }}" required>
                            @error('data_producao')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="produto">Produto *</label>
                            <input type="text" name="produto" id="produto" class="form-control @error('produto') is-invalid @enderror" value="{{ old('produto', $folha->produto) }}" required>
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
                            <input type="text" name="lote" id="lote" class="form-control @error('lote') is-invalid @enderror" value="{{ old('lote', $folha->lote) }}" required>
                            @error('lote')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="quantidade">Quantidade *</label>
                            <input type="number" step="0.01" name="quantidade" id="quantidade" class="form-control @error('quantidade') is-invalid @enderror" value="{{ old('quantidade', $folha->quantidade) }}" required>
                            @error('quantidade')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="unidade">Unidade *</label>
                            <select name="unidade" id="unidade" class="form-control @error('unidade') is-invalid @enderror" required>
                                <option value="">Selecione...</option>
                                <option value="mg" {{ old('unidade', $folha->unidade) == 'mg' ? 'selected' : '' }}>mg</option>
                                <option value="g" {{ old('unidade', $folha->unidade) == 'g' ? 'selected' : '' }}>g</option>
                                <option value="kg" {{ old('unidade', $folha->unidade) == 'kg' ? 'selected' : '' }}>kg</option>
                                <option value="ml" {{ old('unidade', $folha->unidade) == 'ml' ? 'selected' : '' }}>ml</option>
                                <option value="l" {{ old('unidade', $folha->unidade) == 'l' ? 'selected' : '' }}>l</option>
                                <option value="mCi" {{ old('unidade', $folha->unidade) == 'mCi' ? 'selected' : '' }}>mCi</option>
                                <option value="MBq" {{ old('unidade', $folha->unidade) == 'MBq' ? 'selected' : '' }}>MBq</option>
                                <option value="GBq" {{ old('unidade', $folha->unidade) == 'GBq' ? 'selected' : '' }}>GBq</option>
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
                                    <option value="{{ $responsavel->id }}" {{ old('responsavel', $folha->responsavel) == $responsavel->id ? 'selected' : '' }}>
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
                    <label for="observacoes">Observações</label>
                    <textarea name="observacoes" id="observacoes" class="form-control @error('observacoes') is-invalid @enderror" rows="4">{{ old('observacoes', $folha->observacoes) }}</textarea>
                    @error('observacoes')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Atualizar
                    </button>
                    <a href="{{ route('prl-fp.show', $folha) }}" class="btn btn-secondary">
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