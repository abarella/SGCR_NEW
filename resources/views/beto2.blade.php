@extends('adminlte::page')
@section('title', 'GEI')
@section('content_header')

@section('content_header')
    <h5 class="m-0 text-gray">BETO 2</h5>
@stop

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <input type="datetime-local" class='form-control' id='v1' name='v1'>

                        <div class="icheck-primary">
                            <input type="checkbox" id="checkbox1">
                            <label for="checkbox1">
                                Checkbox Label
                            </label>
                        </div>
                        <input type='button' onclick='salvar()' value='salvar Swal' /></button>
                        <button type="button" class="btn btn-success swalDefaultSuccess"></button>
                        <button type="button" class="btn btn-danger swalDefaultError"></button>
                        <button type="button" class='btn btn-default' onclick="salvar1('success','mensagem')"  value='teste'>teste</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@push('js')
    <script>
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




