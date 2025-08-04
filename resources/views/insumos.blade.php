@extends('adminlte::page')

@section('title', 'GEI - Gerenciamento de Emails')

@section('content_header')
<h5 class="m-0 text-gray">Insumos</h5>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">


                    @php
                        $result = App\Http\Controllers\InsumosController::func3();
                        //dd($result);

                        $heads1 = [
                            ['label' => 'Código', 'width' => 10],
                            ['label' => 'Descrição', 'width' => 100],
                            ['label' => 'UN', 'width' => 10],

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
@stop
