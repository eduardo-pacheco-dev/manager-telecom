<?php

use App\Livewire\Estacoes\Create;
use App\Livewire\Estacoes\Edit;
use App\Livewire\Estacoes\Index;
use App\Livewire\Estacoes\Show;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('estacoes', Index::class)->name('estacoes.index');
    Route::livewire('estacoes/novo', Create::class)->name('estacoes.create');
    Route::livewire('estacoes/{estacao}', Show::class)->name('estacoes.show');
    Route::livewire('estacoes/{estacao}/editar', Edit::class)->name('estacoes.edit');
});
