<?php

use App\Jobs\ProcessClienteImport;
use App\Livewire\Clientes\Index;
use App\Models\Cliente;
use App\Models\ClienteImport;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer as XlsxWriter;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

function criarArquivoClientes(string $nome, array $linhas): void
{
    Storage::disk('local')->makeDirectory('imports/cliente');

    $cabecalhos = ['Nome', 'Email', 'CNPJ', 'Telefone', 'Segmento', 'Cidade', 'Estado', 'Status'];

    $writer = new XlsxWriter;
    $writer->openToFile(Storage::disk('local')->path('imports/cliente/'.$nome));
    $writer->addRow(Row::fromValues($cabecalhos));

    foreach ($linhas as $linha) {
        $writer->addRow(Row::fromValues($linha));
    }

    $writer->close();
}

test('clientes index shows import button', function () {
    Livewire::test(Index::class)
        ->assertSee('Importar');
});

test('cliente import modal opens', function () {
    Livewire::test(Index::class)
        ->call('abrirImportacao')
        ->assertSet('showImportModal', true)
        ->assertSee('Importar clientes')
        ->assertSee('Arraste o arquivo aqui')
        ->assertSee('CNPJ');
});

test('cliente import template can be downloaded', function () {
    $this->get(route('clientes.importar.modelo'))
        ->assertOk()
        ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
        ->assertDownload('modelo_clientes.xlsx');
});

test('cliente import requires a file', function () {
    Livewire::test(Index::class)
        ->call('iniciarImportacao')
        ->assertHasErrors(['import_arquivo']);
});

test('cliente import dispatches job and stores file', function () {
    Queue::fake();
    Storage::fake('local');

    Livewire::test(Index::class)
        ->set('import_arquivo', UploadedFile::fake()->create('clientes.xlsx', 100, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'))
        ->call('iniciarImportacao')
        ->assertHasNoErrors()
        ->assertSet('showImportModal', false);

    $this->assertDatabaseHas('cliente_imports', [
        'user_id' => $this->user->id,
        'nome_original' => 'clientes.xlsx',
        'status' => 'pendente',
    ]);

    $import = ClienteImport::first();

    expect($import)->not->toBeNull();
    Storage::disk('local')->assertExists($import->arquivo);

    Queue::assertPushed(ProcessClienteImport::class, function (ProcessClienteImport $job) use ($import) {
        return $job->importId === $import->id;
    });
});

test('cliente import processes valid rows', function () {
    Storage::fake('local');

    criarArquivoClientes('clientes.xlsx', [
        ['Empresa Alfa', 'alfa@teste.com', '12345678000195', '(11) 4002-8922', 'Corporativo', 'São Paulo', 'SP', 'Ativo'],
        ['Empresa Beta', 'beta@teste.com', '98765432000198', '(21) 3000-1122', 'Varejo', 'Rio de Janeiro', 'RJ', 'Inativo'],
    ]);

    $import = ClienteImport::create([
        'user_id' => $this->user->id,
        'arquivo' => 'imports/cliente/clientes.xlsx',
        'nome_original' => 'clientes.xlsx',
        'status' => 'pendente',
    ]);

    (new ProcessClienteImport($import->id))->handle();

    $this->assertDatabaseHas('clientes', [
        'nome' => 'Empresa Alfa',
        'email' => 'alfa@teste.com',
        'documento' => '12.345.678/0001-95',
        'segmento' => 'Corporativo',
        'ativo' => true,
    ]);

    $this->assertDatabaseHas('clientes', [
        'nome' => 'Empresa Beta',
        'email' => 'beta@teste.com',
        'documento' => '98.765.432/0001-98',
        'segmento' => 'Varejo',
        'ativo' => false,
    ]);

    expect(Cliente::count())->toBe(2);

    $import->refresh();
    expect($import->status)->toBe('concluido');
    expect($import->importadas)->toBe(2);
});

test('cliente import ignores duplicates and missing required', function () {
    Storage::fake('local');

    Cliente::factory()->create(['email' => 'alfa@teste.com', 'documento' => '12.345.678/0001-95']);

    criarArquivoClientes('clientes.xlsx', [
        ['Empresa Alfa', 'alfa@teste.com', '12345678000195', '', '', '', '', ''],
        ['Empresa Gama', 'gama@teste.com', '11122233000144', '', '', '', '', 'Ativo'],
        ['Sem CNPJ', 'semcnpj@teste.com', '', '', '', '', '', ''],
    ]);

    $import = ClienteImport::create([
        'user_id' => $this->user->id,
        'arquivo' => 'imports/cliente/clientes.xlsx',
        'nome_original' => 'clientes.xlsx',
        'status' => 'pendente',
    ]);

    (new ProcessClienteImport($import->id))->handle();

    $this->assertDatabaseHas('clientes', [
        'nome' => 'Empresa Gama',
        'email' => 'gama@teste.com',
    ]);

    $this->assertDatabaseMissing('clientes', ['email' => 'semcnpj@teste.com']);

    $import->refresh();
    expect($import->status)->toBe('concluido');
    expect($import->importadas)->toBe(1);
    expect($import->ignoradas)->toBe(2);
});

test('cliente import marks status failed on error', function () {
    Storage::fake('local');

    $import = ClienteImport::create([
        'user_id' => $this->user->id,
        'arquivo' => 'imports/cliente/clientes.xlsx',
        'nome_original' => 'clientes.xlsx',
        'status' => 'pendente',
    ]);

    (new ProcessClienteImport($import->id))->handle();

    $import->refresh();
    expect($import->status)->toBe('falhou');
});

test('cliente import notifies via toast when completed', function () {
    $import = ClienteImport::create([
        'user_id' => $this->user->id,
        'arquivo' => 'imports/cliente/teste.xlsx',
        'nome_original' => 'clientes.xlsx',
        'status' => 'pendente',
    ]);

    $component = Livewire::test(Index::class);
    $component->call('verificarImportacoes')->assertHasNoErrors();

    $import->update(['status' => 'concluido']);

    $component->call('verificarImportacoes')->assertDispatched('flux-toast');
});
