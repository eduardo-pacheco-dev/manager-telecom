<?php

use App\Livewire\Servicos\Create;
use App\Livewire\Servicos\Edit;
use App\Livewire\Servicos\Index;
use App\Livewire\Servicos\Show;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('servicos', Index::class)->name('servicos.index');
    Route::livewire('servicos/novo', Create::class)->name('servicos.create');
    Route::livewire('servicos/{servico}', Show::class)->name('servicos.show');
    Route::livewire('servicos/{servico}/editar', Edit::class)->name('servicos.edit');
});
