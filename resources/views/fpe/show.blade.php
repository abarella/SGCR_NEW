@extends('adminlte::page')

@section('title', 'Detalhes da Folha de Prod.-Embalado')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Detalhes da Folha de Prod.-Embalado</h3>
                    <div class="card-tools">
                        <a href="{{ route('fpe.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                        <a href="{{ route('fpe.edit', $fpe->id) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th>Data de Embalagem:</th>
                                    <td>{{ $fpe->data_embalagem ? \Carbon\Carbon::parse($fpe->data_embalagem)->format('d/m/Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Produto:</th>
                                    <td>{{ $fpe->produto }}</td>
                                </tr>
                                <tr>
                                    <th>Lote:</th>
                                    <td>{{ $fpe->lote }}</td>
                                </tr>
                                <tr>
                                    <th>Quantidade:</th>
                                    <td>{{ $fpe->quantidade }} {{ $fpe->unidade }}</td>
                                </tr>
                                <tr>
                                    <th>Unidade:</th>
                                    <td>{{ $fpe->unidade }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">

                                <tr>
                                    <th>Responsável:</th>
                                    <td>{{ $fpe->responsavel ? $fpe->responsavel->name : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        @switch($fpe->status)
                                            @case('pendente')
                                                <span class="badge badge-warning">Pendente</span>
                                                @break
                                            @case('aprovado')
                                                <span class="badge badge-success">Aprovado</span>
                                                @break
                                            @case('rejeitado')
                                                <span class="badge badge-danger">Rejeitado</span>
                                                @break
                                            @default
                                                <span class="badge badge-secondary">{{ ucfirst($fpe->status) }}</span>
                                        @endswitch
                                    </td>
                                </tr>
                                <tr>
                                    <th>Criado em:</th>
                                    <td>{{ $fpe->created_at ? $fpe->created_at->format('d/m/Y H:i:s') : '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($fpe->observacoes)
                    <div class="row mt-3">
                        <div class="col-12">
                            <h5>Observações:</h5>
                            <div class="alert alert-info">
                                {{ $fpe->observacoes }}
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($fpe->status == 'pendente')
                    <div class="row mt-3">
                        <div class="col-12">
                            <h5>Ações:</h5>
                            <form action="{{ route('fpe.approve', $fpe->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success" onclick="return confirm('Confirmar aprovação?')">
                                    <i class="fas fa-check"></i> Aprovar
                                </button>
                            </form>
                            <form action="{{ route('fpe.reject', $fpe->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Confirmar rejeição?')">
                                    <i class="fas fa-times"></i> Rejeitar
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 