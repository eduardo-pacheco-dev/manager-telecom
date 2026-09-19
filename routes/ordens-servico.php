<?php

use App\Livewire\OrdensServico\Create;
use App\Livewire\OrdensServico\Edit;
use App\Livewire\OrdensServico\Index;
use App\Livewire\OrdensServico\Show;
use App\Models\OrdemServicoAnexo;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('ordens-servico', Index::class)->name('ordens-servico.index');
    Route::livewire('ordens-servico/novo', Create::class)->name('ordens-servico.create');
    Route::livewire('ordens-servico/{ordemServico}', Show::class)->name('ordens-servico.show');
    Route::livewire('ordens-servico/{ordemServico}/editar', Edit::class)->name('ordens-servico.edit');

    Route::get('ordens-servico/anexos/{anexo}/download', function (OrdemServicoAnexo $anexo) {
        abort_unless(Storage::disk('local')->exists($anexo->arquivo), 404);

        return Storage::disk('local')->download($anexo->arquivo, $anexo->nome);
    })->name('ordens-servico.anexos.download');
});
