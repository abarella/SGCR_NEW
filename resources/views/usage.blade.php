@extends('adminlte::page')

@section('title', 'GEI - Usage')

@section('content_header')
    <h5 class="m-0">Usage</h5>
    <meta name="base-url" content="{{ url('/') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                Como utilizar o sistema<br>
                1) Adicione o Emissor<br>
                2) Adicione o Grupo onde estarão as pessoas que receberão os e-mails<br>
                3) Adicione o Assunto<br>
                4) Adicione a Assinatura<br>
                <hr>
                dentro das procedures que enviam e-mail deve ser trocadas por "exec portal_corporativo.dbo.sp_gei_email_insert"
                passando os parâmetros:<br>
                1) Id do Emissor<br>
                2) Id do Grupo<br>
                3) Id do Assunto<br>
                4) Id da Assinatura<br>
                5) Texto do corpo do email<br>
                <hr>
                Configuração:<br>
                No SQL SERVER deve ser criado os PROFILES com o nome do emissor-grupo eg.: SGDS-FABRICA<br>
                <hr>
                Schedule:<br>
                Deverá ser schedulado a procedure portal_corporativo.dbo.sp_gei_send_email <br>
                Todos os dias e a cada 5 minutos<br>

            </div>
        </div>
    </div>
@stop
