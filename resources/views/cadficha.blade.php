@extends('adminlte::page')

@section('title', 'GEI - Gerenciamento de Emails')

@section('content_header')
<h5 class="m-0 text-gray">Cadastro</h5>
<meta name="base-url" content="{{ url('/') }}">
<meta name="csrf-token" content="{{ csrf_token() }}">

@stop

@push('js')
<script>
    function pesq_prod_insu(){
        //const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        //const baseUrl = document.querySelector('meta[name="base-url"]').getAttribute('content');

        _prod = document.getElementById('idcat').value
        _insu = document.getElementById('idinsumo').value

        if (_prod==""){return;}

                fetch('/api/func5/' + _prod)
                .then(response => response.json())
                .then(data => {
                    console.log(data); // Manipule a resposta aqui
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

                        tr.addEventListener('click', () => {
                            // Coloque aqui a ação que você deseja executar ao clicar na linha
                            //alert(item.Cod_Item_Comp)
                            //document.getElementById('idinsumo').value = item.Cod_Item_Comp
                            //console.log('Linha clicada:', item);
                            //console.log('Item clicado:', item.Cod_Item_Comp);
                            //console.log('combo clicado:', document.getElementById('idinsumo').value);
                            document.getElementById('qtde').value = format(item.Qtd_Comp)
                            document.getElementById('perda').value = format(item.Perda)
                            $('#idinsumo').val(item.Cod_Item_Comp).trigger('change');
                            document.querySelectorAll('#minhaTabela tbody tr').forEach(row => {
                                row.classList.remove('bg-primary');
                            });

                            // Adiciona a classe 'selected-row' à linha clicada
                            tr.classList.add('bg-primary');
                            edita(item.R0210_oid)
                        });


                        tbody.appendChild(tr);
                    });
                })
                .catch(error => {
                    console.error('Erro ao chamar a função:', error);
                });
    }


        function format(valor){
            _valor = parseFloat(valor).toFixed(3)
            //_valor = new Intl.NumberFormat("pt-BR").format(valor)

            return(_valor)
        }

        function edita(idR0210){
            prod = document.getElementById('idcat').value
            insu = document.getElementById('idinsumo').value

            hd_prod.value = prod
            hd_insu.value = insu
            hd_codi.value = idR0210
            document.getElementById('salvar').value = 'Atualizar';
            document.getElementById('cancelar').style.display = 'inline';
        }

        function cancelaEDT(){
            document.getElementById('salvar').value = 'Gravar';
            document.getElementById('cancelar').style.display = 'none';
            hd_prod.value = ''
            hd_insu.value = ''
            hd_codi.value = ''

        }

        $(document).ready(function(){
            $('#idcat').val("{{ old('idcat') }}").trigger('change');
            //$('#idinsumo').val("{{ old('idinsumo') }}").trigger('change');
            qtde.value = "{{ old('qtde') }}"
            perda.value = "{{ old('perda') }}"
        });


</script>
@endpush

@section('content')
<form method="POST" action="{{route('cadficha')}}" class="form-data-table" id="form1">
    @csrf
    <input type='hidden' id="hd_prod" name="hd_prod" value="{{ old('hd_prod') }}" />
    <input type='hidden' id="hd_insu" name="hd_insu" value="{{ old('hd_insu') }}" />
    <input type='hidden' id="hd_codi" name="hd_codi" value="{{ old('hd_codi') }}" />
    <input type='hidden' id="hd_usua" name="hd_usua" value="{{ old('hd_usua') }}" />

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @php
                        $result  = App\Http\Controllers\CatProdController::func4();
                        $result1 = App\Http\Controllers\InsumosController::func4();
                        $usuario = Auth::user()->username;
                        echo "<script>hd_usua.value = '".$usuario ."'</script>"

                    @endphp
                    <div class="row">
                        <div class="col-6">
                            @php
                            $config = [
                                "placeholder" => "Selecione a opção...",
                                "allowClear" => true,
                            ];
                            @endphp
                            <x-adminlte-select2 name="idcat" label="Produto" id="idcat" igroup-size="md" :config="$config" onchange="pesq_prod_insu()" >
                                <x-slot name="prependSlot">
                                    <div class="input-group-text text-primary">
                                        <i class="fas fa-tag"></i>
                                    </div>
                                </x-slot>
                                <option value="">Selecione a opção</option>
                                @foreach($result as $row1)
                                        <option value="{!! $row1['Prod_Codigo'] !!}"> {{$row1['Prod_Descricao']}} - {!! $row1['Prod_Codigo'] !!} </option>
                                @endforeach
                            </x-adminlte-select2>



                        </div>
                        <div class="col-6">

                            @php
                            $config1 = [
                                "placeholder" => "Selecione a opção...",
                                "allowClear" => true,
                            ];
                            @endphp
                            <x-adminlte-select2 name="idinsumo" label="Insumo" id="idinsumo" igroup-size="md" :config="$config1"  xonchange="pesq_prod_insu()">
                                <x-slot name="prependSlot">
                                    <div class="input-group-text text-primary">
                                        <i class="fas fa-tag"></i>
                                    </div>
                                </x-slot>
                                <option value="">Selecione a opção</option>
                                @foreach($result1 as $row2)
                                <option value="{!! $row2['codigo'] !!}">{{$row2['descricao']}} - {!! $row2['codigo'] !!} </option>
                                @endforeach
                            </x-adminlte-select2>

                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-2">
                            <label for="qtde">Qtde</label><br>
                            <input type="number" class="form-control" id="qtde" name="qtde" step="0.001" placeholder="0,000" />
                        </div>
                        <div class="col-2">
                            <label for="perda">% Perda</label><br>
                            <input type="number" class="form-control" id="perda" name="perda"  step="0.001" placeholder="0,000" />
                        </div>

                        <div class="col-1">
                            <label for="botao">&nbsp;</label><br>
                            <input type="submit" class="form-control btn btn-primary" id="salvar" name="salvar" value="Gravar"  />
                        </div>
                        <div class="col-1">
                            <label for="botao">&nbsp;</label><br>
                            <input type="button" class="form-control btn btn-danger" id="cancelar" name="cancelar" value="Cancelar" style="display: none" onclick="cancelaEDT()" />
                        </div>


                    </div>

                    <br>

                    <div class="row">
                        <div class="col-12">
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
            </div>
        </div>
    </div>
</form>
@stop
