@extends('adminlte::page')

@section('title', 'Transferências de Material')

@section('content_header')
    <h1>Transferências de Material</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Lista de Transferências de Material</h3>
            <div class="card-tools">
                <a href="{{ route('trf-tm.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nova Transferência
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
                            <th>Data Transferência</th>
                            <th>Material</th>
                            <th>Quantidade</th>
                            <th>Origem</th>
                            <th>Destino</th>
                            <th>Status</th>
                            <th>Responsável</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($trfTms as $trfTm)
                            <tr>
                                <td>{{ $trfTm->id }}</td>
                                <td>{{ $trfTm->data_transferencia ? $trfTm->data_transferencia->format('d/m/Y') : '-' }}</td>
                                <td>{{ $trfTm->material }}</td>
                                <td>{{ $trfTm->quantidade }} {{ $trfTm->unidade }}</td>
                                <td>{{ $trfTm->origem }}</td>
                                <td>{{ $trfTm->destino }}</td>
                                <td>
                                    @switch($trfTm->status)
                                        @case('pendente')
                                            <span class="badge badge-warning">Pendente</span>
                                            @break
                                        @case('concluida')
                                            <span class="badge badge-success">Concluída</span>
                                            @break
                                        @case('cancelada')
                                            <span class="badge badge-danger">Cancelada</span>
                                            @break
                                    @endswitch
                                </td>
                                <td>{{ $trfTm->responsavel ? ($trfTm->responsavel->name ?? 'Usuário não encontrado') : 'Não definido' }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('trf-tm.show', $trfTm) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('trf-tm.edit', $trfTm) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($trfTm->status == 'pendente')
                                            <form action="{{ route('trf-tm.concluir', $trfTm) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Confirmar conclusão?')">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#cancelarModal{{ $trfTm->id }}">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                        <form action="{{ route('trf-tm.destroy', $trfTm) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Excluir transferência?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal Cancelar Transferência -->
                            @if($trfTm->status == 'pendente')
                                <div class="modal fade" id="cancelarModal{{ $trfTm->id }}" tabindex="-1" role="dialog">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form action="{{ route('trf-tm.cancelar', $trfTm) }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Cancelar Transferência</h5>
                                                    <button type="button" class="close" data-dismiss="modal">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>Motivo do Cancelamento *</label>
                                                        <textarea name="motivo_cancelamento" class="form-control" rows="3" required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-danger">Confirmar Cancelamento</button>
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

            {{ $trfTms->links() }}
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