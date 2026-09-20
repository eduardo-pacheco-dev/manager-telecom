<?php

use App\Jobs\ProcessEstacaoImport;
use App\Livewire\Estacoes\Index;
use App\Models\Estacao;
use App\Models\EstacaoImport;
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

function criarArquivoEstacoes(string $nome, array $linhas): void
{
    Storage::disk('local')->makeDirectory('imports/estacao');

    $cabecalhos = ['Site_ID', 'Endereco_ID', 'Tipo_Elemento', 'Tecnologia', 'Classificacao', 'Municipio', 'Estado', 'Regional', 'Status', 'Data_Aquisicao'];

    $writer = new XlsxWriter;
    $writer->openToFile(Storage::disk('local')->path('imports/estacao/'.$nome));
    $writer->addRow(Row::fromValues($cabecalhos));

    foreach ($linhas as $linha) {
        $writer->addRow(Row::fromValues($linha));
    }

    $writer->close();
}

test('estacoes index shows import button', function () {
    Livewire::test(Index::class)
        ->assertSee('Importar');
});

test('estacao import modal opens', function () {
    Livewire::test(Index::class)
        ->call('abrirImportacao')
        ->assertSet('showImportModal', true)
        ->assertSee('Importar estações')
        ->assertSee('Arraste o arquivo aqui')
        ->assertSee('Site_ID');
});

test('estacao import template can be downloaded', function () {
    $this->get(route('estacoes.importar.modelo'))
        ->assertOk()
        ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
        ->assertDownload('modelo_estacoes.xlsx');
});

test('estacao import requires a file', function () {
    Livewire::test(Index::class)
        ->call('iniciarImportacao')
        ->assertHasErrors(['import_arquivo']);
});

test('estacao import dispatches job and stores file', function () {
    Queue::fake();
    Storage::fake('local');

    Livewire::test(Index::class)
        ->set('import_arquivo', UploadedFile::fake()->create('estacoes.xlsx', 100, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'))
        ->call('iniciarImportacao')
        ->assertHasNoErrors()
        ->assertSet('showImportModal', false);

    $this->assertDatabaseHas('estacao_imports', [
        'user_id' => $this->user->id,
        'nome_original' => 'estacoes.xlsx',
        'status' => 'pendente',
    ]);

    $import = EstacaoImport::first();

    expect($import)->not->toBeNull();
    Storage::disk('local')->assertExists($import->arquivo);

    Queue::assertPushed(ProcessEstacaoImport::class, function (ProcessEstacaoImport $job) use ($import) {
        return $job->importId === $import->id;
    });
});

test('estacao import processes valid rows', function () {
    Storage::fake('local');

    criarArquivoEstacoes('estacoes.xlsx', [
        ['AC1001', 'AC1001_001', 'BTS', 'LTE', 'ACESSO', 'São Paulo', 'SP', 'TCO', 'Ativo', '2023-05-10'],
        ['AC1002', 'AC1002_002', 'ENODE B', '5G NR', 'BACKHAUL', 'Campinas', 'SP', 'TCL', 'Em construção', '15/03/2024'],
    ]);

    $import = EstacaoImport::create([
        'user_id' => $this->user->id,
        'arquivo' => 'imports/estacao/estacoes.xlsx',
        'nome_original' => 'estacoes.xlsx',
        'status' => 'pendente',
    ]);

    (new ProcessEstacaoImport($import->id))->handle();

    $this->assertDatabaseHas('estacoes', [
        'site_id' => 'AC1001',
        'endereco_id' => 'AC1001_001',
        'tipo_elemento' => 'BTS',
        'tecnologia' => 'LTE',
        'classificacao' => 'ACESSO',
        'municipio' => 'São Paulo',
        'estado' => 'SP',
        'regional' => 'TCO',
        'status' => 'Ativo',
    ]);

    $this->assertDatabaseHas('estacoes', [
        'site_id' => 'AC1002',
        'tipo_elemento' => 'ENODE B',
        'tecnologia' => '5G NR',
        'status' => 'Em construção',
    ]);

    expect(Estacao::count())->toBe(2);

    $import->refresh();
    expect($import->status)->toBe('concluido');
    expect($import->importadas)->toBe(2);
});

test('estacao import ignores duplicates and missing required', function () {
    Storage::fake('local');

    Estacao::factory()->create(['site_id' => 'AC1001']);

    criarArquivoEstacoes('estacoes.xlsx', [
        ['AC1001', '', 'BTS', '', '', '', '', '', '', ''],
        ['', '', 'BTS', '', '', '', '', '', '', ''],
        ['AC1003', '', 'NODE B', 'GSM', '', 'São Paulo', 'SP', '', 'Ativo', ''],
    ]);

    $import = EstacaoImport::create([
        'user_id' => $this->user->id,
        'arquivo' => 'imports/estacao/estacoes.xlsx',
        'nome_original' => 'estacoes.xlsx',
        'status' => 'pendente',
    ]);

    (new ProcessEstacaoImport($import->id))->handle();

    $this->assertDatabaseHas('estacoes', [
        'site_id' => 'AC1003',
        'tipo_elemento' => 'NODE B',
    ]);

    $import->refresh();
    expect($import->status)->toBe('concluido');
    expect($import->importadas)->toBe(1);
    expect($import->ignoradas)->toBe(2);
});

test('estacao import marks status failed on error', function () {
    Storage::fake('local');

    $import = EstacaoImport::create([
        'user_id' => $this->user->id,
        'arquivo' => 'imports/estacao/estacoes.xlsx',
        'nome_original' => 'estacoes.xlsx',
        'status' => 'pendente',
    ]);

    (new ProcessEstacaoImport($import->id))->handle();

    $import->refresh();
    expect($import->status)->toBe('falhou');
});

test('estacao import notifies via toast when completed', function () {
    $import = EstacaoImport::create([
        'user_id' => $this->user->id,
        'arquivo' => 'imports/estacao/teste.xlsx',
        'nome_original' => 'estacoes.xlsx',
        'status' => 'pendente',
    ]);

    $component = Livewire::test(Index::class);
    $component->call('verificarImportacoes')->assertHasNoErrors();

    $import->update(['status' => 'concluido']);

    $component->call('verificarImportacoes')->assertDispatched('flux-toast');
});
