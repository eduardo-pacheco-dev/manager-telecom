<?php

use App\Livewire\Storage\Index;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('storage', Index::class)->name('storage.index');
});
