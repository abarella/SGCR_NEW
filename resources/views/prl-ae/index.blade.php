@extends('adminlte::page')

@section('title', 'Alterações de Estoque')

@section('content_header')
    <h1>Alterações de Estoque</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Lista de Alterações de Estoque</h3>
            <div class="card-tools">
                <a href="{{ route('prl-ae.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nova Alteração
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
                            <th>Data Alteração</th>
                            <th>Produto</th>
                            <th>Lote</th>
                            <th>Qtd. Atual</th>
                            <th>Qtd. Nova</th>
                            <th>Tipo</th>
                            <th>Status</th>
                            <th>Responsável</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($alteracoes as $alteracao)
                            <tr>
                                <td>{{ $alteracao->id }}</td>
                                <td>{{ $alteracao->data_alteracao->format('d/m/Y') }}</td>
                                <td>{{ $alteracao->produto }}</td>
                                <td>{{ $alteracao->lote }}</td>
                                <td>{{ $alteracao->quantidade_atual }} {{ $alteracao->unidade }}</td>
                                <td>{{ $alteracao->quantidade_nova }} {{ $alteracao->unidade }}</td>
                                <td>
                                    @switch($alteracao->tipo_alteracao)
                                        @case('entrada')
                                            <span class="badge badge-success">Entrada</span>
                                            @break
                                        @case('saida')
                                            <span class="badge badge-danger">Saída</span>
                                            @break
                                        @case('ajuste')
                                            <span class="badge badge-warning">Ajuste</span>
                                            @break
                                    @endswitch
                                </td>
                                <td>
                                    @switch($alteracao->status)
                                        @case('pendente')
                                            <span class="badge badge-warning">Pendente</span>
                                            @break
                                        @case('aprovado')
                                            <span class="badge badge-success">Aprovado</span>
                                            @break
                                        @case('rejeitado')
                                            <span class="badge badge-danger">Rejeitado</span>
                                            @break
                                    @endswitch
                                </td>
                                <td>{{ $alteracao->responsavel ? ($alteracao->responsavel->name ?? 'Usuário não encontrado') : 'Não definido' }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('prl-ae.show', $alteracao) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('prl-ae.edit', $alteracao) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($alteracao->status == 'pendente')
                                            <form action="{{ route('prl-ae.aprovar-alteracao', $alteracao) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Aprovar alteração?')">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#rejeitarModal{{ $alteracao->id }}">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                        <form action="{{ route('prl-ae.destroy', $alteracao) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Excluir alteração?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal Rejeitar Alteração -->
                            @if($alteracao->status == 'pendente')
                                <div class="modal fade" id="rejeitarModal{{ $alteracao->id }}" tabindex="-1" role="dialog">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form action="{{ route('prl-ae.rejeitar-alteracao', $alteracao) }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Rejeitar Alteração</h5>
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

            {{ $alteracoes->links() }}
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