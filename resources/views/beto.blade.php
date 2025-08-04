

@extends('adminlte::page')

@section('title', 'GEI')

@section('content_header')
    <h5 class="m-0 text-gray">BETO</h5>
@stop

@section('content')

</div>

    <form method="POST" action="{{ route('beto') }}" class=" xform-data-table align-center" id='form1'>
        @csrf
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                lado 1<br>
                                <input type="text" id="text1" name="text1" class="alert-info shadow" value="{{ $text1 }}" >
                                <input type="submit" class="btn btn-danger btn-sm ">
                                <x-adminlte-button label="Primary" theme="primary" icon="fas fa-key"/>
                                <x-adminlte-button label="Info" theme="info" icon="fas fa-info-circle"/>
                                <x-adminlte-button class="btn-flat" type="submit" label="Salvar" theme="success" icon="fas fa-lg fa-save"/>
                                <x-adminlte-button class="btn-lg" type="reset" label="Reset" theme="outline-danger" icon="fas fa-lg fa-trash"/>
                                <x-adminlte-button class="btn-sm bg-gradient-info" type="button" label="Help" icon="fas fa-lg fa-question"/>
                                <hr>
                                <x-adminlte-input name="iBasic"/>

                                <x-adminlte-select2 name="sel2Basic">
                                    <option>Option 1</option>
                                    <option disabled>Option 2</option>
                                    <option selected>Option 3</option>
                                </x-adminlte-select2>

                                <x-adminlte-select2 name="sel2Vehicle" label="Carros" label-class="text-lightblue"
                                    igroup-size="sm" data-placeholder="Select an option...">
                                    <x-slot name="prependSlot">
                                        <div class="input-group-text bg-gradient-info">
                                            <i class="fas fa-car-side"></i>
                                        </div>
                                    </x-slot>
                                    <option/>
                                    <option>Vehicle 1</option>
                                    <option>Vehicle 2</option>
                                </x-adminlte-select2>
                                @php
                                $config3 = [
                                    "placeholder" => "Select multiple options...",
                                    "allowClear" => true,
                                ];
                                @endphp
                                <x-adminlte-textarea name="taDesc" label="Description" rows=5 label-class="text-lightblue"
                                    igroup-size="sm" placeholder="Insert description...">
                                    <x-slot name="prependSlot">
                                        <div class="input-group-text bg-dark">
                                            <i class="fas fa-lg fa-file-alt text-warning"></i>
                                        </div>
                                    </x-slot>
                                </x-adminlte-textarea>


                                <x-adminlte-select2 id="sel2Category" name="sel2Category[]" label="Categorias"
                                    label-class="text-danger" igroup-size="sm" :config="$config3" multiple>
                                    <x-slot name="prependSlot">
                                        <div class="input-group-text bg-gradient-red">
                                            <i class="fas fa-tag"></i>
                                        </div>
                                    </x-slot>
                                    <x-slot name="appendSlot">
                                        <x-adminlte-button theme="outline-dark" label="Clear" icon="fas fa-lg fa-ban text-danger"/>
                                    </x-slot>
                                    <option>Sports</option>
                                    <option>News</option>
                                    <option>Games</option>
                                    <option>Science</option>
                                    <option>Maths</option>
                                </x-adminlte-select2>

                                @php
                                $config4 = [
                                    "timePicker" => true,
                                    "startDate" => "js:moment().subtract(6, 'days')",
                                    "endDate" => "js:moment()",
                                    "locale" => ["format" => "DD/MM/YYYY HH:mm"],
                                    "language" => "es",
                                ];
                                @endphp
                                <x-adminlte-date-range name="drDisabled" :config="$config4" readonly />



                            </div>
                        </div>
                        <div class="row">
                            <div class="col-8">
                                lado 2&nbsp;<hr>
                                @php
                                    $heads = [
                                        'ID',
                                        'Name',
                                        ['label' => 'Phone', 'width' => 50],
                                        ['label' => 'Actions', 'no-export' => true, 'width' => 5],
                                    ];

                                    $btnEdit = '<button class="btn btn-xs btn-default text-primary mx-1 shadow" title="Edit">
                                                    <i class="fa fa-lg fa-fw fa-pen"></i>
                                                </button>';
                                    $btnDelete = '<button class="btn btn-xs btn-default text-danger mx-1 shadow" title="Delete">
                                                    <i class="fa fa-lg fa-fw fa-trash"></i>
                                                </button>';
                                    $btnDetails = '<button class="btn btn-xs btn-default text-teal mx-1 shadow" title="Details">
                                                    <i class="fa fa-lg fa-fw fa-eye"></i>
                                                </button>';

                                    $config = [
                                        'data' => [
                                            [22, 'John Bender', '+02 (123) 123456789', '<nobr>'.$btnEdit.$btnDelete.$btnDetails.'</nobr>'],
                                            [19, 'Sophia Clemens', '+99 (987) 987654321', '<nobr>'.$btnEdit.$btnDelete.$btnDetails.'</nobr>'],
                                            [3, 'Peter Sousa', '+69 (555) 12367345243', '<nobr>'.$btnEdit.$btnDelete.$btnDetails.'</nobr>'],
                                            [4, 'Peter Sousa', '+69 (555) 12367345243', '<nobr>'.$btnEdit.$btnDelete.$btnDetails.'</nobr>'],
                                            [5, 'Peter Sousa', '+69 (555) 12367345243', '<nobr>'.$btnEdit.$btnDelete.$btnDetails.'</nobr>'],
                                            [6, 'Peter Sousa', '+69 (555) 12367345243', '<nobr>'.$btnEdit.$btnDelete.$btnDetails.'</nobr>'],
                                            [7, 'Peter Sousa', '+69 (555) 12367345243', '<nobr>'.$btnEdit.$btnDelete.$btnDetails.'</nobr>'],
                                            [8, 'Peter Sousa de Maria Machado e Olveira Bragança asdf asdf asdf asdf asdf asdf asdf asdf asfd asdf asdf asdf asdf asdf as fdasdf asfdx', '+69 (555) 12367345243 qwqer qwer qwer qwer qwer qwre qwer q qwerqwre', '<nobr>'.$btnEdit.$btnDelete.$btnDetails.'</nobr>'],
                                        ],
                                        'order' => [[0, 'asc']],
                                        'responsive' => true,
                                        'columns' => [null, null, null, ['orderable' => false]],
                                    ];
                                    //dd($config);
                                    $config['paging'] = true;
                                    $config["lengthMenu"] = [3, 5, 50, 100, 500];
                                    $config['language'] = [ 'url' => 'https://cdn.datatables.net/plug-ins/2.0.7/i18n/pt-BR.json' ];
                                @endphp

                            <x-adminlte-datatable id="table1" :heads="$heads" head-theme="light" :config="$config"
                            striped hoverable bordered compressed compact with-buttons >

                                @foreach($config['data'] as $row)
                                    <tr>
                                        @foreach($row as $cell)
                                            <td>{!! $cell !!}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </x-adminlte-datatable>

                            <hr>
                                @php
                                    //$result = App\Http\Controllers\BetoController::func2();
                                    $result = App\Http\Controllers\BetoController::func3();

                                    $heads1 = [
                                        ['label' => 'A', 'width' => 3],
                                        ['label' => 'B', 'width' => 10],
                                        ['label' => 'C', 'width' => 10],
                                        ['label' => 'D', 'width' => 10],
                                        ['label' => 'E', 'width' => 10],
                                    ];



                                    $config1 = [
                                        'data1' =>$result,
                                        'responsive' => true,
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

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>



    @push('js')
    <script>

$('.form-data-table').on('submit', function(event){
    event.preventDefault();
    form1.target='ifr';
    form1.submit()

    /*
     Swal.fire({
                 position: "top-end",
                 icon: "success",
                 title: "Seu trabalho foi salvo",
                 showConfirmButton: false,
                 timer: 1500
             });
             */
  })



    </script>
    @endpush


@if(Session::get('beto_OK') )
<script type="text/javascript">
    function massge() {
        var Toast = Swal.mixin({
                toast: true,
                position: 'center',
                showConfirmButton: false,
                timer: 3000
            });
            Toast.fire({
                icon: 'success',
                title: 'E-MAIL VALIDADO',
                animation: true,
                timerProgressBar: true,
                text : "O e-mail informado foi validado com sucesso",
			          heading: 'Custom styles',
			          bgColor: '#ff0000',
		            textColor: '#fff',
            })
    }
    window.onload = massge;
   </script>
@endif

@if(Session::has('beto_ERRO') )
<script type="text/javascript">

    function massge() {
        var Toast = Swal.mixin({
                toast: true,
                position: 'center',
                showConfirmButton: false,
                timer: 3000
            });
            Toast.fire({
                icon: 'error',
                title: 'E-MAIL INVÁLIDO',
                animation: true,
                timerProgressBar: true,
                text : "O e-mail informado não esta no formato adequado",
			    heading: 'btn',
			    bgcolor: '#2ecc71',
		        textColor: '#ff0000',
            })
    }
    window.onload = massge;
   </script>
@endif


@stop
