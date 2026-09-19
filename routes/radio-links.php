<?php

use App\Livewire\RadioLinks\Create;
use App\Livewire\RadioLinks\Edit;
use App\Livewire\RadioLinks\Index;
use App\Livewire\RadioLinks\Show;
use App\Models\RadioLinkAnexo;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('radio-links', Index::class)->name('radio-links.index');
    Route::livewire('radio-links/novo', Create::class)->name('radio-links.create');
    Route::livewire('radio-links/{radioLink}', Show::class)->name('radio-links.show');
    Route::livewire('radio-links/{radioLink}/editar', Edit::class)->name('radio-links.edit');

    Route::get('radio-links/anexos/{anexo}/download', function (RadioLinkAnexo $anexo) {
        abort_unless(Storage::disk('local')->exists($anexo->arquivo), 404);

        return Storage::disk('local')->download($anexo->arquivo, $anexo->nome);
    })->name('radio-links.anexos.download');
});
