<?php

use App\Livewire\Dashboard\Index as Dashboard;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('dashboard', Dashboard::class)->name('dashboard');
});

require __DIR__.'/settings.php';
require __DIR__.'/colaboradores.php';
require __DIR__.'/clientes.php';
require __DIR__.'/produtos.php';
require __DIR__.'/servicos.php';
require __DIR__.'/estacoes.php';
require __DIR__.'/radio-links.php';
require __DIR__.'/ordens-servico.php';
require __DIR__.'/nokia.php';
require __DIR__.'/usuarios.php';
require __DIR__.'/storage.php';
