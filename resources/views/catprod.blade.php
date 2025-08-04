@extends('adminlte::page')
@section('title', 'GEI - Gerenciamento de Emails')
@section('content_header')

@section('content_header')
    <h5 class="m-0 text-gray">Catálogo de Produtos</h5>
    <meta name="base-url" content="{{ url('/') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
                <div class="row">
                    <div class="col-12">

                        @php
                        $result = App\Http\Controllers\CatProdController::func3();
                        //dd($result);

                        $heads1 = [
                            ['label' => '', 'width' => 1],
                            ['label' => 'Código', 'width' => 10],
                            ['label' => 'Produto', 'width' => 100],

                        ];



                        $config1 = [
                            'data1' =>$result,
                            'responsive' => true,
                            'order1' => [[1, 'desc']],
                            //'columns1' => [null, null, null, null],
                            'stateSave'=>true,
                            'lengthMenu'=> [[10, 25, 50, -1],['10 registros', '25 registros', '50 registros', 'Todos']],
                            ];

                        $config1['paging'] = true;
                        $config1['language'] = [ 'url' => 'https://cdn.datatables.net/plug-ins/2.0.7/i18n/pt-BR.json' ];


                    @endphp

                        <x-adminlte-datatable id="table2" :heads="$heads1" head-theme="light" :config="$config1"
                                striped hoverable bordered compressed compact with-buttons>

                                @foreach($config1['data1'] as $row1)
                                <tr>
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
@stop



<x-adminlte-modal id="modalMin" title="Insumos" size="xl" theme="teal" icon="fas fa-star" v-centered static-backdrop scrollable>
    <form method="POST"    class="form-data-table align-center" id='form2' name='form2' >
        @csrf
        @method('PUT')

        <div style="height:500px; width:900px">
            <div class='row'>
                <div class="col-2">
                    &nbsp;<label for="id" class="form-label">Código</label>
                    <input type='text' id="id"        name="id"        class='form-control' readonly />
                </div>
                <div class="col-10">
                    &nbsp;<label for="descr" class="form-label">Descrição</label>
                    <input type='text' id="descr" name="descr" class='form-control' readonly/>
                </div>
            </div>
            <br>
            <div class='row' >
                <div class="table-responsive-sm" style="height: 400px; overflow-y:auto; width: 100%">
                    <table id="minhaTabela" class="table table-sm" >
                        <thead>
                            <tr>
                                <th>Cód</th>
                                <th>Descrição</th>
                                <th>Qtde.</th>
                                <th>% Perda</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Linhas da tabela serão preenchidas aqui -->
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
        <div class="card-body" name="footerSlot">
            <x-adminlte-button theme="danger" label="Fechar" data-dismiss="modal"/>
        </div>
    </form>
</x-adminlte-modal>




@push('js')
    <script>

        $(document).ready(function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const baseUrl = document.querySelector('meta[name="base-url"]').getAttribute('content');

            $('#modalMin').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget); // Botão que disparou o modal
                var id = button.data('id'); // Extraindo informações dos atributos data-*
                var descr = button.data('descr');

                fetch('/api/func5/' + id)
                .then(response => response.json())
                .then(data => {
                    //console.log(data); // Manipule a resposta aqui
                    const tbody = document.querySelector('#minhaTabela tbody');
                    tbody.innerHTML = '';
                    data.forEach(item => {

                        const tr = document.createElement('tr');
                        tr.innerHTML =
                        '<td>' +
                        item.Cod_Item_Comp +
                        '</td>' +
                        '<td>'+
                        item.Desc_Item_Comp +
                        '</td>'+
                        '<td align="right">'+
                        format(item.Qtd_Comp) +
                        '</td>'+
                        '<td  align="right">'+
                        format(item.Perda) +
                        '</td>';

                        tbody.appendChild(tr);
                    });

                })
                .catch(error => {
                    console.error('Erro ao chamar a função:', error);
                });


                //alert(result);

                // Atualizando o conteúdo do modal.
                var modal = $(this);
                modal.find('#id').val(id);
                modal.find('#descr').val(descr);

            })

            //document.addEventListener('DOMContentLoaded', function() {
            //    console.log('teste2a');
            //    document.getElementById('buscarInsumo').addEventListener('click', function(event) {
            //        event.preventDefault();
            //        console.log('teste2');
            //        //const descricaoMaterial = document.getElementById('p0039_Descricao_Material').value;
            //    })
            //})

        });

        function format(valor){
            _valor = parseFloat(valor).toFixed(3)
            //_valor = new Intl.NumberFormat("pt-BR").format(valor)

            return(_valor)
        }

        function salvar() {
            Swal.fire({
                position: "top-end",
                icon: "success",
                title: "Seu trabalho foi salvo",
                showConfirmButton: false,
                timer: 1500
            });
        }



        function salvar1(tipo, mensagem) {
            var Toast = Swal.mixin({
                toast: true,
                position: 'center',
                showConfirmButton: false,
                timer: 3000
            });
            Toast.fire({
                icon: tipo,
                animation: true,
                title: mensagem + '  ' + tipo,
                text : "corpo da mensagem",
                    heading: 'Custom styles',
                    bgColor: '#2ecc71',
                    textColor: '#fff',
            })
        }


    </script>
@endpush
