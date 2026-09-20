<?php

use App\Jobs\ProcessServicoImport;
use App\Livewire\Servicos\Index;
use App\Models\Servico;
use App\Models\ServicoImport;
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

function criarArquivoServicos(string $nome, array $linhas): void
{
    Storage::disk('local')->makeDirectory('imports/servico');

    $cabecalhos = ['Nome', 'Código', 'Categoria', 'Descrição', 'Preço', 'Status'];

    $writer = new XlsxWriter;
    $writer->openToFile(Storage::disk('local')->path('imports/servico/'.$nome));
    $writer->addRow(Row::fromValues($cabecalhos));

    foreach ($linhas as $linha) {
        $writer->addRow(Row::fromValues($linha));
    }

    $writer->close();
}

test('servicos index shows import button', function () {
    Livewire::test(Index::class)
        ->assertSee('Importar');
});

test('servico import modal opens', function () {
    Livewire::test(Index::class)
        ->call('abrirImportacao')
        ->assertSet('showImportModal', true)
        ->assertSee('Importar serviços')
        ->assertSee('Arraste o arquivo aqui')
        ->assertSee('Código');
});

test('servico import template can be downloaded', function () {
    $this->get(route('servicos.importar.modelo'))
        ->assertOk()
        ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
        ->assertDownload('modelo_servicos.xlsx');
});

test('servico import requires a file', function () {
    Livewire::test(Index::class)
        ->call('iniciarImportacao')
        ->assertHasErrors(['import_arquivo']);
});

test('servico import dispatches job and stores file', function () {
    Queue::fake();
    Storage::fake('local');

    Livewire::test(Index::class)
        ->set('import_arquivo', UploadedFile::fake()->create('servicos.xlsx', 100, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'))
        ->call('iniciarImportacao')
        ->assertHasNoErrors()
        ->assertSet('showImportModal', false);

    $this->assertDatabaseHas('servico_imports', [
        'user_id' => $this->user->id,
        'nome_original' => 'servicos.xlsx',
        'status' => 'pendente',
    ]);

    $import = ServicoImport::first();

    expect($import)->not->toBeNull();
    Storage::disk('local')->assertExists($import->arquivo);

    Queue::assertPushed(ProcessServicoImport::class, function (ProcessServicoImport $job) use ($import) {
        return $job->importId === $import->id;
    });
});

test('servico import processes valid rows', function () {
    Storage::fake('local');

    criarArquivoServicos('servicos.xlsx', [
        ['Instalação de Fibra', 'SRV-0001', 'Instalação', 'Fibra residencial', '199.90', 'Ativo'],
        ['Suporte Técnico', 'SRV-0002', 'suporte', 'Atendimento', 'R$ 89,90', 'Inativo'],
    ]);

    $import = ServicoImport::create([
        'user_id' => $this->user->id,
        'arquivo' => 'imports/servico/servicos.xlsx',
        'nome_original' => 'servicos.xlsx',
        'status' => 'pendente',
    ]);

    (new ProcessServicoImport($import->id))->handle();

    $this->assertDatabaseHas('servicos', [
        'nome' => 'Instalação de Fibra',
        'codigo' => 'SRV-0001',
        'categoria' => 'Instalação',
        'ativo' => true,
    ]);

    $this->assertDatabaseHas('servicos', [
        'nome' => 'Suporte Técnico',
        'codigo' => 'SRV-0002',
        'categoria' => 'Suporte',
        'ativo' => false,
    ]);

    expect(Servico::count())->toBe(2);

    $import->refresh();
    expect($import->status)->toBe('concluido');
    expect($import->importadas)->toBe(2);
});

test('servico import ignores duplicates and missing required', function () {
    Storage::fake('local');

    Servico::factory()->create(['codigo' => 'SRV-0001']);

    criarArquivoServicos('servicos.xlsx', [
        ['Duplicado', 'SRV-0001', 'Instalação', '', '', ''],
        ['', '', 'Instalação', '', '', ''],
        ['Novo Serviço', 'SRV-0003', 'Configuração', '', '', 'Ativo'],
    ]);

    $import = ServicoImport::create([
        'user_id' => $this->user->id,
        'arquivo' => 'imports/servico/servicos.xlsx',
        'nome_original' => 'servicos.xlsx',
        'status' => 'pendente',
    ]);

    (new ProcessServicoImport($import->id))->handle();

    $this->assertDatabaseHas('servicos', [
        'nome' => 'Novo Serviço',
        'codigo' => 'SRV-0003',
    ]);

    $import->refresh();
    expect($import->status)->toBe('concluido');
    expect($import->importadas)->toBe(1);
    expect($import->ignoradas)->toBe(2);
});

test('servico import marks status failed on error', function () {
    Storage::fake('local');

    $import = ServicoImport::create([
        'user_id' => $this->user->id,
        'arquivo' => 'imports/servico/servicos.xlsx',
        'nome_original' => 'servicos.xlsx',
        'status' => 'pendente',
    ]);

    (new ProcessServicoImport($import->id))->handle();

    $import->refresh();
    expect($import->status)->toBe('falhou');
});

test('servico import notifies via toast when completed', function () {
    $import = ServicoImport::create([
        'user_id' => $this->user->id,
        'arquivo' => 'imports/servico/teste.xlsx',
        'nome_original' => 'servicos.xlsx',
        'status' => 'pendente',
    ]);

    $component = Livewire::test(Index::class);
    $component->call('verificarImportacoes')->assertHasNoErrors();

    $import->update(['status' => 'concluido']);

    $component->call('verificarImportacoes')->assertDispatched('flux-toast');
});
