<?php

use App\Livewire\Estacoes\Create;
use App\Livewire\Estacoes\Edit;
use App\Livewire\Estacoes\Index;
use App\Livewire\Estacoes\Show;
use App\Models\EstacaoAnexo;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('estacoes', Index::class)->name('estacoes.index');
    Route::livewire('estacoes/novo', Create::class)->name('estacoes.create');
    Route::livewire('estacoes/{estacao}', Show::class)->name('estacoes.show');
    Route::livewire('estacoes/{estacao}/editar', Edit::class)->name('estacoes.edit');

    Route::get('estacoes/anexos/{anexo}/download', function (EstacaoAnexo $anexo) {
        abort_unless(Storage::disk('local')->exists($anexo->arquivo), 404);

        return Storage::disk('local')->download($anexo->arquivo, $anexo->nome);
    })->name('estacoes.anexos.download');
});
