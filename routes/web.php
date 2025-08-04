<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PspAdController;
use App\Http\Controllers\PspPsController;


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
        Route::get('/test', [PspPsController::class, 'test'])->name('psp-ps.test');
        Route::get('/{numero}', [PspPsController::class, 'show'])->name('psp-ps.show');
        Route::get('/{numero}/edit', [PspPsController::class, 'edit'])->name('psp-ps.edit');
        Route::put('/{numero}', [PspPsController::class, 'update'])->name('psp-ps.update');
        Route::get('/{numero}/doc', [PspPsController::class, 'editDoc'])->name('psp-ps.edit-doc');
        Route::put('/{numero}/doc', [PspPsController::class, 'updateDoc'])->name('psp-ps.update-doc');
    });
});



Route::prefix('psp-ad')->name('psp-ad.')->group(function() {
    Route::get('/', [PspAdController::class, 'index'])->name('index');
    Route::post('/atualizar', [PspAdController::class, 'atualizar'])->name('atualizar');
});


Route::get('/telescope', [\Laravel\Telescope\Http\Controllers\HomeController::class, 'index']);


