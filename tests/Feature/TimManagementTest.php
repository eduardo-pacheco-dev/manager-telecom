<?php

use App\Livewire\Tim\Create;
use App\Livewire\Tim\Edit;
use App\Livewire\Tim\Index;
use App\Livewire\Tim\RelatorioCreate;
use App\Livewire\Tim\RelatorioShow;
use App\Livewire\Tim\Show;
use App\Models\Estacao;
use App\Models\OrdemServico;
use App\Models\TimProjeto;
use App\Models\TimProjetoEtapa;
use App\Models\TimRelatorio;
use App\Models\User;
use App\Services\ExcelExporter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Symfony\Component\HttpFoundation\StreamedResponse;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('tim index page is displayed', function () {
    TimProjeto::factory()->count(3)->create();

    $this->get(route('tim.index'))->assertOk();
});

test('tim index shows list and stats', function () {
    TimProjeto::factory()->count(3)->create();

    Livewire::test(Index::class)
        ->assertSee('Projetos TIM')
        ->assertSee('Total de projetos')
        ->assertSee('Ativos')
        ->assertSee('OS vinculadas');
});

test('tim projeto can be created', function () {
    $estacao = Estacao::factory()->create();

    Livewire::test(Create::class)
        ->set('nome', 'Implantação RAN TIM')
        ->set('status', 'Em andamento')
        ->set('data_inicio', '2026-05-01')
        ->set('estacao_id', $estacao->id)
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('tim_projetos', [
        'codigo' => 'TIM-0001',
        'nome' => 'Implantação RAN TIM',
        'status' => 'Em andamento',
    ]);
});

test('tim projeto creation creates an OS automatically when none is selected', function () {
    $estacao = Estacao::factory()->create();

    Livewire::test(Create::class)
        ->set('nome', 'Implantação RAN TIM')
        ->set('status', 'Em andamento')
        ->set('estacao_id', $estacao->id)
        ->call('save')
        ->assertHasNoErrors();

    $projeto = TimProjeto::where('nome', 'Implantação RAN TIM')->first();

    $ordem = $projeto->ordensServico()->first();

    expect($ordem)->not->toBeNull();
    expect($ordem->codigo)->toBe('OS-0001');
    expect($ordem->estacao_a_id)->toBe($estacao->id);
    expect($ordem->escopo)->toBe('Estação');
    expect($ordem->status)->toBe('Aberta');
});

test('tim projeto creation links an existing OS when selected', function () {
    $estacao = Estacao::factory()->create();
    $ordem = OrdemServico::factory()->create(['escopo' => 'Estação', 'estacao_a_id' => $estacao->id]);

    Livewire::test(Create::class)
        ->set('nome', 'Implantação RAN TIM')
        ->set('status', 'Em andamento')
        ->set('estacao_id', $estacao->id)
        ->set('ordem_servico_id', $ordem->id)
        ->call('save')
        ->assertHasNoErrors();

    $projeto = TimProjeto::where('nome', 'Implantação RAN TIM')->first();

    expect($ordem->refresh()->projeto_tim_id)->toBe($projeto->id);
    expect($projeto->ordensServico()->count())->toBe(1);
});

test('tim projeto can be created with oc and os fam codes', function () {
    $estacao = Estacao::factory()->create();

    Livewire::test(Create::class)
        ->set('nome', 'Implantação RAN TIM')
        ->set('status', 'Em andamento')
        ->set('oc', 'OC-2026-001')
        ->set('os_fam_entrega', 'FAM-ENT-001')
        ->set('os_fam_instalacao', 'FAM-INST-001')
        ->set('os_fam_panoramica', 'FAM-PAN-001')
        ->set('os_fam_desinstalacao', 'FAM-DES-001')
        ->set('estacao_id', $estacao->id)
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('tim_projetos', [
        'nome' => 'Implantação RAN TIM',
        'oc' => 'OC-2026-001',
        'os_fam_entrega' => 'FAM-ENT-001',
        'os_fam_instalacao' => 'FAM-INST-001',
        'os_fam_panoramica' => 'FAM-PAN-001',
        'os_fam_desinstalacao' => 'FAM-DES-001',
    ]);
});

test('tim projeto creation requires nome', function () {
    Livewire::test(Create::class)
        ->call('save')
        ->assertHasErrors(['nome']);
});

test('tim projeto creation requires estacao', function () {
    Livewire::test(Create::class)
        ->set('nome', 'Implantação RAN TIM')
        ->set('status', 'Em andamento')
        ->call('save')
        ->assertHasErrors(['estacao_id']);
});

test('tim projeto can be created with vinculated estacao', function () {
    $estacao = Estacao::factory()->create();

    Livewire::test(Create::class)
        ->set('nome', 'Implantação RAN TIM')
        ->set('status', 'Em andamento')
        ->set('estacao_id', $estacao->id)
        ->call('save')
        ->assertHasNoErrors();

    $projeto = TimProjeto::where('nome', 'Implantação RAN TIM')->first();

    expect($estacao->refresh()->projeto_tim_id)->toBe($projeto->id);
});

test('tim projeto creation ignores estacao already vinculada to another projeto', function () {
    $outroProjeto = TimProjeto::factory()->create();
    $estacao = Estacao::factory()->create(['projeto_tim_id' => $outroProjeto->id]);

    Livewire::test(Create::class)
        ->set('nome', 'Implantação RAN TIM')
        ->set('status', 'Em andamento')
        ->set('estacao_id', $estacao->id)
        ->call('save')
        ->assertHasNoErrors();

    expect($estacao->refresh()->projeto_tim_id)->toBe($outroProjeto->id);
});

test('tim projeto creation registers estacao vinculada historico', function () {
    $estacao = Estacao::factory()->create();

    Livewire::test(Create::class)
        ->set('nome', 'Implantação RAN TIM')
        ->set('status', 'Em andamento')
        ->set('estacao_id', $estacao->id)
        ->call('save')
        ->assertHasNoErrors();

    $projeto = TimProjeto::where('nome', 'Implantação RAN TIM')->first();

    expect($projeto->historicos()->where('tipo', 'estacao_vinculada')->count())->toBe(1);
});

test('tim projeto can be edited', function () {
    $projeto = TimProjeto::factory()->create(['codigo' => 'TIM-0001']);
    TimProjetoEtapa::create(['projeto_tim_id' => $projeto->id, 'etapa' => 'MOS', 'status' => 'Pendente', 'data_baseline' => '2026-06-01', 'data_planejada' => '2026-06-10', 'data_real' => '2026-06-12']);

    Livewire::test(Edit::class, ['projeto' => $projeto])
        ->set('nome', 'Projeto Atualizado')
        ->set('status', 'Concluído')
        ->set('baseline_mos', '2026-06-15')
        ->set('planejada_mos', '2026-06-20')
        ->set('real_mos', '2026-06-22')
        ->call('save')
        ->assertHasNoErrors();

    $projeto->refresh();

    expect($projeto->nome)->toBe('Projeto Atualizado');
    expect($projeto->status)->toBe('Concluído');

    $etapaMos = $projeto->etapas()->where('etapa', 'MOS')->first();

    expect($etapaMos->data_baseline?->format('Y-m-d'))->toBe('2026-06-15');
    expect($etapaMos->data_planejada?->format('Y-m-d'))->toBe('2026-06-20');
    expect($etapaMos->data_real?->format('Y-m-d'))->toBe('2026-06-22');
});

test('tim projeto can be deleted and detaches orders', function () {
    $projeto = TimProjeto::factory()->create(['codigo' => 'TIM-0001']);
    $ordem = OrdemServico::factory()->create(['projeto_tim_id' => $projeto->id]);

    Livewire::test(Index::class)
        ->call('destroy', $projeto->id);

    $this->assertDatabaseMissing('tim_projetos', ['id' => $projeto->id]);
    expect($ordem->refresh()->projeto_tim_id)->toBeNull();
});

test('tim show page displays vinculated orders', function () {
    $projeto = TimProjeto::factory()->create(['codigo' => 'TIM-0001']);
    OrdemServico::factory()->count(2)->create(['projeto_tim_id' => $projeto->id]);

    Livewire::test(Show::class, ['projeto' => $projeto])
        ->assertSee('TIM-0001')
        ->assertSee('Ordens de serviço vinculadas');
});

test('ordem can be vinculated to projeto', function () {
    $projeto = TimProjeto::factory()->create(['codigo' => 'TIM-0001']);
    $ordem = OrdemServico::factory()->create();

    Livewire::test(Show::class, ['projeto' => $projeto])
        ->call('vincular', $ordem->id);

    expect($ordem->refresh()->projeto_tim_id)->toBe($projeto->id);
});

test('ordem can be desvinculated from projeto', function () {
    $projeto = TimProjeto::factory()->create(['codigo' => 'TIM-0001']);
    $ordem = OrdemServico::factory()->create(['projeto_tim_id' => $projeto->id]);

    Livewire::test(Show::class, ['projeto' => $projeto])
        ->call('desvincular', $ordem->id);

    expect($ordem->refresh()->projeto_tim_id)->toBeNull();
});

test('tim projetos can be selected for bulk deletion', function () {
    $p1 = TimProjeto::factory()->create();
    $p2 = TimProjeto::factory()->create();

    Livewire::test(Index::class)
        ->call('alternarSelecao', $p1->id)
        ->call('alternarSelecao', $p2->id)
        ->assertSet('selecionados', [$p1->id, $p2->id])
        ->call('excluirSelecionados')
        ->assertSet('selecionados', []);

    $this->assertDatabaseMissing('tim_projetos', ['id' => $p1->id]);
    $this->assertDatabaseMissing('tim_projetos', ['id' => $p2->id]);
});

test('tim projetos can be exported as excel', function () {
    TimProjeto::factory()->create(['codigo' => 'TIM-0001', 'nome' => 'Projeto Export']);

    $response = Livewire::test(Index::class)
        ->call('exportarTodos');

    $response->assertStatus(200);
    expect($response->instance()->exportarTodos(app(ExcelExporter::class)))
        ->toBeInstanceOf(StreamedResponse::class);
});

test('unauthenticated user cannot access tim', function () {
    auth()->logout();

    $this->get(route('tim.index'))->assertRedirect(route('login'));
    $this->get(route('tim.create'))->assertRedirect(route('login'));
});

test('relatorio can be created with linked os and estacao', function () {
    $projeto = TimProjeto::factory()->create(['codigo' => 'TIM-0001']);
    $ordem = OrdemServico::factory()->create();
    $estacao = Estacao::factory()->create();

    Livewire::test(RelatorioCreate::class, ['projeto' => $projeto])
        ->set('ordem_servico_id', $ordem->id)
        ->set('estacao_id', $estacao->id)
        ->set('data_inicio', '2026-05-01')
        ->set('data_planejada', '2026-05-15')
        ->set('data_real', '2026-05-14')
        ->set('status', 'Em andamento')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('tim_relatorios', [
        'projeto_tim_id' => $projeto->id,
        'ordem_servico_id' => $ordem->id,
        'estacao_id' => $estacao->id,
        'status' => 'Em andamento',
    ]);

    $relatorio = TimRelatorio::where('projeto_tim_id', $projeto->id)->first();

    expect($relatorio->data_inicio?->format('Y-m-d'))->toBe('2026-05-01');
    expect($relatorio->data_planejada?->format('Y-m-d'))->toBe('2026-05-15');
    expect($relatorio->data_real?->format('Y-m-d'))->toBe('2026-05-14');
});

test('relatorio creation requires os and estacao', function () {
    $projeto = TimProjeto::factory()->create();

    Livewire::test(RelatorioCreate::class, ['projeto' => $projeto])
        ->call('save')
        ->assertHasErrors(['ordem_servico_id', 'estacao_id']);
});

test('relatorio show page displays vinculados', function () {
    $projeto = TimProjeto::factory()->create();
    $ordem = OrdemServico::factory()->create();
    $estacao = Estacao::factory()->create();
    $relatorio = TimRelatorio::create([
        'projeto_tim_id' => $projeto->id,
        'ordem_servico_id' => $ordem->id,
        'estacao_id' => $estacao->id,
        'status' => 'Pendente',
        'ativo' => true,
    ]);

    Livewire::test(RelatorioShow::class, ['projeto' => $projeto, 'relatorio' => $relatorio])
        ->assertSee($ordem->codigo)
        ->assertSee($estacao->site_id)
        ->assertSee('Início')
        ->assertSee('Planejada')
        ->assertSee('Real');
});

test('relatorio status can be updated', function () {
    $projeto = TimProjeto::factory()->create();
    $relatorio = TimRelatorio::create([
        'projeto_tim_id' => $projeto->id,
        'ordem_servico_id' => null,
        'estacao_id' => null,
        'status' => 'Pendente',
        'ativo' => true,
    ]);

    Livewire::test(RelatorioShow::class, ['projeto' => $projeto, 'relatorio' => $relatorio])
        ->call('atualizarStatus', 'Concluído')
        ->assertHasNoErrors();

    expect($relatorio->refresh()->status)->toBe('Concluído');
    expect($relatorio->data_real)->not->toBeNull();
});

test('relatorio can be deleted', function () {
    $projeto = TimProjeto::factory()->create();
    $relatorio = TimRelatorio::create([
        'projeto_tim_id' => $projeto->id,
        'ordem_servico_id' => null,
        'estacao_id' => null,
        'status' => 'Pendente',
        'ativo' => true,
    ]);

    Livewire::test(RelatorioShow::class, ['projeto' => $projeto, 'relatorio' => $relatorio])
        ->call('destroy');

    $this->assertDatabaseMissing('tim_relatorios', ['id' => $relatorio->id]);
});

test('projeto creation defaults data inicio to real mos and data fim to real rfa', function () {
    $estacao = Estacao::factory()->create();

    Livewire::test(Create::class)
        ->set('nome', 'Implantação RAN TIM')
        ->set('status', 'Em andamento')
        ->set('estacao_id', $estacao->id)
        ->set('real_mos', '2026-06-12')
        ->set('real_rfa', '2026-09-05')
        ->call('save')
        ->assertHasNoErrors();

    $projeto = TimProjeto::where('nome', 'Implantação RAN TIM')->first();

    expect($projeto->data_inicio?->format('Y-m-d'))->toBe('2026-06-12');
    expect($projeto->data_fim?->format('Y-m-d'))->toBe('2026-09-05');
});

test('projeto creation keeps explicit data inicio and fim over etapa reals', function () {
    $estacao = Estacao::factory()->create();

    Livewire::test(Create::class)
        ->set('nome', 'Implantação RAN TIM')
        ->set('status', 'Em andamento')
        ->set('estacao_id', $estacao->id)
        ->set('data_inicio', '2026-05-01')
        ->set('data_fim', '2026-10-01')
        ->set('real_mos', '2026-06-12')
        ->set('real_rfa', '2026-09-05')
        ->call('save')
        ->assertHasNoErrors();

    $projeto = TimProjeto::where('nome', 'Implantação RAN TIM')->first();

    expect($projeto->data_inicio?->format('Y-m-d'))->toBe('2026-05-01');
    expect($projeto->data_fim?->format('Y-m-d'))->toBe('2026-10-01');
});

test('projeto is created with five etapas', function () {
    $estacao = Estacao::factory()->create();

    Livewire::test(Create::class)
        ->set('nome', 'Implantação RAN TIM')
        ->set('status', 'Em andamento')
        ->set('estacao_id', $estacao->id)
        ->set('baseline_mos', '2026-06-01')
        ->set('baseline_instalacao', '2026-07-01')
        ->set('baseline_integracao', '2026-08-01')
        ->set('baseline_rfa', '2026-09-01')
        ->call('save')
        ->assertHasNoErrors();

    $projeto = TimProjeto::where('nome', 'Implantação RAN TIM')->first();

    expect($projeto->etapas()->count())->toBe(5);
    expect($projeto->etapas()->pluck('etapa')->all())->toBe(TimProjeto::ETAPAS);
});

test('projeto creation saves etapa baselines, planejada and real dates', function () {
    $estacao = Estacao::factory()->create();

    Livewire::test(Create::class)
        ->set('nome', 'Implantação RAN TIM')
        ->set('status', 'Em andamento')
        ->set('estacao_id', $estacao->id)
        ->set('baseline_mos', '2026-06-01')
        ->set('planejada_mos', '2026-06-10')
        ->set('real_mos', '2026-06-12')
        ->set('baseline_instalacao', '2026-07-01')
        ->set('planejada_instalacao', '2026-07-10')
        ->set('real_rfa', '2026-09-05')
        ->call('save')
        ->assertHasNoErrors();

    $projeto = TimProjeto::where('nome', 'Implantação RAN TIM')->first();

    expect($projeto->etapas()->where('etapa', 'MOS')->first()->data_baseline?->format('Y-m-d'))->toBe('2026-06-01');
    expect($projeto->etapas()->where('etapa', 'MOS')->first()->data_planejada?->format('Y-m-d'))->toBe('2026-06-10');
    expect($projeto->etapas()->where('etapa', 'MOS')->first()->data_real?->format('Y-m-d'))->toBe('2026-06-12');
    expect($projeto->etapas()->where('etapa', 'Instalação')->first()->data_baseline?->format('Y-m-d'))->toBe('2026-07-01');
    expect($projeto->etapas()->where('etapa', 'Instalação')->first()->data_planejada?->format('Y-m-d'))->toBe('2026-07-10');
    expect($projeto->etapas()->where('etapa', 'RFA')->first()->data_real?->format('Y-m-d'))->toBe('2026-09-05');
    expect($projeto->etapas()->where('etapa', 'Documentação')->first()->data_baseline)->toBeNull();
    expect($projeto->etapas()->where('etapa', 'Documentação')->first()->data_planejada)->toBeNull();
    expect($projeto->etapas()->where('etapa', 'Documentação')->first()->data_real)->toBeNull();
});

test('projeto show page displays etapas', function () {
    $projeto = TimProjeto::factory()->create(['codigo' => 'TIM-0001']);
    TimProjetoEtapa::create(['projeto_tim_id' => $projeto->id, 'etapa' => 'MOS', 'status' => 'Pendente']);
    TimProjetoEtapa::create(['projeto_tim_id' => $projeto->id, 'etapa' => 'Instalação', 'status' => 'Pendente']);

    Livewire::test(Show::class, ['projeto' => $projeto])
        ->assertSee('MOS')
        ->assertSee('Instalação')
        ->assertSee('Etapas')
        ->assertSee('Relatórios');
});

test('etapa can be advanced', function () {
    $projeto = TimProjeto::factory()->create(['codigo' => 'TIM-0001']);
    $etapa = TimProjetoEtapa::create(['projeto_tim_id' => $projeto->id, 'etapa' => 'MOS', 'status' => 'Pendente']);

    Livewire::test(Show::class, ['projeto' => $projeto])
        ->call('avancarEtapa', $etapa->id)
        ->assertHasNoErrors();

    expect($etapa->refresh()->status)->toBe('Em andamento');
});

test('etapa can be advanced to concluida and records date', function () {
    $projeto = TimProjeto::factory()->create(['codigo' => 'TIM-0001']);
    $etapa = TimProjetoEtapa::create(['projeto_tim_id' => $projeto->id, 'etapa' => 'MOS', 'status' => 'Em andamento']);

    Livewire::test(Show::class, ['projeto' => $projeto])
        ->call('avancarEtapa', $etapa->id)
        ->assertHasNoErrors();

    expect($etapa->refresh()->status)->toBe('Concluída');
    expect($etapa->data_conclusao)->not->toBeNull();
});

test('etapa cannot advance beyond concluida', function () {
    $projeto = TimProjeto::factory()->create(['codigo' => 'TIM-0001']);
    $etapa = TimProjetoEtapa::create(['projeto_tim_id' => $projeto->id, 'etapa' => 'MOS', 'status' => 'Concluída']);

    Livewire::test(Show::class, ['projeto' => $projeto])
        ->call('avancarEtapa', $etapa->id);

    expect($etapa->refresh()->status)->toBe('Concluída');
});

test('etapa can be reverted', function () {
    $projeto = TimProjeto::factory()->create(['codigo' => 'TIM-0001']);
    $etapa = TimProjetoEtapa::create(['projeto_tim_id' => $projeto->id, 'etapa' => 'MOS', 'status' => 'Em andamento']);

    Livewire::test(Show::class, ['projeto' => $projeto])
        ->call('retrocederEtapa', $etapa->id)
        ->assertHasNoErrors();

    expect($etapa->refresh()->status)->toBe('Pendente');
});

test('etapa cannot revert before pendente', function () {
    $projeto = TimProjeto::factory()->create(['codigo' => 'TIM-0001']);
    $etapa = TimProjetoEtapa::create(['projeto_tim_id' => $projeto->id, 'etapa' => 'MOS', 'status' => 'Pendente']);

    Livewire::test(Show::class, ['projeto' => $projeto])
        ->call('retrocederEtapa', $etapa->id);

    expect($etapa->refresh()->status)->toBe('Pendente');
});

test('projeto creation registers historico', function () {
    $estacao = Estacao::factory()->create();

    Livewire::test(Create::class)
        ->set('nome', 'Implantação RAN TIM')
        ->set('status', 'Em andamento')
        ->set('estacao_id', $estacao->id)
        ->call('save')
        ->assertHasNoErrors();

    $projeto = TimProjeto::where('nome', 'Implantação RAN TIM')->first();

    expect($projeto->historicos()->count())->toBe(3);
    expect($projeto->historicos()->orderBy('id')->first()->tipo)->toBe('criacao');
    expect($projeto->historicos()->where('tipo', 'os_vinculada')->count())->toBe(1);
});

test('vincular and desvincular register historico', function () {
    $projeto = TimProjeto::factory()->create(['codigo' => 'TIM-0001']);
    $ordem = OrdemServico::factory()->create();

    Livewire::test(Show::class, ['projeto' => $projeto])
        ->call('vincular', $ordem->id)
        ->assertHasNoErrors();

    expect($projeto->historicos()->where('tipo', 'os_vinculada')->count())->toBe(1);

    Livewire::test(Show::class, ['projeto' => $projeto])
        ->call('desvincular', $ordem->id)
        ->assertHasNoErrors();

    expect($projeto->historicos()->where('tipo', 'os_desvinculada')->count())->toBe(1);
});

test('etapa change registers historico', function () {
    $projeto = TimProjeto::factory()->create(['codigo' => 'TIM-0001']);
    $etapa = TimProjetoEtapa::create(['projeto_tim_id' => $projeto->id, 'etapa' => 'MOS', 'status' => 'Pendente']);

    Livewire::test(Show::class, ['projeto' => $projeto])
        ->call('avancarEtapa', $etapa->id)
        ->assertHasNoErrors();

    $historico = $projeto->historicos()->where('tipo', 'etapa_alterada')->first();

    expect($historico)->not->toBeNull();
    expect($historico->descricao)->toBe('MOS: Pendente → Em andamento');
});

test('relatorio creation registers historico', function () {
    $projeto = TimProjeto::factory()->create(['codigo' => 'TIM-0001']);
    $ordem = OrdemServico::factory()->create();
    $estacao = Estacao::factory()->create();

    Livewire::test(RelatorioCreate::class, ['projeto' => $projeto])
        ->set('ordem_servico_id', $ordem->id)
        ->set('estacao_id', $estacao->id)
        ->call('save')
        ->assertHasNoErrors();

    expect($projeto->historicos()->where('tipo', 'relatorio_criado')->count())->toBe(1);
});

test('show page displays historico aside', function () {
    $projeto = TimProjeto::factory()->create(['codigo' => 'TIM-0001']);
    $projeto->registrarHistorico('criacao', 'Projeto criado');

    Livewire::test(Show::class, ['projeto' => $projeto])
        ->assertSee('Histórico')
        ->assertSee('Projeto criado');
});

test('projeto can be created with anexos', function () {
    Storage::fake('local');

    $estacao = Estacao::factory()->create();

    Livewire::test(Create::class)
        ->set('nome', 'Implantação RAN TIM')
        ->set('status', 'Em andamento')
        ->set('estacao_id', $estacao->id)
        ->set('anexos_tssr', [UploadedFile::fake()->create('tssr.pdf', 100)])
        ->set('anexos_docd', [UploadedFile::fake()->create('docd.xlsx', 100)])
        ->set('anexos_notas_fiscais', [
            UploadedFile::fake()->create('nota1.pdf', 100),
            UploadedFile::fake()->create('nota2.pdf', 100),
        ])
        ->call('save')
        ->assertHasNoErrors();

    $projeto = TimProjeto::where('nome', 'Implantação RAN TIM')->first();

    expect($projeto->anexos)->toHaveCount(4);
    expect($projeto->anexos()->where('categoria', 'TSSR')->count())->toBe(1);
    expect($projeto->anexos()->where('categoria', 'DOC-D')->count())->toBe(1);
    expect($projeto->anexos()->where('categoria', 'Notas Fiscais')->count())->toBe(2);

    foreach ($projeto->anexos as $anexo) {
        Storage::disk('local')->assertExists($anexo->arquivo);
    }
});

test('anexo can be downloaded', function () {
    Storage::fake('local');

    $projeto = TimProjeto::factory()->create(['codigo' => 'TIM-0001']);
    $anexo = $projeto->anexos()->create([
        'categoria' => 'TSSR',
        'nome' => 'tssr.pdf',
        'arquivo' => 'anexos/projeto/'.$projeto->id.'/tssr.pdf',
        'mime' => 'application/pdf',
        'tamanho' => 100,
    ]);

    Storage::disk('local')->put($anexo->arquivo, 'conteudo do arquivo');

    $this->get(route('tim.anexos.download', $anexo))
        ->assertOk()
        ->assertDownload($anexo->nome);
});

test('anexo can be removed', function () {
    Storage::fake('local');

    $projeto = TimProjeto::factory()->create(['codigo' => 'TIM-0001']);
    $anexo = $projeto->anexos()->create([
        'categoria' => 'TSSR',
        'nome' => 'tssr.pdf',
        'arquivo' => 'anexos/projeto/'.$projeto->id.'/tssr.pdf',
        'mime' => 'application/pdf',
    ]);

    Storage::disk('local')->put($anexo->arquivo, 'conteudo do arquivo');

    Livewire::test(Show::class, ['projeto' => $projeto])
        ->call('removerAnexo', $anexo->id)
        ->assertHasNoErrors();

    expect($projeto->anexos()->count())->toBe(0);
    Storage::disk('local')->assertMissing($anexo->arquivo);
});

test('projeto deletes anexo files', function () {
    Storage::fake('local');

    $projeto = TimProjeto::factory()->create(['codigo' => 'TIM-0001']);
    $anexo = $projeto->anexos()->create([
        'categoria' => 'TSSR',
        'nome' => 'tssr.pdf',
        'arquivo' => 'anexos/projeto/'.$projeto->id.'/tssr.pdf',
        'mime' => 'application/pdf',
    ]);

    Storage::disk('local')->put($anexo->arquivo, 'conteudo do arquivo');

    Livewire::test(Show::class, ['projeto' => $projeto])
        ->call('destroy');

    expect(TimProjeto::find($projeto->id))->toBeNull();
    Storage::disk('local')->assertMissing($anexo->arquivo);
});