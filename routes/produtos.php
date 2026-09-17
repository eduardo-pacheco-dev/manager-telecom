<?php

use App\Livewire\Produtos\Create;
use App\Livewire\Produtos\Edit;
use App\Livewire\Produtos\Index;
use App\Livewire\Produtos\Show;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('produtos', Index::class)->name('produtos.index');
    Route::livewire('produtos/novo', Create::class)->name('produtos.create');
    Route::livewire('produtos/{produto}', Show::class)->name('produtos.show');
    Route::livewire('produtos/{produto}/editar', Edit::class)->name('produtos.edit');
});
