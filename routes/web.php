<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PspAdController;
use App\Http\Controllers\DefinicaoSerie\DefinicaoSerieController;
use App\Http\Controllers\PspPsController;
use App\Http\Controllers\PspRmController;
use App\Http\Controllers\PspPcController;




Route::get('/', function () {
    return view('auth.login');
});


Auth::routes();
Route::view('/', 'auth.login');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::get('/bxp', [App\Http\Controllers\bxpController::class, 'index'])->name('bxp.index');

// Rotas PSP-PS
Route::middleware(['auth'])->group(function () {
    Route::prefix('psp-ps')->group(function () {
        Route::get('/', [PspPsController::class, 'index'])->name('psp-ps.index');
        Route::get('/lista', [PspPsController::class, 'lista'])->name('psp-ps.lista');
        Route::get('/status', [PspPsController::class, 'status'])->name('psp-ps.status');
        //Route::get('/test', [PspPsController::class, 'test'])->name('psp-ps.test');
        Route::get('/{numero}', [PspPsController::class, 'show'])->name('psp-ps.show');
        Route::get('/{numero}/edit', [PspPsController::class, 'edit'])->name('psp-ps.edit');
        Route::put('/{numero}', [PspPsController::class, 'update'])->name('psp-ps.update');
        Route::get('/{numero}/doc', [PspPsController::class, 'editDoc'])->name('psp-ps.edit-doc');
        Route::put('/{numero}/doc', [PspPsController::class, 'updateDoc'])->name('psp-ps.update-doc');
    });
});

// Rotas PSP-RM (R.D. & M.M.)
Route::middleware(['auth'])->group(function () {
    Route::prefix('psp-rm')->group(function () {
        Route::get('/', [PspRmController::class, 'index'])->name('psp-rm.index');
        Route::get('/test', [PspRmController::class, 'test'])->name('psp-rm.test');
        Route::get('/test-database', [PspRmController::class, 'testDatabase'])->name('psp-rm.test-database');
        Route::get('/listar-produtos', [PspRmController::class, 'listarProdutos'])->name('psp-rm.listar-produtos');
        Route::post('/atualizar-producoes', [PspRmController::class, 'atualizarProducoes'])->name('psp-rm.atualizar-producoes');
        Route::get('/abrir-calibracao', [PspRmController::class, 'abrirCalibracao'])->name('psp-rm.abrir-calibracao');
        Route::post('/atualizar-calibracao', [PspRmController::class, 'atualizarCalibracao'])->name('psp-rm.atualizar-calibracao');
        
        
    });
});

// Rotas PSP-PC (Pastas Não Concluídas)
Route::middleware(['auth'])->group(function () {
    Route::prefix('psp-pc')->group(function () {
        Route::get('/', [PspPcController::class, 'index'])->name('psp-pc.index');
        Route::get('/testar-conexao', [PspPcController::class, 'testarConexao'])->name('psp-pc.testar-conexao');
        Route::get('/testar-procedure', [PspPcController::class, 'testarProcedure'])->name('psp-pc.testar-procedure');
        Route::get('/lista', [PspPcController::class, 'lista'])->name('psp-pc.lista');
        Route::get('/produtos', [PspPcController::class, 'produtos'])->name('psp-pc.produtos');
        Route::get('/documentacao', [PspPcController::class, 'documentacao'])->name('psp-pc.documentacao');
        Route::get('/ocorrencias', [PspPcController::class, 'ocorrencias'])->name('psp-pc.ocorrencias');
        Route::get('/localizar', [PspPcController::class, 'localizar'])->name('psp-pc.localizar');
        
        // Rota de teste para verificar se o JavaScript funciona
        Route::get('/lista-teste', function() {
            return response()->json([
                'success' => true,
                'data' => [
                    (object)[
                        'pst_numero' => '12345',
                        'pst_produto510' => 'PRODUTO-TESTE',
                        'Lote' => 'LOTE-TESTE',
                        'pst_ano_lote' => '2024',
                        'pst_ano' => '2024',
                        'pst_registro' => '01/01/2024',
                        'pst_previsaoproducao' => '01/02/2024',
                        'pst_previsaocontrole' => '01/03/2024',
                        'nome_comercial' => 'PRODUTO DE TESTE',
                        'producao_revisadopor' => 'TESTE USUARIO',
                        'controle_revisadopor' => 'TESTE USUARIO 2',
                        'status' => 'TESTE STATUS'
                    ]
                ],
                'total' => 1,
                'pagina' => 1,
                'porPagina' => 50,
                'totalPaginas' => 1
            ]);
        })->name('psp-pc.lista-teste');
        
        // Rotas para modais refatorados
        Route::get('/usuarios-revisores', [PspPcController::class, 'usuariosRevisores'])->name('psp-pc.usuarios-revisores');
        Route::get('/status-pasta', [PspPcController::class, 'statusPasta'])->name('psp-pc.status-pasta');
        Route::get('/status-producao', [PspPcController::class, 'statusProducao'])->name('psp-pc.status-producao');
        Route::get('/documentacao-dados', [PspPcController::class, 'documentacaoDados'])->name('psp-pc.documentacao-dados');
        Route::post('/documentacao-salvar', [PspPcController::class, 'documentacaoSalvar'])->name('psp-pc.documentacao-salvar');
        Route::get('/tipos-ocorrencia', [PspPcController::class, 'tiposOcorrencia'])->name('psp-pc.tipos-ocorrencia');
        Route::post('/ppst-lista4', [PspPcController::class, 'ppstLista4'])->name('psp-pc.ppst-lista4');
        Route::get('/usuarios-responsaveis', [PspPcController::class, 'usuariosResponsaveis'])->name('psp-pc.usuarios-responsaveis');
        Route::get('/ocorrencias-lista', [PspPcController::class, 'ocorrenciasLista'])->name('psp-pc.ocorrencias-lista');
        Route::post('/ocorrencia-salvar', [PspPcController::class, 'ocorrenciaSalvar'])->name('psp-pc.ocorrencia-salvar');
        Route::get('/setores', [PspPcController::class, 'setores'])->name('psp-pc.setores');
        Route::get('/localizacoes-historico', [PspPcController::class, 'localizacoesHistorico'])->name('psp-pc.localizacoes-historico');
        Route::post('/localizacao-salvar', [PspPcController::class, 'localizacaoSalvar'])->name('psp-pc.localizacao-salvar');
        Route::post('/executar-procedure', [PspPcController::class, 'executarProcedure'])->name('psp-pc.executar-procedure');
    });
});


Route::prefix('psp-ad')->name('psp-ad.')->group(function() {
    Route::get('/', [PspAdController::class, 'index'])->name('index');
    Route::post('/atualizar', [PspAdController::class, 'atualizar'])->name('atualizar');
});


// Rotas para Definição de Série
Route::prefix('dfv-ds')->name('dfv-ds.')->group(function() {
    Route::get('/', [DefinicaoSerieController::class, 'index'])->name('index');
    Route::post('/carregar-lotes', [DefinicaoSerieController::class, 'carregarLotes'])->name('carregar-lotes');
    Route::post('/pesquisar-lista-serie', [DefinicaoSerieController::class, 'pesquisarListaSerie'])->name('pesquisar-lista-serie');
    Route::post('/definir-serie', [DefinicaoSerieController::class, 'definirSerie'])->name('definir-serie');
    Route::post('/definir-multiplas-series', [DefinicaoSerieController::class, 'definirMultiplasSeries'])->name('definir-multiplas-series');
    Route::get('/intervalo', [DefinicaoSerieController::class, 'intervalo'])->name('intervalo');
    Route::post('/definir-serie-intervalo', [DefinicaoSerieController::class, 'definirSerieIntervalo'])->name('definir-serie-intervalo');
    Route::get('/intervalo-lote', [DefinicaoSerieController::class, 'intervaloLote'])->name('intervalo-lote');
    Route::post('/definir-serie-intervalo-lote', [DefinicaoSerieController::class, 'definirSerieIntervaloLote'])->name('definir-serie-intervalo-lote');
    Route::get('/definicao-serie/buscar-numero', [DefinicaoSerieController::class, 'buscarNumero']);
    Route::post('/buscar-serie', [DefinicaoSerieController::class, 'buscarSerie'])->name('buscar-serie');
});


// Route::get('/telescope', [\Laravel\Telescope\Http\Controllers\HomeController::class, 'index']); // Desabilitado

// Rotas para Escala de Tarefas
Route::prefix('esc-tr')->name('escalatarefas.')->middleware(['auth'])->group(function() {
            Route::get('/', [App\Http\Controllers\EscTrController::class, 'index'])->name('index');
            Route::post('/store', [App\Http\Controllers\EscTrController::class, 'store'])->name('store');
            Route::post('/update', [App\Http\Controllers\EscTrController::class, 'update'])->name('update');
            Route::post('/destroy', [App\Http\Controllers\EscTrController::class, 'destroy'])->name('destroy');
            Route::get('/data', [App\Http\Controllers\EscTrController::class, 'getData'])->name('data');
});

// Rotas para Escala Semanal
Route::prefix('esc-ct')->name('esc-ct.')->middleware(['auth'])->group(function() {
    Route::get('/', [App\Http\Controllers\EscCtController::class, 'index'])->name('index');
    Route::post('/store', [App\Http\Controllers\EscCtController::class, 'store'])->name('store');
    Route::post('/update', [App\Http\Controllers\EscCtController::class, 'update'])->name('update');
    Route::post('/destroy', [App\Http\Controllers\EscCtController::class, 'destroy'])->name('destroy');
    Route::post('/duplicar', [App\Http\Controllers\EscCtController::class, 'duplicar'])->name('duplicar');
    Route::post('/usuarios-associados', [App\Http\Controllers\EscCtController::class, 'getUsuariosAssociados'])->name('usuarios-associados');
    Route::get('/data', [App\Http\Controllers\EscCtController::class, 'getData'])->name('data');
});

// Rota de teste para depuração
//require __DIR__ . '/test.php';

