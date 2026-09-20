<?php

use App\Jobs\ProcessOrdemServicoImport;
use App\Livewire\OrdensServico\Index;
use App\Models\Estacao;
use App\Models\OrdemServico;
use App\Models\OrdemServicoImport;
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

test('ordens-servico index shows import button', function () {
    Livewire::test(Index::class)
        ->assertSee('Importar');
});

test('ordem servico import modal opens', function () {
    Livewire::test(Index::class)
        ->call('abrirImportacao')
        ->assertSet('showImportModal', true)
        ->assertSee('Importar ordens de serviço')
        ->assertSee('Arraste o arquivo aqui')
        ->assertSee('Cód_AFL');
});

test('ordem servico import requires a file', function () {
    Livewire::test(Index::class)
        ->call('iniciarImportacao')
        ->assertHasErrors(['import_arquivo']);
});

test('ordem servico import dispatches job and stores file', function () {
    Queue::fake();
    Storage::fake('local');

    Livewire::test(Index::class)
        ->set('import_arquivo', UploadedFile::fake()->create('ordens.xlsx', 100, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'))
        ->call('iniciarImportacao')
        ->assertHasNoErrors()
        ->assertSet('showImportModal', false);

    $this->assertDatabaseHas('ordem_servico_imports', [
        'user_id' => $this->user->id,
        'nome_original' => 'ordens.xlsx',
        'status' => 'pendente',
    ]);

    $import = OrdemServicoImport::first();

    expect($import)->not->toBeNull();
    Storage::disk('local')->assertExists($import->arquivo);

    Queue::assertPushed(ProcessOrdemServicoImport::class, function (ProcessOrdemServicoImport $job) use ($import) {
        return $job->importId === $import->id;
    });
});

test('ordem servico import notifies via toast when completed', function () {
    $import = OrdemServicoImport::create([
        'user_id' => $this->user->id,
        'arquivo' => 'imports/ordem-servico/teste.xlsx',
        'nome_original' => 'ordens.xlsx',
        'status' => 'pendente',
    ]);

    $component = Livewire::test(Index::class);
    $component->call('verificarImportacoes')->assertHasNoErrors();

    $import->update(['status' => 'concluido']);

    $component->call('verificarImportacoes')->assertDispatched('flux-toast');
});

test('ordem servico import notifies via toast when failed', function () {
    $import = OrdemServicoImport::create([
        'user_id' => $this->user->id,
        'arquivo' => 'imports/ordem-servico/teste.xlsx',
        'nome_original' => 'ordens.xlsx',
        'status' => 'pendente',
    ]);

    $component = Livewire::test(Index::class);
    $component->call('verificarImportacoes')->assertHasNoErrors();

    $import->update(['status' => 'falhou', 'erro' => 'Erro de teste']);

    $component->call('verificarImportacoes')->assertDispatched('flux-toast');
});

test('ordem servico import maps excel columns and stores raw data', function () {
    Storage::fake('local');
    Estacao::factory()->create(['site_id' => '4G-JQIT19']);

    criarArquivoOrdemServico('ordens.xlsx', [[
        'AFL20260620', 'Pendente OS/PO', '4G-JQIT19', '', '', '', 'CT_REUSO',
        'VISTORIA', 'Fabricio Paes', 'Djalma Teixeira', '1311997', '', '',
        'teste', '2026-05-05 00:00:00',
    ]]);

    $import = OrdemServicoImport::create([
        'user_id' => $this->user->id,
        'arquivo' => 'imports/ordem-servico/ordens.xlsx',
        'nome_original' => 'ordens.xlsx',
        'status' => OrdemServicoImport::STATUS_PENDENTE,
    ]);

    (new ProcessOrdemServicoImport($import->id))->handle();

    $import->refresh();

    expect($import->status)->toBe(OrdemServicoImport::STATUS_CONCLUIDO);
    expect($import->importadas)->toBe(1);

    $ordem = OrdemServico::where('codigo', 'AFL20260620')->first();

    expect($ordem)->not->toBeNull();
    expect($ordem->status)->toBe('Aberta');
    expect($ordem->tipo)->toBe('Inspeção');
    expect($ordem->projeto)->toBe('CT_REUSO');
    expect($ordem->supervisor)->toBe('Fabricio Paes');
    expect($ordem->coordenador)->toBe('Djalma Teixeira');
    expect($ordem->oc_tim)->toBe('1311997');
    expect($ordem->observacao)->toBe('teste');
    expect($ordem->data_abertura?->format('Y-m-d'))->toBe('2026-05-05');
    expect($ordem->dados_brutos)->toBeArray();
});

test('ordem servico import updates duplicate codigo', function () {
    Storage::fake('local');
    Estacao::factory()->create(['site_id' => '4G-JQIT19']);

    OrdemServico::factory()->create(['codigo' => 'AFL20260620', 'titulo' => 'Antigo']);

    criarArquivoOrdemServico('ordens.xlsx', [[
        'AFL20260620', 'Concluída', '4G-JQIT19', '', '', '', 'CT_MW',
        'ATIVAÇÃO', 'Fabricio Paes', 'Djalma Teixeira', '1311997', '', '',
        '', '2026-05-05 00:00:00',
    ]]);

    $import = OrdemServicoImport::create([
        'user_id' => $this->user->id,
        'arquivo' => 'imports/ordem-servico/ordens.xlsx',
        'nome_original' => 'ordens.xlsx',
        'status' => OrdemServicoImport::STATUS_PENDENTE,
    ]);

    (new ProcessOrdemServicoImport($import->id))->handle();

    $ordem = OrdemServico::where('codigo', 'AFL20260620')->first();

    expect($ordem->status)->toBe('Concluída');
    expect($ordem->tipo)->toBe('Ativação');
});

test('ordem servico import ignores rows without codigo or unknown stations', function () {
    Storage::fake('local');
    Estacao::factory()->create(['site_id' => '4G-JQIT19']);

    criarArquivoOrdemServico('ordens.xlsx', [
        [
            '', 'Pendente', '4G-JQIT19', '', '', '', 'CT_REUSO',
            '', '', '', '', '', '', '', '',
        ],
        [
            'AFL-X1', 'Pendente', 'ZZ9999', '', '', '', 'CT_MW',
            '', '', '', '', '', '', '', '',
        ],
    ]);

    $import = OrdemServicoImport::create([
        'user_id' => $this->user->id,
        'arquivo' => 'imports/ordem-servico/ordens.xlsx',
        'nome_original' => 'ordens.xlsx',
        'status' => OrdemServicoImport::STATUS_PENDENTE,
    ]);

    (new ProcessOrdemServicoImport($import->id))->handle();

    $import->refresh();

    expect($import->importadas)->toBe(1);
    expect($import->ignoradas)->toBe(1);

    $this->assertDatabaseHas('ordens_servico', ['codigo' => 'AFL-X1']);
});

test('ordem servico import marks as failed when file is missing', function () {
    Storage::fake('local');

    $import = OrdemServicoImport::create([
        'user_id' => $this->user->id,
        'arquivo' => 'imports/ordem-servico/inexistente.xlsx',
        'nome_original' => 'inexistente.xlsx',
        'status' => OrdemServicoImport::STATUS_PENDENTE,
    ]);

    (new ProcessOrdemServicoImport($import->id))->handle();

    $import->refresh();

    expect($import->status)->toBe(OrdemServicoImport::STATUS_FALHOU);
    expect($import->erro)->not->toBeNull();
});

/**
 * @param  array<int, array<int, mixed>>  $linhas
 */
function criarArquivoOrdemServico(string $nome, array $linhas): void
{
    Storage::disk('local')->makeDirectory('imports/ordem-servico');

    $cabecalhos = [
        'Cód_AFL', 'Status_Geral', 'Site_ID A', 'END_ID A', 'Site_ID B', 'END_ID B',
        'Projeto', 'Descrição', 'Supervisor', 'Coordenador', 'OC (TIM)', 'Chave_MW',
        'SMP_Nokia', 'OBS GERAL', 'Data_Cadastro_Ativ',
    ];

    $writer = new XlsxWriter;
    $writer->openToFile(Storage::disk('local')->path('imports/ordem-servico/'.$nome));
    $writer->addRow(Row::fromValues($cabecalhos));

    foreach ($linhas as $linha) {
        $writer->addRow(Row::fromValues($linha));
    }

    $writer->close();
}
