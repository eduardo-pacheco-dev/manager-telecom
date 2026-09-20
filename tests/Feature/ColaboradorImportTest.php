<?php

use App\Jobs\ProcessColaboradorImport;
use App\Livewire\Colaboradores\Index;
use App\Models\Colaborador;
use App\Models\ColaboradorImport;
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

function criarArquivoColaboradores(string $nome, array $linhas): void
{
    Storage::disk('local')->makeDirectory('imports/colaborador');

    $cabecalhos = ['Nome', 'Email', 'CPF', 'Telefone', 'Cargo', 'Departamento', 'Categoria', 'Data_Admissão', 'Salário', 'Status'];

    $writer = new XlsxWriter;
    $writer->openToFile(Storage::disk('local')->path('imports/colaborador/'.$nome));
    $writer->addRow(Row::fromValues($cabecalhos));

    foreach ($linhas as $linha) {
        $writer->addRow(Row::fromValues($linha));
    }

    $writer->close();
}

test('colaboradores index shows import button', function () {
    Livewire::test(Index::class)
        ->assertSee('Importar');
});

test('colaborador import modal opens', function () {
    Livewire::test(Index::class)
        ->call('abrirImportacao')
        ->assertSet('showImportModal', true)
        ->assertSee('Importar colaboradores')
        ->assertSee('Arraste o arquivo aqui')
        ->assertSee('Nome');
});

test('colaborador import template can be downloaded', function () {
    $this->get(route('colaboradores.importar.modelo'))
        ->assertOk()
        ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
        ->assertDownload('modelo_colaboradores.xlsx');
});

test('colaborador import requires a file', function () {
    Livewire::test(Index::class)
        ->call('iniciarImportacao')
        ->assertHasErrors(['import_arquivo']);
});

test('colaborador import dispatches job and stores file', function () {
    Queue::fake();
    Storage::fake('local');

    Livewire::test(Index::class)
        ->set('import_arquivo', UploadedFile::fake()->create('colaboradores.xlsx', 100, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'))
        ->call('iniciarImportacao')
        ->assertHasNoErrors()
        ->assertSet('showImportModal', false);

    $this->assertDatabaseHas('colaborador_imports', [
        'user_id' => $this->user->id,
        'nome_original' => 'colaboradores.xlsx',
        'status' => 'pendente',
    ]);

    $import = ColaboradorImport::first();

    expect($import)->not->toBeNull();
    Storage::disk('local')->assertExists($import->arquivo);

    Queue::assertPushed(ProcessColaboradorImport::class, function (ProcessColaboradorImport $job) use ($import) {
        return $job->importId === $import->id;
    });
});

test('colaborador import processes valid rows', function () {
    Storage::fake('local');

    criarArquivoColaboradores('colaboradores.xlsx', [
        ['Ana Silva', 'ana@test.com', '12345678901', '(11) 99999-0001', 'Engenheira', 'Engenharia', 'CLT', '2024-01-15', '8500.00', 'Ativo'],
        ['Bruno Souza', 'bruno@test.com', '98765432100', '(21) 98888-0002', 'Analista', 'TI', 'PJ', '15/02/2024', 'R$ 6.000,00', 'Ativo'],
        ['', '', '', '', '', '', '', '', '', ''],
    ]);

    $import = ColaboradorImport::create([
        'user_id' => $this->user->id,
        'arquivo' => 'imports/colaborador/colaboradores.xlsx',
        'nome_original' => 'colaboradores.xlsx',
        'status' => 'pendente',
    ]);

    (new ProcessColaboradorImport($import->id))->handle();

    $this->assertDatabaseHas('colaboradores', [
        'nome' => 'Ana Silva',
        'email' => 'ana@test.com',
        'categoria' => 'CLT',
        'ativo' => true,
    ]);

    $this->assertDatabaseHas('colaboradores', [
        'nome' => 'Bruno Souza',
        'email' => 'bruno@test.com',
        'categoria' => 'PJ',
        'ativo' => true,
    ]);

    expect(Colaborador::count())->toBe(2);

    $import->refresh();
    expect($import->status)->toBe('concluido');
    expect($import->importadas)->toBe(2);
});

test('colaborador import ignores duplicates and missing required', function () {
    Storage::fake('local');

    Colaborador::factory()->create(['email' => 'ana@test.com', 'cpf' => '123.456.789-01']);

    criarArquivoColaboradores('colaboradores.xlsx', [
        ['Ana Silva', 'ana@test.com', '12345678901', '', '', '', 'CLT', '', '', ''],
        ['Carlos Lima', 'carlos@test.com', '11122233344', '', '', '', 'Freelancer', '', '', 'Inativo'],
        ['Sem CPF', 'semcpf@test.com', '', '', '', '', 'CLT', '', '', ''],
    ]);

    $import = ColaboradorImport::create([
        'user_id' => $this->user->id,
        'arquivo' => 'imports/colaborador/colaboradores.xlsx',
        'nome_original' => 'colaboradores.xlsx',
        'status' => 'pendente',
    ]);

    (new ProcessColaboradorImport($import->id))->handle();

    $this->assertDatabaseHas('colaboradores', [
        'nome' => 'Carlos Lima',
        'email' => 'carlos@test.com',
        'categoria' => 'Freelancer',
        'ativo' => false,
    ]);

    $this->assertDatabaseMissing('colaboradores', ['email' => 'semcpf@test.com']);

    $import->refresh();
    expect($import->status)->toBe('concluido');
    expect($import->importadas)->toBe(1);
    expect($import->ignoradas)->toBe(2);
});

test('colaborador import marks status failed on error', function () {
    Storage::fake('local');

    $import = ColaboradorImport::create([
        'user_id' => $this->user->id,
        'arquivo' => 'imports/colaborador/colaboradores.xlsx',
        'nome_original' => 'colaboradores.xlsx',
        'status' => 'pendente',
    ]);

    (new ProcessColaboradorImport($import->id))->handle();

    $import->refresh();
    expect($import->status)->toBe('falhou');
});

test('colaborador import notifies via toast when completed', function () {
    $import = ColaboradorImport::create([
        'user_id' => $this->user->id,
        'arquivo' => 'imports/colaborador/teste.xlsx',
        'nome_original' => 'colaboradores.xlsx',
        'status' => 'pendente',
    ]);

    $component = Livewire::test(Index::class);
    $component->call('verificarImportacoes')->assertHasNoErrors();

    $import->update(['status' => 'concluido']);

    $component->call('verificarImportacoes')->assertDispatched('flux-toast');
});
