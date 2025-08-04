@extends('adminlte::page')
@section('title', 'SGCR - Produção')
@section('content_header')

@section('content_header')
    <h6 class="m-0 text-gray">content views\admin_user\index.blade.php</h6>
@stop

@section('content')


@php
//$result = App\Http\Controllers\BetoController::func2();
$result = App\Http\Controllers\UserController\adminUserController::retornaUsuariosCadastrados();


$heads1 = [
                                        ['label' => 'Usuário', 'width' => 3],
                                        ['label' => 'Sistema', 'width' => 10],
                                        ['label' => 'Programa', 'width' => 10],
                                        ['label' => 'Programa', 'width' => 10],
                                        ['label' => 'Link', 'width' => 10],
                                    ];



                                    $config1 = [
                                        'data1' =>$result,
                                        'order1' => [[1, 'desc']],
                                        'columns1' => [null, null, null, null],
                                        'stateSave'=>true,
                                        'lengthMenu'=> [[2 ,10, 25, 50, -1],['2 registros','10 registros', '25 registros', '50 registros', 'Todos']],
                                        ];

                                    $config1['paging'] = true;

                                    //$config1["lengthMenu"] = [3, 5, 50, 100, 500];
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
@stop
