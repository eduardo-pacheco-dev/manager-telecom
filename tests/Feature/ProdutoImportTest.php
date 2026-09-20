<?php

use App\Jobs\ProcessProdutoImport;
use App\Livewire\Produtos\Index;
use App\Models\Produto;
use App\Models\ProdutoImport;
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

function criarArquivoProdutos(string $nome, array $linhas): void
{
    Storage::disk('local')->makeDirectory('imports/produto');

    $cabecalhos = ['Nome', 'Código', 'Categoria', 'Descrição', 'Preço', 'Status'];

    $writer = new XlsxWriter;
    $writer->openToFile(Storage::disk('local')->path('imports/produto/'.$nome));
    $writer->addRow(Row::fromValues($cabecalhos));

    foreach ($linhas as $linha) {
        $writer->addRow(Row::fromValues($linha));
    }

    $writer->close();
}

test('produtos index shows import button', function () {
    Livewire::test(Index::class)
        ->assertSee('Importar');
});

test('produto import modal opens', function () {
    Livewire::test(Index::class)
        ->call('abrirImportacao')
        ->assertSet('showImportModal', true)
        ->assertSee('Importar produtos')
        ->assertSee('Arraste o arquivo aqui')
        ->assertSee('Código');
});

test('produto import template can be downloaded', function () {
    $this->get(route('produtos.importar.modelo'))
        ->assertOk()
        ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
        ->assertDownload('modelo_produtos.xlsx');
});

test('produto import requires a file', function () {
    Livewire::test(Index::class)
        ->call('iniciarImportacao')
        ->assertHasErrors(['import_arquivo']);
});

test('produto import dispatches job and stores file', function () {
    Queue::fake();
    Storage::fake('local');

    Livewire::test(Index::class)
        ->set('import_arquivo', UploadedFile::fake()->create('produtos.xlsx', 100, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'))
        ->call('iniciarImportacao')
        ->assertHasNoErrors()
        ->assertSet('showImportModal', false);

    $this->assertDatabaseHas('produto_imports', [
        'user_id' => $this->user->id,
        'nome_original' => 'produtos.xlsx',
        'status' => 'pendente',
    ]);

    $import = ProdutoImport::first();

    expect($import)->not->toBeNull();
    Storage::disk('local')->assertExists($import->arquivo);

    Queue::assertPushed(ProcessProdutoImport::class, function (ProcessProdutoImport $job) use ($import) {
        return $job->importId === $import->id;
    });
});

test('produto import processes valid rows', function () {
    Storage::fake('local');

    criarArquivoProdutos('produtos.xlsx', [
        ['Roteador Wi-Fi 6', 'PROD-0001', 'Equipamento', 'Dual band', '349.90', 'Ativo'],
        ['Cabo Cat6 30m', 'PROD-0002', 'cabo', 'Patch cord', 'R$ 89,90', 'Inativo'],
    ]);

    $import = ProdutoImport::create([
        'user_id' => $this->user->id,
        'arquivo' => 'imports/produto/produtos.xlsx',
        'nome_original' => 'produtos.xlsx',
        'status' => 'pendente',
    ]);

    (new ProcessProdutoImport($import->id))->handle();

    $this->assertDatabaseHas('produtos', [
        'nome' => 'Roteador Wi-Fi 6',
        'codigo' => 'PROD-0001',
        'categoria' => 'Equipamento',
        'ativo' => true,
    ]);

    $this->assertDatabaseHas('produtos', [
        'nome' => 'Cabo Cat6 30m',
        'codigo' => 'PROD-0002',
        'categoria' => 'Cabeamento',
        'ativo' => false,
    ]);

    expect(Produto::count())->toBe(2);

    $import->refresh();
    expect($import->status)->toBe('concluido');
    expect($import->importadas)->toBe(2);
});

test('produto import ignores duplicates and missing required', function () {
    Storage::fake('local');

    Produto::factory()->create(['codigo' => 'PROD-0001']);

    criarArquivoProdutos('produtos.xlsx', [
        ['Duplicado', 'PROD-0001', 'Equipamento', '', '', ''],
        ['', '', 'Equipamento', '', '', ''],
        ['Novo Produto', 'PROD-0003', 'Acessório', '', '', 'Ativo'],
    ]);

    $import = ProdutoImport::create([
        'user_id' => $this->user->id,
        'arquivo' => 'imports/produto/produtos.xlsx',
        'nome_original' => 'produtos.xlsx',
        'status' => 'pendente',
    ]);

    (new ProcessProdutoImport($import->id))->handle();

    $this->assertDatabaseHas('produtos', [
        'nome' => 'Novo Produto',
        'codigo' => 'PROD-0003',
    ]);

    $import->refresh();
    expect($import->status)->toBe('concluido');
    expect($import->importadas)->toBe(1);
    expect($import->ignoradas)->toBe(2);
});

test('produto import marks status failed on error', function () {
    Storage::fake('local');

    $import = ProdutoImport::create([
        'user_id' => $this->user->id,
        'arquivo' => 'imports/produto/produtos.xlsx',
        'nome_original' => 'produtos.xlsx',
        'status' => 'pendente',
    ]);

    (new ProcessProdutoImport($import->id))->handle();

    $import->refresh();
    expect($import->status)->toBe('falhou');
});

test('produto import notifies via toast when completed', function () {
    $import = ProdutoImport::create([
        'user_id' => $this->user->id,
        'arquivo' => 'imports/produto/teste.xlsx',
        'nome_original' => 'produtos.xlsx',
        'status' => 'pendente',
    ]);

    $component = Livewire::test(Index::class);
    $component->call('verificarImportacoes')->assertHasNoErrors();

    $import->update(['status' => 'concluido']);

    $component->call('verificarImportacoes')->assertDispatched('flux-toast');
});
