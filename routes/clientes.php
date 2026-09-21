<?php

use App\Http\Controllers\ClienteTemplateController;
use App\Livewire\Clientes\Configuracoes;
use App\Livewire\Clientes\Create;
use App\Livewire\Clientes\Edit;
use App\Livewire\Clientes\Index;
use App\Livewire\Clientes\Show;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('clientes', Index::class)->name('clientes.index');
    Route::livewire('clientes/novo', Create::class)->name('clientes.create');
    Route::livewire('clientes/configuracoes', Configuracoes::class)->name('clientes.configuracoes');
    Route::livewire('clientes/{cliente}', Show::class)->name('clientes.show');
    Route::livewire('clientes/{cliente}/editar', Edit::class)->name('clientes.edit');

    Route::get('clientes/importar/modelo', ClienteTemplateController::class)->name('clientes.importar.modelo');
});
