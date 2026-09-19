<?php

use App\Jobs\ProcessRadioLinkImport;
use App\Livewire\RadioLinks\Index;
use App\Models\Estacao;
use App\Models\RadioLink;
use App\Models\RadioLinkImport;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\CSV\Options;
use OpenSpout\Writer\CSV\Writer as CsvWriter;
use OpenSpout\Writer\XLSX\Writer as XlsxWriter;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('radio-links index shows import button', function () {
    Livewire::test(Index::class)
        ->assertSee('Importar');
});

test('import modal opens and shows template link', function () {
    Livewire::test(Index::class)
        ->call('abrirImportacao')
        ->assertSet('showImportModal', true)
        ->assertSee('Importar radio links')
        ->assertSee('Baixar modelo de planilha');
});

test('import requires a file', function () {
    Livewire::test(Index::class)
        ->call('iniciarImportacao')
        ->assertHasErrors(['import_arquivo']);
});

test('import rejects non excel files', function () {
    Livewire::test(Index::class)
        ->set('import_arquivo', UploadedFile::fake()->create('dados.txt', 100, 'text/plain'))
        ->call('iniciarImportacao')
        ->assertHasErrors(['import_arquivo']);
});

test('import dispatches job and stores file', function () {
    Queue::fake();
    Storage::fake('local');

    Livewire::test(Index::class)
        ->set('import_arquivo', UploadedFile::fake()->create('radio_links.xlsx', 100, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'))
        ->call('iniciarImportacao')
        ->assertHasNoErrors()
        ->assertSet('showImportModal', false);

    $this->assertDatabaseHas('radio_link_imports', [
        'user_id' => $this->user->id,
        'nome_original' => 'radio_links.xlsx',
        'status' => 'pendente',
    ]);

    $import = RadioLinkImport::first();

    expect($import)->not->toBeNull();
    Storage::disk('local')->assertExists($import->arquivo);

    Queue::assertPushed(ProcessRadioLinkImport::class, function (ProcessRadioLinkImport $job) use ($import) {
        return $job->importId === $import->id;
    });
});

test('import model records progress through job lifecycle', function () {
    Storage::fake('local');
    $estacaoA = Estacao::factory()->create(['site_id' => 'AC10J1']);
    $estacaoB = Estacao::factory()->create(['site_id' => 'AC1001']);
    criarArquivoImportacao('radio_links.xlsx', [[
        'RL-T1', 'Link Teste', 'AC10J1', 'AC1001', 23, '1 Gbps',
        '1E1', 'Dupla', 'ERICSSON', 'MINI-LINK 6363', 12.5, 'Ativo',
        '2023-05-10', 'Importado via teste.',
    ]]);

    $import = RadioLinkImport::create([
        'user_id' => $this->user->id,
        'arquivo' => 'imports/radio-links/radio_links.xlsx',
        'nome_original' => 'radio_links.xlsx',
        'status' => RadioLinkImport::STATUS_PENDENTE,
    ]);

    (new ProcessRadioLinkImport($import->id))->handle();

    $import->refresh();

    expect($import->status)->toBe(RadioLinkImport::STATUS_CONCLUIDO);
    expect($import->total_linhas)->toBe(1);
    expect($import->importadas)->toBe(1);
    expect($import->ignoradas)->toBe(0);

    $this->assertDatabaseHas('radio_links', [
        'codigo' => 'RL-T1',
        'estacao_a_id' => $estacaoA->id,
        'estacao_b_id' => $estacaoB->id,
        'frequencia' => 23.0,
        'capacidade' => '1 Gbps',
        'status' => 'Ativo',
    ]);
});

test('import resolves stations by site id and updates duplicates', function () {
    Storage::fake('local');
    $estacaoA = Estacao::factory()->create(['site_id' => 'AC10J1']);
    $estacaoB = Estacao::factory()->create(['site_id' => 'AC1001']);

    RadioLink::factory()->create([
        'codigo' => 'RL-DUP',
        'estacao_a_id' => $estacaoA->id,
        'estacao_b_id' => $estacaoB->id,
        'status' => 'Inativo',
    ]);

    $arquivo = criarArquivoImportacao('radio_links.xlsx', [[
        'RL-DUP', 'Link Atualizado', 'AC10J1', 'AC1001', null, '2 Gbps',
        null, null, null, null, null, 'Ativo', null, null,
    ]]);

    $import = RadioLinkImport::create([
        'user_id' => $this->user->id,
        'arquivo' => 'imports/radio-links/radio_links.xlsx',
        'nome_original' => 'radio_links.xlsx',
        'status' => RadioLinkImport::STATUS_PENDENTE,
    ]);

    (new ProcessRadioLinkImport($import->id))->handle();

    $import->refresh();

    expect($import->status)->toBe(RadioLinkImport::STATUS_CONCLUIDO);
    expect($import->importadas)->toBe(1);

    $radioLink = RadioLink::where('codigo', 'RL-DUP')->first();
    expect($radioLink->capacidade)->toBe('2 Gbps');
    expect($radioLink->status)->toBe('Ativo');
    expect($radioLink->nome)->toBe('Link Atualizado');
});

test('import skips rows without required data or unknown stations', function () {
    Storage::fake('local');

    $arquivo = criarArquivoImportacao('radio_links.xlsx', [
        [
            '', 'Sem cÃ³digo', 'AC10J1', 'AC1001', null, null,
            null, null, null, null, null, null, null, null,
        ],
        [
            'RL-X1', 'EstaÃ§Ã£o inexistente', 'ZZ9999', 'AC1001', null, null,
            null, null, null, null, null, null, null, null,
        ],
        [
            'RL-X2', 'A igual B', 'AC10J1', 'AC10J1', null, null,
            null, null, null, null, null, null, null, null,
        ],
    ]);

    $import = RadioLinkImport::create([
        'user_id' => $this->user->id,
        'arquivo' => 'imports/radio-links/radio_links.xlsx',
        'nome_original' => 'radio_links.xlsx',
        'status' => RadioLinkImport::STATUS_PENDENTE,
    ]);

    (new ProcessRadioLinkImport($import->id))->handle();

    $import->refresh();

    expect($import->status)->toBe(RadioLinkImport::STATUS_CONCLUIDO);
    expect($import->total_linhas)->toBe(3);
    expect($import->importadas)->toBe(0);
    expect($import->ignoradas)->toBe(3);

    $this->assertDatabaseMissing('radio_links', ['codigo' => 'RL-X1']);
    $this->assertDatabaseMissing('radio_links', ['codigo' => 'RL-X2']);
});

test('import supports csv files', function () {
    Storage::fake('local');
    Estacao::factory()->create(['site_id' => 'AC10J1']);
    Estacao::factory()->create(['site_id' => 'AC1001']);
    criarArquivoCsv('radio_links.csv', [[
        'RL-CSV', 'Link CSV', 'AC10J1', 'AC1001', 23, '1 Gbps',
        '1E1', 'Dupla', 'ERICSSON', 'MINI-LINK 6363', 12.5, 'Ativo',
        '2023-05-10', 'Importado via CSV.',
    ]]);

    $import = RadioLinkImport::create([
        'user_id' => $this->user->id,
        'arquivo' => 'imports/radio-links/radio_links.csv',
        'nome_original' => 'radio_links.csv',
        'status' => RadioLinkImport::STATUS_PENDENTE,
    ]);

    (new ProcessRadioLinkImport($import->id))->handle();

    $import->refresh();

    expect($import->status)->toBe(RadioLinkImport::STATUS_CONCLUIDO);
    expect($import->importadas)->toBe(1);

    $this->assertDatabaseHas('radio_links', ['codigo' => 'RL-CSV']);
});

test('import marks as failed when file is missing', function () {
    Storage::fake('local');

    $import = RadioLinkImport::create([
        'user_id' => $this->user->id,
        'arquivo' => 'imports/radio-links/inexistente.xlsx',
        'nome_original' => 'inexistente.xlsx',
        'status' => RadioLinkImport::STATUS_PENDENTE,
    ]);

    (new ProcessRadioLinkImport($import->id))->handle();

    $import->refresh();

    expect($import->status)->toBe(RadioLinkImport::STATUS_FALHOU);
    expect($import->erro)->not->toBeNull();
});

test('import template route returns xlsx', function () {
    $this->get(route('radio-links.importar.modelo'))
        ->assertOk()
        ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
});

/**
 * @param  array<int, array<int, mixed>>  $linhas
 */
function criarArquivoImportacao(string $nome, array $linhas): void
{
    Storage::disk('local')->makeDirectory('imports/radio-links');

    $cabecalhos = [
        'codigo', 'nome', 'estacao_a', 'estacao_b', 'frequencia', 'capacidade',
        'canal', 'polarizacao', 'fabricante', 'modelo', 'distancia', 'status',
        'data_ativacao', 'observacao',
    ];

    $writer = new XlsxWriter;
    $writer->openToFile(Storage::disk('local')->path('imports/radio-links/'.$nome));
    $writer->addRow(Row::fromValues($cabecalhos));

    foreach ($linhas as $linha) {
        $writer->addRow(Row::fromValues($linha));
    }

    $writer->close();
}

/**
 * @param  array<int, array<int, mixed>>  $linhas
 */
function criarArquivoCsv(string $nome, array $linhas): void
{
    Storage::disk('local')->makeDirectory('imports/radio-links');

    $cabecalhos = [
        'codigo', 'nome', 'estacao_a', 'estacao_b', 'frequencia', 'capacidade',
        'canal', 'polarizacao', 'fabricante', 'modelo', 'distancia', 'status',
        'data_ativacao', 'observacao',
    ];

    $writer = new CsvWriter(new Options(
        FIELD_DELIMITER: ';',
    ));
    $writer->openToFile(Storage::disk('local')->path('imports/radio-links/'.$nome));
    $writer->addRow(Row::fromValues($cabecalhos));

    foreach ($linhas as $linha) {
        $writer->addRow(Row::fromValues($linha));
    }

    $writer->close();
}
