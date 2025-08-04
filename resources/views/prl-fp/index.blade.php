@extends('adminlte::page')

@section('title', 'Folhas de Produção')

@section('content_header')
    <h1>Folhas de Produção</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Lista de Folhas de Produção</h3>
            <div class="card-tools">
                <a href="{{ route('prl-fp.create') }}" class="btn btn-primary">
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
                            <th>Data Produção</th>
                            <th>Produto</th>
                            <th>Lote</th>
                            <th>Quantidade</th>
                            <th>Status</th>
                            <th>Responsável</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($folhas as $folha)
                            <tr>
                                <td>{{ $folha->id }}</td>
                                <td>{{ $folha->data_producao->format('d/m/Y') }}</td>
                                <td>{{ $folha->produto }}</td>
                                <td>{{ $folha->lote }}</td>
                                <td>{{ $folha->quantidade }} {{ $folha->unidade }}</td>
                                <td>
                                    @switch($folha->status)
                                        @case('pendente')
                                            <span class="badge badge-warning">Pendente</span>
                                            @break
                                        @case('em_producao')
                                            <span class="badge badge-info">Em Produção</span>
                                            @break
                                        @case('concluido')
                                            <span class="badge badge-primary">Concluído</span>
                                            @break
                                        @case('aprovado')
                                            <span class="badge badge-success">Aprovado</span>
                                            @break
                                        @case('rejeitado')
                                            <span class="badge badge-danger">Rejeitado</span>
                                            @break
                                    @endswitch
                                </td>
                                <td>{{ $folha->responsavel ? ($folha->responsavel->name ?? 'Usuário não encontrado') : 'Não definido' }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('prl-fp.show', $folha) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('prl-fp.edit', $folha) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($folha->status == 'pendente')
                                            <form action="{{ route('prl-fp.iniciar-producao', $folha) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Iniciar produção?')">
                                                    <i class="fas fa-play"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if($folha->status == 'em_producao')
                                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#finalizarModal{{ $folha->id }}">
                                                <i class="fas fa-stop"></i>
                                            </button>
                                        @endif
                                        @if($folha->status == 'concluido')
                                            <form action="{{ route('prl-fp.aprovar-producao', $folha) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Aprovar produção?')">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#rejeitarModal{{ $folha->id }}">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                        <form action="{{ route('prl-fp.destroy', $folha) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Excluir folha?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal Finalizar Produção -->
                            @if($folha->status == 'em_producao')
                                <div class="modal fade" id="finalizarModal{{ $folha->id }}" tabindex="-1" role="dialog">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form action="{{ route('prl-fp.finalizar-producao', $folha) }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Finalizar Produção</h5>
                                                    <button type="button" class="close" data-dismiss="modal">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>Temperatura (°C)</label>
                                                        <input type="number" step="0.01" name="temperatura" class="form-control">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Umidade (%)</label>
                                                        <input type="number" step="0.01" name="umidade" class="form-control">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>pH</label>
                                                        <input type="number" step="0.01" min="0" max="14" name="ph" class="form-control">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Condições Especiais</label>
                                                        <textarea name="condicoes_especiais" class="form-control" rows="3"></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-primary">Finalizar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Modal Rejeitar Produção -->
                            @if($folha->status == 'concluido')
                                <div class="modal fade" id="rejeitarModal{{ $folha->id }}" tabindex="-1" role="dialog">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form action="{{ route('prl-fp.rejeitar-producao', $folha) }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Rejeitar Produção</h5>
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

            {{ $folhas->links() }}
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