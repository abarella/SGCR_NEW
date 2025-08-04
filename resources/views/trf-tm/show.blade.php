@extends('adminlte::page')

@section('title', 'Detalhes da Transferência de Material')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Detalhes da Transferência de Material</h3>
        <div class="card-tools">
            <a href="{{ route('trf-tm.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
            <a href="{{ route('trf-tm.edit', $trfTm->id) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-edit"></i> Editar
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <th width="40%">Data da Transferência:</th>
                        <td>{{ $trfTm->data_transferencia ? \Carbon\Carbon::parse($trfTm->data_transferencia)->format('d/m/Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Material:</th>
                        <td>{{ $trfTm->material }}</td>
                    </tr>
                    <tr>
                        <th>Quantidade:</th>
                        <td>{{ $trfTm->quantidade }} {{ $trfTm->unidade }}</td>
                    </tr>
                    <tr>
                        <th>Origem:</th>
                        <td>{{ $trfTm->origem }}</td>
                    </tr>
                    <tr>
                        <th>Destino:</th>
                        <td>{{ $trfTm->destino }}</td>
                    </tr>
                    <tr>
                        <th>Responsável:</th>
                        <td>{{ $trfTm->responsavel ? $trfTm->responsavel->name : '-' }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <th width="40%">Status:</th>
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
                                @default
                                    <span class="badge badge-secondary">{{ ucfirst($trfTm->status) }}</span>
                            @endswitch
                        </td>
                    </tr>
                    <tr>
                        <th>Concluído por:</th>
                        <td>{{ $trfTm->concluidoPor ? $trfTm->concluidoPor->name : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Data de Conclusão:</th>
                        <td>{{ $trfTm->data_conclusao ? $trfTm->data_conclusao->format('d/m/Y H:i:s') : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Cancelado por:</th>
                        <td>{{ $trfTm->canceladoPor ? $trfTm->canceladoPor->name : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Data de Cancelamento:</th>
                        <td>{{ $trfTm->data_cancelamento ? $trfTm->data_cancelamento->format('d/m/Y H:i:s') : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Criado em:</th>
                        <td>{{ $trfTm->created_at ? $trfTm->created_at->format('d/m/Y H:i:s') : '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        @if($trfTm->observacoes)
        <div class="row mt-3">
            <div class="col-12">
                <h5>Observações:</h5>
                <div class="alert alert-info">
                    {{ $trfTm->observacoes }}
                </div>
            </div>
        </div>
        @endif

        @if($trfTm->motivo_cancelamento)
        <div class="row mt-3">
            <div class="col-12">
                <h5>Motivo do Cancelamento:</h5>
                <div class="alert alert-danger">
                    {{ $trfTm->motivo_cancelamento }}
                </div>
            </div>
        </div>
        @endif

        @if($trfTm->status == 'pendente')
        <div class="row mt-3">
            <div class="col-12">
                <h5>Ações:</h5>
                <form action="{{ route('trf-tm.concluir', $trfTm->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-success" onclick="return confirm('Confirmar conclusão?')">
                        <i class="fas fa-check"></i> Concluir
                    </button>
                </form>
                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#cancelarModal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
            </div>
        </div>

        <!-- Modal Cancelar Transferência -->
        <div class="modal fade" id="cancelarModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('trf-tm.cancelar', $trfTm->id) }}" method="POST">
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
    </div>
</div>
@endsection 