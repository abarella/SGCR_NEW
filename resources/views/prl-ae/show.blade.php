@extends('adminlte::page')

@section('title', 'Detalhes da Alteração de Estoque')

@section('content_header')
    <h1>Detalhes da Alteração de Estoque #{{ $alteracao->id }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Informações da Alteração de Estoque</h3>
            <div class="card-tools">
                <a href="{{ route('prl-ae.edit', $alteracao) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <a href="{{ route('prl-ae.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Informações Básicas</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">ID</th>
                            <td>{{ $alteracao->id }}</td>
                        </tr>
                        <tr>
                            <th>Data de Alteração</th>
                            <td>{{ $alteracao->data_alteracao->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <th>Produto</th>
                            <td>{{ $alteracao->produto }}</td>
                        </tr>
                        <tr>
                            <th>Lote</th>
                            <td>{{ $alteracao->lote }}</td>
                        </tr>
                        <tr>
                            <th>Tipo de Alteração</th>
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
                        </tr>
                        <tr>
                            <th>Status</th>
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
                        </tr>
                        <tr>
                            <th>Responsável</th>
                            <td>{{ $alteracao->responsavel ? ($alteracao->responsavel->name ?? 'Usuário não encontrado') : 'Não definido' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5>Informações de Quantidade</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">Quantidade Atual</th>
                            <td>{{ $alteracao->quantidade_atual }} {{ $alteracao->unidade }}</td>
                        </tr>
                        <tr>
                            <th>Quantidade Nova</th>
                            <td>{{ $alteracao->quantidade_nova }} {{ $alteracao->unidade }}</td>
                        </tr>
                        <tr>
                            <th>Diferença</th>
                            <td>
                                @php
                                    $diferenca = $alteracao->quantidade_nova - $alteracao->quantidade_atual;
                                    $sinal = $diferenca >= 0 ? '+' : '';
                                @endphp
                                <span class="{{ $diferenca >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $sinal }}{{ $diferenca }} {{ $alteracao->unidade }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Unidade</th>
                            <td>{{ $alteracao->unidade }}</td>
                        </tr>
                        <tr>
                            <th>Criado em</th>
                            <td>{{ $alteracao->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Atualizado em</th>
                            <td>{{ $alteracao->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            @if($alteracao->motivo)
                <div class="row mt-3">
                    <div class="col-12">
                        <h5>Motivo da Alteração</h5>
                        <div class="alert alert-info">
                            {{ $alteracao->motivo }}
                        </div>
                    </div>
                </div>
            @endif

            @if($alteracao->observacoes)
                <div class="row mt-3">
                    <div class="col-12">
                        <h5>Observações</h5>
                        <div class="alert alert-warning">
                            {{ $alteracao->observacoes }}
                        </div>
                    </div>
                </div>
            @endif

            @if($alteracao->status == 'aprovado')
                <div class="row mt-3">
                    <div class="col-12">
                        <h5>Informações de Aprovação</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th width="20%">Aprovado por</th>
                                <td>{{ $alteracao->aprovadoPor ? $alteracao->aprovadoPor->name : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Data de Aprovação</th>
                                <td>{{ $alteracao->data_aprovacao ? $alteracao->data_aprovacao->format('d/m/Y H:i') : 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            @endif

            @if($alteracao->status == 'rejeitado')
                <div class="row mt-3">
                    <div class="col-12">
                        <h5>Informações de Rejeição</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th width="20%">Rejeitado por</th>
                                <td>{{ $alteracao->rejeitadoPor ? $alteracao->rejeitadoPor->name : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Data de Rejeição</th>
                                <td>{{ $alteracao->data_rejeicao ? $alteracao->data_rejeicao->format('d/m/Y H:i') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Motivo da Rejeição</th>
                                <td>{{ $alteracao->motivo_rejeicao }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            @endif

            <div class="row mt-3">
                <div class="col-12">
                    <h5>Ações</h5>
                    <div class="btn-group">
                        @if($alteracao->status == 'pendente')
                            <form action="{{ route('prl-ae.aprovar-alteracao', $alteracao) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-success" onclick="return confirm('Aprovar alteração?')">
                                    <i class="fas fa-check"></i> Aprovar Alteração
                                </button>
                            </form>
                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#rejeitarModal">
                                <i class="fas fa-times"></i> Rejeitar Alteração
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Rejeitar Alteração -->
    @if($alteracao->status == 'pendente')
        <div class="modal fade" id="rejeitarModal" tabindex="-1" role="dialog">
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