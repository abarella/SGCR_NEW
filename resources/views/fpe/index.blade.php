@extends('adminlte::page')

@section('title', 'Folhas de Produção Embalado')

@section('content_header')
    <h1>Folhas de Produção Embalado</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Lista de Folhas de Produção Embalado</h3>
            <div class="card-tools">
                <a href="{{ route('fpe.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nova Folha
                </a>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    {{ session('success') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Data Embalagem</th>
                            <th>Produto</th>
                            <th>Lote</th>
                            <th>Quantidade</th>
                            <th>Status</th>
                            <th>Responsável</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($fpes as $fpe)
                            <tr>
                                <td>{{ $fpe->id }}</td>
                                <td>{{ $fpe->data_embalagem ? $fpe->data_embalagem->format('d/m/Y') : '-' }}</td>
                                <td>{{ $fpe->produto }}</td>
                                <td>{{ $fpe->lote }}</td>
                                <td>{{ $fpe->quantidade }} {{ $fpe->unidade }}</td>
                                <td>
                                    @switch($fpe->status)
                                        @case('pendente')
                                            <span class="badge badge-warning">Pendente</span>
                                            @break
                                        @case('embalado')
                                            <span class="badge badge-info">Embalado</span>
                                            @break
                                        @case('aprovado')
                                            <span class="badge badge-success">Aprovado</span>
                                            @break
                                        @case('rejeitado')
                                            <span class="badge badge-danger">Rejeitado</span>
                                            @break
                                    @endswitch
                                </td>
                                <td>{{ $fpe->responsavel ? ($fpe->responsavel->name ?? 'Usuário não encontrado') : 'Não definido' }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('fpe.show', $fpe) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('fpe.edit', $fpe) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($fpe->status == 'pendente')
                                            <form action="{{ route('fpe.embalar', $fpe) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-primary" onclick="return confirm('Marcar como Embalado?')">
                                                    <i class="fas fa-box"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if($fpe->status == 'embalado')
                                            <form action="{{ route('fpe.approve', $fpe) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Aprovar folha?')">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#rejeitarModal{{ $fpe->id }}">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                        <form action="{{ route('fpe.destroy', $fpe) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Excluir folha?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal Rejeitar Folha -->
                            @if($fpe->status == 'embalado')
                                <div class="modal fade" id="rejeitarModal{{ $fpe->id }}" tabindex="-1" role="dialog">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form action="{{ route('fpe.reject', $fpe) }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Rejeitar Folha</h5>
                                                    <button type="button" class="close" data-dismiss="modal">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>Motivo da Rejeição *</label>
                                                        <textarea name="motivo_rejeicao" class="form-control" rows="3" required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-danger">Rejeitar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $fpes->links() }}
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        $(document).ready(function() {
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
        });
    </script>
@stop 