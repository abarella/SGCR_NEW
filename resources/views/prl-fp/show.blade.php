@extends('adminlte::page')

@section('title', 'Detalhes da Folha de Produção')

@section('content_header')
    <h1>Detalhes da Folha de Produção #{{ $folha->id }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Informações da Folha de Produção</h3>
            <div class="card-tools">
                <a href="{{ route('prl-fp.edit', $folha) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <a href="{{ route('prl-fp.index') }}" class="btn btn-secondary">
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
                            <td>{{ $folha->id }}</td>
                        </tr>
                        <tr>
                            <th>Data de Produção</th>
                            <td>{{ $folha->data_producao->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <th>Produto</th>
                            <td>{{ $folha->produto }}</td>
                        </tr>
                        <tr>
                            <th>Lote</th>
                            <td>{{ $folha->lote }}</td>
                        </tr>
                        <tr>
                            <th>Quantidade</th>
                            <td>{{ $folha->quantidade }} {{ $folha->unidade }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
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
                        </tr>
                        <tr>
                            <th>Responsável</th>
                            <td>{{ $folha->responsavel ? ($folha->responsavel->name ?? 'Usuário não encontrado') : 'Não definido' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5>Informações de Produção</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">Data de Início</th>
                            <td>{{ $folha->data_inicio ? $folha->data_inicio->format('d/m/Y H:i') : 'Não iniciado' }}</td>
                        </tr>
                        <tr>
                            <th>Data de Fim</th>
                            <td>{{ $folha->data_fim ? $folha->data_fim->format('d/m/Y H:i') : 'Não finalizado' }}</td>
                        </tr>
                        <tr>
                            <th>Temperatura</th>
                            <td>{{ $folha->temperatura ? $folha->temperatura . ' °C' : 'Não registrado' }}</td>
                        </tr>
                        <tr>
                            <th>Umidade</th>
                            <td>{{ $folha->umidade ? $folha->umidade . ' %' : 'Não registrado' }}</td>
                        </tr>
                        <tr>
                            <th>pH</th>
                            <td>{{ $folha->ph ? $folha->ph : 'Não registrado' }}</td>
                        </tr>
                        <tr>
                            <th>Criado em</th>
                            <td>{{ $folha->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Atualizado em</th>
                            <td>{{ $folha->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            @if($folha->observacoes)
                <div class="row mt-3">
                    <div class="col-12">
                        <h5>Observações</h5>
                        <div class="alert alert-info">
                            {{ $folha->observacoes }}
                        </div>
                    </div>
                </div>
            @endif

            @if($folha->condicoes_especiais)
                <div class="row mt-3">
                    <div class="col-12">
                        <h5>Condições Especiais</h5>
                        <div class="alert alert-warning">
                            {{ $folha->condicoes_especiais }}
                        </div>
                    </div>
                </div>
            @endif

            @if($folha->status == 'aprovado')
                <div class="row mt-3">
                    <div class="col-12">
                        <h5>Informações de Aprovação</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th width="20%">Aprovado por</th>
                                <td>{{ $folha->aprovadoPor ? $folha->aprovadoPor->name : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Data de Aprovação</th>
                                <td>{{ $folha->data_aprovacao ? $folha->data_aprovacao->format('d/m/Y H:i') : 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            @endif

            @if($folha->status == 'rejeitado')
                <div class="row mt-3">
                    <div class="col-12">
                        <h5>Informações de Rejeição</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th width="20%">Rejeitado por</th>
                                <td>{{ $folha->rejeitadoPor ? $folha->rejeitadoPor->name : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Data de Rejeição</th>
                                <td>{{ $folha->data_rejeicao ? $folha->data_rejeicao->format('d/m/Y H:i') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Motivo da Rejeição</th>
                                <td>{{ $folha->motivo_rejeicao }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            @endif

            <div class="row mt-3">
                <div class="col-12">
                    <h5>Ações</h5>
                    <div class="btn-group">
                        @if($folha->status == 'pendente')
                            <form action="{{ route('prl-fp.iniciar-producao', $folha) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-success" onclick="return confirm('Iniciar produção?')">
                                    <i class="fas fa-play"></i> Iniciar Produção
                                </button>
                            </form>
                        @endif

                        @if($folha->status == 'em_producao')
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#finalizarModal">
                                <i class="fas fa-stop"></i> Finalizar Produção
                            </button>
                        @endif

                        @if($folha->status == 'concluido')
                            <form action="{{ route('prl-fp.aprovar-producao', $folha) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-success" onclick="return confirm('Aprovar produção?')">
                                    <i class="fas fa-check"></i> Aprovar Produção
                                </button>
                            </form>
                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#rejeitarModal">
                                <i class="fas fa-times"></i> Rejeitar Produção
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Finalizar Produção -->
    @if($folha->status == 'em_producao')
        <div class="modal fade" id="finalizarModal" tabindex="-1" role="dialog">
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
        <div class="modal fade" id="rejeitarModal" tabindex="-1" role="dialog">
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