@extends('adminlte::page')

@section('title', 'Teste PSP-RM')

@section('content_header')
    <h5 class="m-0">Teste PSP-RM</h5>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <h3>Teste de Layout</h3>
        <p>Esta é uma página de teste para verificar se o layout está funcionando corretamente.</p>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Campo de Teste:</label>
                    <input type="text" class="form-control" placeholder="Digite algo">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Select de Teste:</label>
                    <select class="form-control">
                        <option>Opção 1</option>
                        <option>Opção 2</option>
                        <option>Opção 3</option>
                    </select>
                </div>
            </div>
        </div>
        
        <button class="btn btn-primary">Botão de Teste</button>
    </div>
</div>
@stop
