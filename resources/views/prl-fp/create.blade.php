@extends('adminlte::page')

@section('title', 'Nova Folha de Produção')

@section('content_header')
    <h1>Nova Folha de Produção</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Criar Nova Folha de Produção</h3>
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

            <form action="{{ route('prl-fp.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="data_producao">Data de Produção *</label>
                            <input type="date" name="data_producao" id="data_producao" class="form-control @error('data_producao') is-invalid @enderror" value="{{ old('data_producao', date('Y-m-d')) }}" required>
                            @error('data_producao')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="produto">Produto *</label>
                            <input type="text" name="produto" id="produto" class="form-control @error('produto') is-invalid @enderror" value="{{ old('produto') }}" required>
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
                            <input type="text" name="lote" id="lote" class="form-control @error('lote') is-invalid @enderror" value="{{ old('lote') }}" required>
                            @error('lote')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="quantidade">Quantidade *</label>
                            <input type="number" step="0.01" name="quantidade" id="quantidade" class="form-control @error('quantidade') is-invalid @enderror" value="{{ old('quantidade') }}" required>
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
                                <option value="mg" {{ old('unidade') == 'mg' ? 'selected' : '' }}>mg</option>
                                <option value="g" {{ old('unidade') == 'g' ? 'selected' : '' }}>g</option>
                                <option value="kg" {{ old('unidade') == 'kg' ? 'selected' : '' }}>kg</option>
                                <option value="ml" {{ old('unidade') == 'ml' ? 'selected' : '' }}>ml</option>
                                <option value="l" {{ old('unidade') == 'l' ? 'selected' : '' }}>l</option>
                                <option value="mCi" {{ old('unidade') == 'mCi' ? 'selected' : '' }}>mCi</option>
                                <option value="MBq" {{ old('unidade') == 'MBq' ? 'selected' : '' }}>MBq</option>
                                <option value="GBq" {{ old('unidade') == 'GBq' ? 'selected' : '' }}>GBq</option>
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
                                    <option value="{{ $responsavel->id }}" {{ old('responsavel') == $responsavel->id ? 'selected' : '' }}>
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
                    <textarea name="observacoes" id="observacoes" class="form-control @error('observacoes') is-invalid @enderror" rows="4">{{ old('observacoes') }}</textarea>
                    @error('observacoes')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar
                    </button>
                    <a href="{{ route('prl-fp.index') }}" class="btn btn-secondary">
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