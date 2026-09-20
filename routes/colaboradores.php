<?php

use App\Http\Controllers\ColaboradorTemplateController;
use App\Livewire\Colaboradores\Create;
use App\Livewire\Colaboradores\Edit;
use App\Livewire\Colaboradores\Index;
use App\Livewire\Colaboradores\Show;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('colaboradores', Index::class)->name('colaboradores.index');
    Route::livewire('colaboradores/novo', Create::class)->name('colaboradores.create');
    Route::livewire('colaboradores/{colaborador}', Show::class)->name('colaboradores.show');
    Route::livewire('colaboradores/{colaborador}/editar', Edit::class)->name('colaboradores.edit');

    Route::get('colaboradores/importar/modelo', ColaboradorTemplateController::class)->name('colaboradores.importar.modelo');
});
