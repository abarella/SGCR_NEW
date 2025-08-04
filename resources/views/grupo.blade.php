@extends('adminlte::page')

@section('title', 'GEI - Grupo')

@section('content_header')
    <h5 class="m-0">Grupo</h5>
    <meta name="base-url" content="{{ url('/') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@stop

<style>
    #modalMin .modal-footer {
      display: none;
    }

    #modalMinAdd .modal-footer {
      display: none;
    }


</style>

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="POST"  action="{{ route('grupo.index') }}"  class="form-data-table align-center" id='form1'>
                    @csrf
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <x-adminlte-button icon="fa fa-plus" data-toggle="modal"  data-target="#modalMinAdd" class="btn btn-default text-primary mx-1 shadow" label="Adicionar" title="Adicionar"/>
                                        </div>
                                    </div>


                                    <div class="row">
                                        <div class="col-12">
                                            @php
                                                $result = App\Http\Controllers\GrupoController::listaGrupo();
                                                $result1 = App\Http\Controllers\EmissorController::listaEmissor();

                                               // dd($result1);

                                                $heads1 = [
                                                    ['label' => '', 'width' => '15'],
                                                    ['label' => 'ID', 'width' => 10],
                                                    ['label' => 'EMISSOR', 'width' => 10],
                                                    ['label' => 'DESCRIÇÃO', 'width' => 10],
                                                    ['label' => 'EMAILS', 'width' => 999],
                                                    ['label' => 'CC', 'width' => 999],
                                                    ['label' => 'BCC', 'width' => 999],
                                                ];

                                                $config1 = [
                                                    'data1' =>$result,
                                                    'responsive' => true,
                                                    'order1' => [[1, 'desc']],
                                                    'columns1' => [null, null],
                                                    'stateSave'=>true,
                                                    'lengthMenu'=> [[5 ,10, 25, 50, -1],['5 registros','10 registros', '25 registros', '50 registros', 'Todos']],
                                                    ];

                                                $config1['paging'] = true;

                                                $config1['language'] = [ 'url' => 'https://cdn.datatables.net/plug-ins/2.0.7/i18n/pt-BR.json' ];

                                            @endphp
                                            <x-adminlte-datatable id="table2" :heads="$heads1" head-theme="light" :config="$config1" striped hoverable bordered compact compressed  with-buttons>
                                                @foreach($config1['data1'] as $row1)

                                                <tr>
                                                    <td>
                                                        <x-adminlte-button data-toggle="modal" data-target="#modalMin" class="btn btn-default text-primary mx-1 shadow" title="Edit"   icon="fas fa-pen"    data-id="{{ $row1['id'] }}"  data-descricao="{{ $row1['descricao'] }}" data-emissor="{{ $row1['Emissor'] }}" data-emails="{{ $row1['emails'] }}" data-cc="{{ $row1['cc'] }}" data-bcc="{{ $row1['bcc'] }}" ></x-adminlte-button>
                                                        <x-adminlte-button data-toggle="modal"  class="btn btn-default text-danger  mx-1 shadow  btn-delete" title="Delete" icon="fas fa-trash"  data-id="{{ $row1['id'] }}"></x-adminlte-button>

                                                    </td>

                                                    @foreach($row1 as $cell1)
                                                        <td>{!! $cell1 !!}</td>
                                                    @endforeach

                                                </tr>
                                                @endforeach
                                            </x-adminlte-datatable>



                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

@push('js')


<script>
    $(document).ready(function() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const baseUrl = document.querySelector('meta[name="base-url"]').getAttribute('content');


        $('#modalMin').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Botão que disparou o modal
            var id = button.data('id'); // Extraindo informações dos atributos data-*
            var emissor = button.data('emissor');
            var descricao = button.data('descricao');
            var emails = button.data('emails');
            var cc = button.data('cc');
            var bcc = button.data('bcc');

            // Atualizando o conteúdo do modal.
            var modal = $(this);
            modal.find('#id').val(id);
            modal.find('#emissor').val(emissor);
            modal.find('#descricao').val(descricao);
            modal.find('#emails').val(emails);
            modal.find('#cc').val(cc);
            modal.find('#bcc').val(bcc);

            // Atualizando a ação do formulário com o ID correto
            var formAction = "{{ route('grupo.update', ':grupo') }}";
            formAction = formAction.replace(':grupo', id);
            modal.find('#form2').attr('action', formAction);


        $('#modalMinAdd').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Botão que disparou o modal
            var descricao = button.data('descricao');
            var emails = button.data('emails');
            var cc = button.data('cc');
            var bcc = button.data('bcc');

            // Atualizando o conteúdo do modal.
            var modal = $(this);
            modal.find('#descricao').val(descricao);
            modal.find('#emails').val(emails);
            modal.find('#cc').val(cc);
            modal.find('#bcc').val(bcc);
            // Atualizando a ação do formulário com o ID correto
            var formAction = "{{ route('emissor.store', ':emissor') }}";
            formAction = formAction.replace(':emissor', id);
            modal.find('#form3').attr('action', formAction);
        })




    });


    //Executa o Update do Status da Solicitação
    $('#form2').on('submit', function(e) {
            //console.log('Formulário enviado'); // Adicione isso
            e.preventDefault();
            //const solicitacaoId = $(this).attr('data-id');
            const solicitacaoId = $('#id').val();
            const form = $(this)[0];
            const formData = new FormData(form);
            $.ajax({
                type: "POST",
                url: `${baseUrl}/grupo/${solicitacaoId}`,
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-HTTP-Method-Override': 'PUT'
                },
                success: function(response) {
                    $('#modalMin').modal('hide');
                    location.reload();
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        if (errors && Object.keys(errors).length > 0) {
                            let errorMessage = '';
                            Object.keys(errors).forEach(fieldName => {
                                errorMessage += errors[fieldName][0] + '\n';
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro ao analizar solicitação!',
                                text: 'Campo(s) obrigatório(s) não foi(foram) preenchido(s)!',
                                showConfirmButton: true,
                            });
                        }
                    } else {
                        showMessagesAndClearSession();
                    }
                }
            });
        });
    });


    //Executa o Insert do Status da Solicitação
    $('#form3').on('submit', function(e) {
    e.preventDefault();
    var form = $(this);
    var url = form.attr('action');

    $.ajax({
        type: "POST",
        url: url,
        data: form.serialize(),
        success: function(response) {
            $('#modalMinAdd').modal('hide');
            location.reload();
        },
        error: function(response) {
            // Exibe as mensagens recuperadas da sessão
            showMessagesAndClearSession();
        }
    });
});


// Metodo delete de Emissor com confirmação através do sweetalert2
$(document).on('click', '.btn-delete', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const baseUrl = document.querySelector('meta[name="base-url"]').getAttribute('content');

    const grupoId = this.getAttribute('data-id');

Swal.fire({
    title: 'Tem certeza que deseja deletar o Emissor #' + grupoId + '?',
    text: "Você não poderá reverter isso!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Sim, deletar',
    cancelButtonText: 'Cancelar'
}).then((result) => {
    if (result.isConfirmed) {
        fetch( `${baseUrl}/grupo/${grupoId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => {
            if (response.ok) {
                response.json().then(data => {
                    Swal.fire({
                        title:  'Grupo Deletado com Sucesso!',
                        text: data.success_del_eqpto,
                        icon: 'success',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK',
                    }).then(() => {
                        location.reload();
                    });
                });
            } else {
                response.json().then(data => {
                    Swal.fire({
                        title:  'Erro ao Deletar o Grupo',
                        text: data.error_del_eqpto,
                        icon: 'warning',
                        showConfirmButton: false,
                        showCancelButton: false,
                    });
                });
            }
        })
        .catch(error => {
            Swal.fire({
                title:  'Erro ao Deletar o Grupo',
                icon: 'warning',
                showConfirmButton: false,
                showCancelButton: false,
            });
        });
    }

});

})





    </script>
    @endpush





<x-adminlte-modal id="modalMin" title="Grupo" size="lg" theme="teal" icon="fas fa-star" v-centered static-backdrop scrollable>
    <form method="POST"  action="{{ route('grupo.update', ['grupo' => 0]) }}"  class="form-data-table align-center" id='form2' name='form2' >
        @csrf
        @method('PUT')

        <div style="height:420px;">
            &nbsp;<label for="id" class="form-label">Nº</label>
            <input type='text' id="id"        name="id"        class='form-control' readonly />

            &nbsp;<label for="emissor" class="form-label">Emissor</label>
            <input type='text' id="emissor"        name="emissor"        class='form-control' readonly />

            &nbsp;<label for="descricao" class="form-label">Descrição</label>
            <input type='text' id="descricao" name="descricao" class='form-control'/>

            &nbsp;<label for="emails" class="form-label">E-mails</label>
            <input type='text' id="emails" name="emails" class='form-control'/>

            &nbsp;<label for="cc" class="form-label">CC</label>
            <input type='text' id="cc" name="cc" class='form-control'/>

            &nbsp;<label for="bcc" class="form-label">BCC</label>
            <input type='text' id="bcc" name="bcc" class='form-control'/>


        </div>
        <br>
        <div class="card-body" name="footerSlot">
            <x-adminlte-button name="salvarGrupo" id="salvarGrupo" class="btn-flat" type="submit" label="Salvar" theme="success" icon="fas fa-lg fa-save"></x-adminlte-button>
            <x-adminlte-button theme="danger" label="Cancelar" data-dismiss="modal"/>
        </div>
    </form>
</x-adminlte-modal>



<x-adminlte-modal id="modalMinAdd" title="Grupo" size="lg" theme="teal" icon="fas fa-star" v-centered static-backdrop scrollable>
    <form method="POST"  action="{{ route('grupo.store') }}"  class="form-data-table align-center" id='form3' name='form3' >
        @csrf
        <div style="height:420px;">
            <x-adminlte-select name="id_emissor" label="Emissor" id="id_emissor"  igroup-size="md">
                <x-slot name="prependSlot">
                    <div class="input-group-text text-primary">
                        <i class="fas fa-tag"></i>
                    </div>
                </x-slot>>
                @foreach($result1 as $emissor)
                    <option value="{{ $emissor['id'] }}">{{ $emissor['descricao']}}</option>
                @endforeach
            </x-adminlte-select>

            &nbsp;<label for="descrição" class="form-label">Descrição</label>
            <input type='text' id="descricao" name="descricao" class='form-control'/>

            &nbsp;<label for="emails" class="form-label">E-mails</label>
            <input type='text' id="emails" name="emails" class='form-control'/>

            &nbsp;<label for="cc" class="form-label">CC</label>
            <input type='text' id="cc" name="cc" class='form-control'/>

            &nbsp;<label for="bcc" class="form-label">BCC</label>
            <input type='text' id="bcc" name="bcc" class='form-control'/>


        </div>
        <br>
        <div class="card-body" name="footerSlot">
            <x-adminlte-button name="addGrupo" id="addGrupo" class="btn-flat" type="submit" label="Salvar" theme="success" icon="fas fa-lg fa-save"></x-adminlte-button>
            <x-adminlte-button theme="danger" label="Cancelar" data-dismiss="modal"/>
        </div>
    </form>
</x-adminlte-modal>



@stop



