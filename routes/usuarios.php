<?php

use App\Livewire\Usuarios\Create;
use App\Livewire\Usuarios\Edit;
use App\Livewire\Usuarios\Index;
use App\Livewire\Usuarios\Show;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('usuarios', Index::class)->name('usuarios.index');
    Route::livewire('usuarios/novo', Create::class)->name('usuarios.create');
    Route::livewire('usuarios/{usuario}', Show::class)->name('usuarios.show');
    Route::livewire('usuarios/{usuario}/editar', Edit::class)->name('usuarios.edit');
});
