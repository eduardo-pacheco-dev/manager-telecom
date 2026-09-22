<?php

use App\Livewire\Tim\Create;
use App\Livewire\Tim\Edit;
use App\Livewire\Tim\Index;
use App\Livewire\Tim\RelatorioCreate;
use App\Livewire\Tim\RelatorioShow;
use App\Livewire\Tim\Show;
use App\Models\TimProjetoAnexo;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('projetos/tim', Index::class)->name('tim.index');
    Route::livewire('projetos/tim/novo', Create::class)->name('tim.create');
    Route::livewire('projetos/tim/{projeto}', Show::class)->name('tim.show');
    Route::livewire('projetos/tim/{projeto}/editar', Edit::class)->name('tim.edit');
    Route::livewire('projetos/tim/{projeto}/relatorios/novo', RelatorioCreate::class)->name('tim.relatorios.create');
    Route::livewire('projetos/tim/{projeto}/relatorios/{relatorio}', RelatorioShow::class)->name('tim.relatorios.show');

    Route::get('projetos/tim/anexos/{anexo}/download', function (TimProjetoAnexo $anexo) {
        abort_unless(Storage::disk('local')->exists($anexo->arquivo), 404);

        return Storage::disk('local')->download($anexo->arquivo, $anexo->nome);
    })->name('tim.anexos.download');
});