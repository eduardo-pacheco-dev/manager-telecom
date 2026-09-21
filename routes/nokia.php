<?php

use App\Livewire\Nokia\Create;
use App\Livewire\Nokia\Edit;
use App\Livewire\Nokia\Index;
use App\Livewire\Nokia\RelatorioCreate;
use App\Livewire\Nokia\RelatorioShow;
use App\Livewire\Nokia\Show;
use App\Models\NokiaProjetoAnexo;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('projetos/nokia', Index::class)->name('nokia.index');
    Route::livewire('projetos/nokia/novo', Create::class)->name('nokia.create');
    Route::livewire('projetos/nokia/{projeto}', Show::class)->name('nokia.show');
    Route::livewire('projetos/nokia/{projeto}/editar', Edit::class)->name('nokia.edit');
    Route::livewire('projetos/nokia/{projeto}/relatorios/novo', RelatorioCreate::class)->name('nokia.relatorios.create');
    Route::livewire('projetos/nokia/{projeto}/relatorios/{relatorio}', RelatorioShow::class)->name('nokia.relatorios.show');

    Route::get('projetos/nokia/anexos/{anexo}/download', function (NokiaProjetoAnexo $anexo) {
        abort_unless(Storage::disk('local')->exists($anexo->arquivo), 404);

        return Storage::disk('local')->download($anexo->arquivo, $anexo->nome);
    })->name('nokia.anexos.download');
});
