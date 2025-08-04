@extends('adminlte::page')

@section('title', 'Editar Transferência de Material')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Editar Transferência de Material</h3>
        <div class="card-tools">
            <a href="{{ route('trf-tm.show', $trfTm->id) }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('trf-tm.update', $trfTm->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="data_transferencia">Data da Transferência</label>
                        <input type="date" class="form-control @error('data_transferencia') is-invalid @enderror" 
                               id="data_transferencia" name="data_transferencia" 
                               value="{{ old('data_transferencia', $trfTm->data_transferencia ? $trfTm->data_transferencia->format('Y-m-d') : '') }}" required>
                        @error('data_transferencia')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="material">Material</label>
                        <input type="text" class="form-control @error('material') is-invalid @enderror" 
                               id="material" name="material" value="{{ old('material', $trfTm->material) }}" required>
                        @error('material')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="quantidade">Quantidade</label>
                        <input type="number" step="0.01" class="form-control @error('quantidade') is-invalid @enderror" 
                               id="quantidade" name="quantidade" 
                               value="{{ old('quantidade', $trfTm->quantidade) }}" required>
                        @error('quantidade')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="unidade">Unidade</label>
                        <select class="form-control @error('unidade') is-invalid @enderror" id="unidade" name="unidade" required>
                            <option value="">Selecione...</option>
                            <option value="kg" {{ old('unidade', $trfTm->unidade) == 'kg' ? 'selected' : '' }}>kg</option>
                            <option value="g" {{ old('unidade', $trfTm->unidade) == 'g' ? 'selected' : '' }}>g</option>
                            <option value="l" {{ old('unidade', $trfTm->unidade) == 'l' ? 'selected' : '' }}>l</option>
                            <option value="ml" {{ old('unidade', $trfTm->unidade) == 'ml' ? 'selected' : '' }}>ml</option>
                            <option value="un" {{ old('unidade', $trfTm->unidade) == 'un' ? 'selected' : '' }}>un</option>
                            <option value="caixa" {{ old('unidade', $trfTm->unidade) == 'caixa' ? 'selected' : '' }}>caixa</option>
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
                        <label for="origem">Origem</label>
                        <input type="text" class="form-control @error('origem') is-invalid @enderror" 
                               id="origem" name="origem" value="{{ old('origem', $trfTm->origem) }}" required>
                        @error('origem')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="destino">Destino</label>
                        <input type="text" class="form-control @error('destino') is-invalid @enderror" 
                               id="destino" name="destino" value="{{ old('destino', $trfTm->destino) }}" required>
                        @error('destino')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="responsavel">Responsável</label>
                        <select class="form-control @error('responsavel') is-invalid @enderror" id="responsavel" name="responsavel">
                            <option value="">Selecione...</option>
                            @foreach(\App\Models\User::all() as $user)
                                <option value="{{ $user->id }}" 
                                    {{ old('responsavel', $trfTm->responsavel) == $user->id ? 'selected' : '' }}>
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
                          id="observacoes" name="observacoes" rows="3">{{ old('observacoes', $trfTm->observacoes) }}</textarea>
                @error('observacoes')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Atualizar
                </button>
                <a href="{{ route('trf-tm.show', $trfTm->id) }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection 