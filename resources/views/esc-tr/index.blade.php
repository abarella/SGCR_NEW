@extends('adminlte::page')

@section('title', 'Escala de Tarefas')

@section('content_header')
    <h5 class="m-0">Escala de Tarefas</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"></h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalNovaTarefa">
                            <i class="fas fa-plus"></i> Nova Tarefa
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table id="tabelaTarefas" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Ações</th>
                                <th>ID</th>
                                <th>Nome da Tarefa</th>
                                <th>Data de Criação</th>
                                
                                
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tarefas as $tarefa)
                            <tr>
                                <td>
                                    <button type="button" class="btn btn-sm btn-info" 
                                            onclick="editarTarefa({{ $tarefa->ID }}, '{{ $tarefa->Nome }}')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" 
                                            onclick="excluirTarefaModal({{ $tarefa->ID }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    
                                </td>
                                <td>{{ $tarefa->ID }}</td>
                                <td>{{ $tarefa->Nome }}</td>
                                <td>{{ \Carbon\Carbon::parse($tarefa->datatualizacao)->format('d/m/Y H:i') }}</td>
                           
                                
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nova Tarefa -->
<div class="modal fade" id="modalNovaTarefa" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nova Tarefa</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formNovaTarefa">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="txtNomeTarefa">Nome da Tarefa:</label>
                        <input type="text" class="form-control" id="txtNomeTarefa" name="txtNomeTarefa" required>
                    </div>
                    <div class="form-group">
                        <label for="txtSenha">Senha:</label>
                        <input type="password" class="form-control" id="txtSenha" name="txtSenha" maxlength="6" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Tarefa -->
<div class="modal fade" id="modalEditarTarefa" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Tarefa</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formEditarTarefa">
                <div class="modal-body">
                    <input type="hidden" id="m_nr_ID" name="nr_ID">
                    <div class="form-group">
                        <label for="m_txtNome">Nome da Tarefa:</label>
                        <input type="text" class="form-control" id="m_txtNome" name="m_txtNome" required>
                    </div>
                    <div class="form-group">
                        <label for="m_txtSenha">Senha:</label>
                        <input type="password" class="form-control" id="m_txtSenha" name="m_txtSenha" maxlength="6" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Atualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Excluir Tarefa -->
<div class="modal fade" id="modalExcluirTarefa" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formExcluirTarefa">
                <div class="modal-body">
                    <input type="hidden" id="excluir_id" name="id">
                    <p>Tem certeza que deseja excluir esta tarefa?</p>
                    <div class="form-group">
                        <label for="excluir_txtSenha">Senha:</label>
                        <input type="password" class="form-control" id="excluir_txtSenha" name="txtSenha" maxlength="6" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@stop

@section('js')
<script src="{{ asset('js/escalatarefas/app.js') }}"></script>
@stop
