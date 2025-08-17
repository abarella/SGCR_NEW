/**
 * JavaScript para o módulo de Escala de Tarefas
 * Funcionalidades específicas da página
 */

$(document).ready(function() {
    console.log('Document ready');
    
    // Teste do modal
    console.log('Modal excluir existe?', $('#modalExcluirTarefa').length);
    console.log('Modal excluir HTML:', $('#modalExcluirTarefa').html());
    
    // Inicializar DataTable
    inicializarDataTable();
    
    // Configurar formulários
    configurarFormularios();
    
    // Configurar eventos
    configurarEventos();
});

/**
 * Inicializa o DataTable usando AdminLTE
 */
function inicializarDataTable() {
    $('#tabelaTarefas').DataTable({
        "language": {
            "url": "/js/datatables-pt-br.json"
        },
        "responsive": true,
        "autoWidth": false,
        "order": [[0, "desc"]],
        "pageLength": 10,
        "lengthMenu": [[-1,10, 25, 50, 100], ["Todos",10, 25, 50, 100]],
        "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
           '<"row"<"col-sm-12"tr>>' +
           '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        "buttons": []
    });
}

/**
 * Configura os formulários da página
 */
function configurarFormularios() {
    // Form Nova Tarefa
    $('#formNovaTarefa').on('submit', function(e) {
        e.preventDefault();
        salvarNovaTarefa();
    });

    // Form Editar Tarefa
    $('#formEditarTarefa').on('submit', function(e) {
        e.preventDefault();
        atualizarTarefa();
    });

    // Form Excluir Tarefa
    $('#formExcluirTarefa').on('submit', function(e) {
        e.preventDefault();
        excluirTarefa();
    });
}

/**
 * Configura os eventos da página
 */
function configurarEventos() {
    // Limpar formulários ao fechar modais
    $('.modal').on('hidden.bs.modal', function() {
        $(this).find('form')[0].reset();
        $(this).find('.is-invalid').removeClass('is-invalid');
        $(this).find('.invalid-feedback').remove();
    });

    // Validação em tempo real
    $('input[type="password"]').on('input', function() {
        if ($(this).val().length > 6) {
            $(this).val($(this).val().substring(0, 6));
        }
    });
}

/**
 * Salva uma nova tarefa
 */
function salvarNovaTarefa() {
    const formData = $('#formNovaTarefa').serialize();
    
    $.ajax({
        url: '/esc-tr/store',
        method: 'POST',
        data: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function() {
            $('#formNovaTarefa button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Salvando...');
        },
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Sucesso!',
                    text: response.message
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: response.message
                });
            }
        },
        error: function(xhr) {
            let message = 'Erro interno do servidor';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            Swal.fire({
                icon: 'error',
                title: 'Erro!',
                text: message
            });
        },
        complete: function() {
            $('#formNovaTarefa button[type="submit"]').prop('disabled', false).html('Salvar');
        }
    });
}

/**
 * Atualiza uma tarefa existente
 */
function atualizarTarefa() {
    const formData = $('#formEditarTarefa').serialize();
    
    $.ajax({
        url: '/esc-tr/update',
        method: 'POST',
        data: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function() {
            $('#formEditarTarefa button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Atualizando...');
        },
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Sucesso!',
                    text: response.message
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: response.message
                });
            }
        },
        error: function(xhr) {
            let message = 'Erro interno do servidor';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            Swal.fire({
                icon: 'error',
                title: 'Erro!',
                text: message
            });
        },
        complete: function() {
            $('#formEditarTarefa button[type="submit"]').prop('disabled', false).html('Atualizar');
        }
    });
}

/**
 * Exclui uma tarefa
 */
function excluirTarefa() {
    const formData = $('#formExcluirTarefa').serialize();
    
    $.ajax({
        url: '/esc-tr/destroy',
        method: 'POST',
        data: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function() {
            $('#formExcluirTarefa button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Excluindo...');
        },
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Sucesso!',
                    text: response.message
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: response.message
                });
            }
        },
        error: function(xhr) {
            let message = 'Erro interno do servidor';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            Swal.fire({
                icon: 'error',
                title: 'Erro!',
                text: message
            });
        },
        complete: function() {
            $('#formExcluirTarefa button[type="submit"]').prop('disabled', false).html('Excluir');
        }
    });
}

/**
 * Abre modal para editar tarefa
 */
function editarTarefa(id, nome) {
    $('#m_nr_ID').val(id);
    $('#m_txtNome').val(nome);
    $('#modalEditarTarefa').modal('show');
}

/**
 * Abre modal para excluir tarefa
 */
function excluirTarefaModal(id) {
    console.log('excluirTarefaModal chamada com ID:', id);
    $('#excluir_id').val(id);
    console.log('Valor do campo excluir_id:', $('#excluir_id').val());
    $('#modalExcluirTarefa').modal('show');
    console.log('Modal deve estar visível agora');
}

/**
 * Recarrega a tabela de tarefas
 */
function recarregarTabela() {
    $('#tabelaTarefas').DataTable().ajax.reload();
}

/**
 * Exibe mensagem de erro
 */
function exibirErro(mensagem) {
    Swal.fire({
        icon: 'error',
        title: 'Erro!',
        text: mensagem
    });
}

/**
 * Exibe mensagem de sucesso
 */
function exibirSucesso(mensagem) {
    Swal.fire({
        icon: 'success',
        title: 'Sucesso!',
        text: mensagem
    });
}

/**
 * Função de teste para o modal
 */
function testeModal() {
    console.log('Teste modal chamado');
    console.log('Modal excluir existe?', $('#modalExcluirTarefa').length);
    console.log('Modal excluir HTML:', $('#modalExcluirTarefa').html());
    
    // Tenta abrir o modal
    try {
        $('#modalExcluirTarefa').modal('show');
        console.log('Modal aberto com sucesso');
    } catch (error) {
        console.error('Erro ao abrir modal:', error);
    }
}
