<?php

use App\Jobs\ProcessTimProjetoImport;
use App\Livewire\Tim\Index;
use App\Models\Cliente;
use App\Models\Estacao;
use App\Models\OrdemServico;
use App\Models\TimProjeto;
use App\Models\TimProjetoImport;
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

function criarArquivoProjetosTim(string $nome, array $linhas): void
{
    Storage::disk('local')->makeDirectory('imports/projeto-tim');

    $cabecalhos = ['Código', 'Descrição', 'Status', 'Cliente', 'Estação', 'OC', 'OS FAM Entrega', 'OS FAM Instalação', 'OS FAM Panorâmica', 'OS FAM Desinstalação', 'Data Início', 'Data Fim', 'Ativo', 'Criar OS'];

    $writer = new XlsxWriter;
    $writer->openToFile(Storage::disk('local')->path('imports/projeto-tim/'.$nome));
    $writer->addRow(Row::fromValues($cabecalhos));

    foreach ($linhas as $linha) {
        $writer->addRow(Row::fromValues($linha));
    }

    $writer->close();
}

test('tim index shows import button', function () {
    Livewire::test(Index::class)
        ->assertSee('Importar');
});

test('tim import modal opens', function () {
    Livewire::test(Index::class)
        ->call('abrirImportacao')
        ->assertSet('showImportModal', true)
        ->assertSee('Importar projetos TIM Implantação RF')
        ->assertSee('Arraste o arquivo aqui')
        ->assertSee('Código');
});

test('tim import template can be downloaded', function () {
    $this->get(route('tim.importar.modelo'))
        ->assertOk()
        ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
        ->assertDownload('modelo_projetos_tim.xlsx');
});

test('tim import requires a file', function () {
    Livewire::test(Index::class)
        ->call('iniciarImportacao')
        ->assertHasErrors(['import_arquivo']);
});

test('tim import dispatches job and stores file', function () {
    Queue::fake();
    Storage::fake('local');

    Livewire::test(Index::class)
        ->set('import_arquivo', UploadedFile::fake()->create('projetos.xlsx', 100, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'))
        ->call('iniciarImportacao')
        ->assertHasNoErrors()
        ->assertSet('showImportModal', false);

    $this->assertDatabaseHas('tim_projeto_imports', [
        'user_id' => $this->user->id,
        'nome_original' => 'projetos.xlsx',
        'status' => 'pendente',
    ]);

    $import = TimProjetoImport::first();

    expect($import)->not->toBeNull();
    Storage::disk('local')->assertExists($import->arquivo);

    Queue::assertPushed(ProcessTimProjetoImport::class, function (ProcessTimProjetoImport $job) use ($import) {
        return $job->importId === $import->id;
    });
});

test('tim import processes valid rows creating projetos and OS', function () {
    Storage::fake('local');

    $cliente = Cliente::factory()->create(['nome' => 'Cliente TIM']);
    $estacao = Estacao::factory()->create(['site_id' => '4G-JQIT19']);

    criarArquivoProjetosTim('projetos.xlsx', [
        ['Implantação RAN TIM', 'Implantação de estações RAN.', 'Planejamento', 'Cliente TIM', '4G-JQIT19', 'OC-2026-001', 'FAM-1', 'FAM-2', 'FAM-3', 'FAM-4', '2026-01-10', '2026-12-20', 'Sim', 'Sim'],
        ['Modernização 5G', 'Modernização de sites.', 'Em andamento', '', '', '', '', '', '', '', '2026-03-01', '', 'Sim', ''],
        ['', '', '', '', '', '', '', '', '', '', '', '', '', ''],
    ]);

    $import = TimProjetoImport::create([
        'user_id' => $this->user->id,
        'arquivo' => 'imports/projeto-tim/projetos.xlsx',
        'nome_original' => 'projetos.xlsx',
        'status' => 'pendente',
    ]);

    (new ProcessTimProjetoImport($import->id))->handle();

    $this->assertDatabaseHas('tim_projetos', [
        'codigo' => 'Implantação RAN TIM',
        'status' => 'Planejamento',
        'cliente_id' => $cliente->id,
        'oc' => 'OC-2026-001',
        'ativo' => true,
    ]);

    $this->assertDatabaseHas('tim_projetos', [
        'codigo' => 'Modernização 5G',
        'status' => 'Em andamento',
        'ativo' => true,
    ]);

    expect(TimProjeto::count())->toBe(2);
    expect(TimProjeto::where('codigo', 'Modernização 5G')->first()->etapas()->count())->toBe(5);

    expect($estacao->refresh()->projeto_tim_id)->toBe(TimProjeto::where('codigo', 'Implantação RAN TIM')->first()->id);

    $ordem = OrdemServico::where('projeto_tim_id', TimProjeto::where('codigo', 'Implantação RAN TIM')->first()->id)->first();

    expect($ordem)->not->toBeNull();
    expect($ordem->estacao_a_id)->toBe($estacao->id);

    $import->refresh();
    expect($import->status)->toBe('concluido');
    expect($import->importadas)->toBe(2);
});

test('tim import marks status failed on invalid header', function () {
    Storage::fake('local');

    Storage::disk('local')->makeDirectory('imports/projeto-tim');

    $writer = new XlsxWriter;
    $writer->openToFile(Storage::disk('local')->path('imports/projeto-tim/invalido.xlsx'));
    $writer->addRow(Row::fromValues(['Foo', 'Bar']));
    $writer->addRow(Row::fromValues(['Valor 1', 'Valor 2']));
    $writer->close();

    $import = TimProjetoImport::create([
        'user_id' => $this->user->id,
        'arquivo' => 'imports/projeto-tim/invalido.xlsx',
        'nome_original' => 'invalido.xlsx',
        'status' => 'pendente',
    ]);

    (new ProcessTimProjetoImport($import->id))->handle();

    $import->refresh();

    expect($import->status)->toBe('falhou');
    expect($import->erro)->not->toBeNull();
    expect(TimProjeto::count())->toBe(0);
});