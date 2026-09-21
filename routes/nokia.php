<?php

use App\Livewire\Nokia\Create;
use App\Livewire\Nokia\Edit;
use App\Livewire\Nokia\Index;
use App\Livewire\Nokia\Show;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('projetos/nokia', Index::class)->name('nokia.index');
    Route::livewire('projetos/nokia/novo', Create::class)->name('nokia.create');
    Route::livewire('projetos/nokia/{projeto}', Show::class)->name('nokia.show');
    Route::livewire('projetos/nokia/{projeto}/editar', Edit::class)->name('nokia.edit');
});
