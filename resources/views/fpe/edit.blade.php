@extends('adminlte::page')

@section('title', 'Editar Folha de Prod.-Embalado')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Editar Folha de Prod.-Embalado</h3>
        <div class="card-tools">
            <a href="{{ route('fpe.show', $fpe->id) }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('fpe.update', $fpe->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="data_embalagem">Data de Embalagem</label>
                        <input type="date" class="form-control @error('data_embalagem') is-invalid @enderror" 
                               id="data_embalagem" name="data_embalagem" 
                               value="{{ old('data_embalagem', $fpe->data_embalagem) }}" required>
                        @error('data_embalagem')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="produto">Produto</label>
                        <input type="text" class="form-control @error('produto') is-invalid @enderror" 
                               id="produto" name="produto" value="{{ old('produto', $fpe->produto) }}" required>
                        @error('produto')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="lote">Lote</label>
                        <input type="text" class="form-control @error('lote') is-invalid @enderror" 
                               id="lote" name="lote" value="{{ old('lote', $fpe->lote) }}" required>
                        @error('lote')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="quantidade">Quantidade</label>
                        <input type="number" step="0.01" class="form-control @error('quantidade') is-invalid @enderror" 
                               id="quantidade" name="quantidade" 
                               value="{{ old('quantidade', $fpe->quantidade) }}" required>
                        @error('quantidade')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="unidade">Unidade</label>
                        <select class="form-control @error('unidade') is-invalid @enderror" id="unidade" name="unidade" required>
                            <option value="">Selecione...</option>
                            <option value="kg" {{ old('unidade', $fpe->unidade) == 'kg' ? 'selected' : '' }}>kg</option>
                            <option value="g" {{ old('unidade', $fpe->unidade) == 'g' ? 'selected' : '' }}>g</option>
                            <option value="l" {{ old('unidade', $fpe->unidade) == 'l' ? 'selected' : '' }}>l</option>
                            <option value="ml" {{ old('unidade', $fpe->unidade) == 'ml' ? 'selected' : '' }}>ml</option>
                            <option value="un" {{ old('unidade', $fpe->unidade) == 'un' ? 'selected' : '' }}>un</option>
                            <option value="caixa" {{ old('unidade', $fpe->unidade) == 'caixa' ? 'selected' : '' }}>caixa</option>
                        </select>
                        @error('unidade')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="responsavel">Responsável</label>
                        <select class="form-control @error('responsavel') is-invalid @enderror" id="responsavel" name="responsavel">
                            <option value="">Selecione...</option>
                            @foreach(\App\Models\User::all() as $user)
                                <option value="{{ $user->id }}" 
                                    {{ old('responsavel', $fpe->responsavel) == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
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
                <textarea class="form-control @error('observacoes') is-invalid @enderror" 
                          id="observacoes" name="observacoes" rows="3">{{ old('observacoes', $fpe->observacoes) }}</textarea>
                @error('observacoes')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Atualizar
                </button>
                <a href="{{ route('fpe.show', $fpe->id) }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection 