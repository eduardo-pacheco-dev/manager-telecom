<?php

use App\Livewire\Nokia\Create;
use App\Livewire\Nokia\Edit;
use App\Livewire\Nokia\Index;
use App\Livewire\Nokia\RelatorioCreate;
use App\Livewire\Nokia\RelatorioShow;
use App\Livewire\Nokia\Show;
use App\Models\Estacao;
use App\Models\NokiaProjeto;
use App\Models\NokiaProjetoEtapa;
use App\Models\NokiaRelatorio;
use App\Models\OrdemServico;
use App\Models\User;
use App\Services\ExcelExporter;
use Livewire\Livewire;
use Symfony\Component\HttpFoundation\StreamedResponse;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('nokia index page is displayed', function () {
    NokiaProjeto::factory()->count(3)->create();

    $this->get(route('nokia.index'))->assertOk();
});

test('nokia index shows list and stats', function () {
    NokiaProjeto::factory()->count(3)->create();

    Livewire::test(Index::class)
        ->assertSee('Projetos Nokia')
        ->assertSee('Total de projetos')
        ->assertSee('Ativos')
        ->assertSee('OS vinculadas');
});

test('nokia projeto can be created', function () {
    $estacao = Estacao::factory()->create();

    Livewire::test(Create::class)
        ->set('nome', 'Implantação RAN TIM')
        ->set('status', 'Em andamento')
        ->set('data_inicio', '2026-05-01')
        ->set('estacao_id', $estacao->id)
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('nokia_projetos', [
        'codigo' => 'NOK-0001',
        'nome' => 'Implantação RAN TIM',
        'status' => 'Em andamento',
    ]);
});

test('nokia projeto can be created with oc and os fam codes', function () {
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

    $this->assertDatabaseHas('nokia_projetos', [
        'nome' => 'Implantação RAN TIM',
        'oc' => 'OC-2026-001',
        'os_fam_entrega' => 'FAM-ENT-001',
        'os_fam_instalacao' => 'FAM-INST-001',
        'os_fam_panoramica' => 'FAM-PAN-001',
        'os_fam_desinstalacao' => 'FAM-DES-001',
    ]);
});

test('nokia projeto creation requires nome', function () {
    Livewire::test(Create::class)
        ->call('save')
        ->assertHasErrors(['nome']);
});

test('nokia projeto creation requires estacao', function () {
    Livewire::test(Create::class)
        ->set('nome', 'Implantação RAN TIM')
        ->set('status', 'Em andamento')
        ->call('save')
        ->assertHasErrors(['estacao_id']);
});

test('nokia projeto can be created with vinculated estacao', function () {
    $estacao = Estacao::factory()->create();

    Livewire::test(Create::class)
        ->set('nome', 'Implantação RAN TIM')
        ->set('status', 'Em andamento')
        ->set('estacao_id', $estacao->id)
        ->call('save')
        ->assertHasNoErrors();

    $projeto = NokiaProjeto::where('nome', 'Implantação RAN TIM')->first();

    expect($estacao->refresh()->projeto_nokia_id)->toBe($projeto->id);
});

test('nokia projeto creation ignores estacao already vinculada to another projeto', function () {
    $outroProjeto = NokiaProjeto::factory()->create();
    $estacao = Estacao::factory()->create(['projeto_nokia_id' => $outroProjeto->id]);

    Livewire::test(Create::class)
        ->set('nome', 'Implantação RAN TIM')
        ->set('status', 'Em andamento')
        ->set('estacao_id', $estacao->id)
        ->call('save')
        ->assertHasNoErrors();

    expect($estacao->refresh()->projeto_nokia_id)->toBe($outroProjeto->id);
});

test('nokia projeto creation registers estacao vinculada historico', function () {
    $estacao = Estacao::factory()->create();

    Livewire::test(Create::class)
        ->set('nome', 'Implantação RAN TIM')
        ->set('status', 'Em andamento')
        ->set('estacao_id', $estacao->id)
        ->call('save')
        ->assertHasNoErrors();

    $projeto = NokiaProjeto::where('nome', 'Implantação RAN TIM')->first();

    expect($projeto->historicos()->where('tipo', 'estacao_vinculada')->count())->toBe(1);
});

test('nokia projeto can be edited', function () {
    $projeto = NokiaProjeto::factory()->create(['codigo' => 'NOK-0001']);
    NokiaProjetoEtapa::create(['projeto_nokia_id' => $projeto->id, 'etapa' => 'MOS', 'status' => 'Pendente', 'data_baseline' => '2026-06-01', 'data_planejada' => '2026-06-10', 'data_real' => '2026-06-12']);

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

test('nokia projeto can be deleted and detaches orders', function () {
    $projeto = NokiaProjeto::factory()->create(['codigo' => 'NOK-0001']);
    $ordem = OrdemServico::factory()->create(['projeto_nokia_id' => $projeto->id]);

    Livewire::test(Index::class)
        ->call('destroy', $projeto->id);

    $this->assertDatabaseMissing('nokia_projetos', ['id' => $projeto->id]);
    expect($ordem->refresh()->projeto_nokia_id)->toBeNull();
});

test('nokia show page displays vinculated orders', function () {
    $projeto = NokiaProjeto::factory()->create(['codigo' => 'NOK-0001']);
    OrdemServico::factory()->count(2)->create(['projeto_nokia_id' => $projeto->id]);

    Livewire::test(Show::class, ['projeto' => $projeto])
        ->assertSee('NOK-0001')
        ->assertSee('Ordens de serviço vinculadas');
});

test('ordem can be vinculated to projeto', function () {
    $projeto = NokiaProjeto::factory()->create(['codigo' => 'NOK-0001']);
    $ordem = OrdemServico::factory()->create();

    Livewire::test(Show::class, ['projeto' => $projeto])
        ->call('vincular', $ordem->id);

    expect($ordem->refresh()->projeto_nokia_id)->toBe($projeto->id);
});

test('ordem can be desvinculated from projeto', function () {
    $projeto = NokiaProjeto::factory()->create(['codigo' => 'NOK-0001']);
    $ordem = OrdemServico::factory()->create(['projeto_nokia_id' => $projeto->id]);

    Livewire::test(Show::class, ['projeto' => $projeto])
        ->call('desvincular', $ordem->id);

    expect($ordem->refresh()->projeto_nokia_id)->toBeNull();
});

test('nokia projetos can be selected for bulk deletion', function () {
    $p1 = NokiaProjeto::factory()->create();
    $p2 = NokiaProjeto::factory()->create();

    Livewire::test(Index::class)
        ->call('alternarSelecao', $p1->id)
        ->call('alternarSelecao', $p2->id)
        ->assertSet('selecionados', [$p1->id, $p2->id])
        ->call('excluirSelecionados')
        ->assertSet('selecionados', []);

    $this->assertDatabaseMissing('nokia_projetos', ['id' => $p1->id]);
    $this->assertDatabaseMissing('nokia_projetos', ['id' => $p2->id]);
});

test('nokia projetos can be exported as excel', function () {
    NokiaProjeto::factory()->create(['codigo' => 'NOK-0001', 'nome' => 'Projeto Export']);

    $response = Livewire::test(Index::class)
        ->call('exportarTodos');

    $response->assertStatus(200);
    expect($response->instance()->exportarTodos(app(ExcelExporter::class)))
        ->toBeInstanceOf(StreamedResponse::class);
});

test('unauthenticated user cannot access nokia', function () {
    auth()->logout();

    $this->get(route('nokia.index'))->assertRedirect(route('login'));
    $this->get(route('nokia.create'))->assertRedirect(route('login'));
});

test('relatorio can be created with linked os and estacao', function () {
    $projeto = NokiaProjeto::factory()->create(['codigo' => 'NOK-0001']);
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

    $this->assertDatabaseHas('nokia_relatorios', [
        'projeto_nokia_id' => $projeto->id,
        'ordem_servico_id' => $ordem->id,
        'estacao_id' => $estacao->id,
        'status' => 'Em andamento',
    ]);

    $relatorio = NokiaRelatorio::where('projeto_nokia_id', $projeto->id)->first();

    expect($relatorio->data_inicio?->format('Y-m-d'))->toBe('2026-05-01');
    expect($relatorio->data_planejada?->format('Y-m-d'))->toBe('2026-05-15');
    expect($relatorio->data_real?->format('Y-m-d'))->toBe('2026-05-14');
});

test('relatorio creation requires os and estacao', function () {
    $projeto = NokiaProjeto::factory()->create();

    Livewire::test(RelatorioCreate::class, ['projeto' => $projeto])
        ->call('save')
        ->assertHasErrors(['ordem_servico_id', 'estacao_id']);
});

test('relatorio show page displays vinculados', function () {
    $projeto = NokiaProjeto::factory()->create();
    $ordem = OrdemServico::factory()->create();
    $estacao = Estacao::factory()->create();
    $relatorio = NokiaRelatorio::create([
        'projeto_nokia_id' => $projeto->id,
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
    $projeto = NokiaProjeto::factory()->create();
    $relatorio = NokiaRelatorio::create([
        'projeto_nokia_id' => $projeto->id,
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
    $projeto = NokiaProjeto::factory()->create();
    $relatorio = NokiaRelatorio::create([
        'projeto_nokia_id' => $projeto->id,
        'ordem_servico_id' => null,
        'estacao_id' => null,
        'status' => 'Pendente',
        'ativo' => true,
    ]);

    Livewire::test(RelatorioShow::class, ['projeto' => $projeto, 'relatorio' => $relatorio])
        ->call('destroy');

    $this->assertDatabaseMissing('nokia_relatorios', ['id' => $relatorio->id]);
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

    $projeto = NokiaProjeto::where('nome', 'Implantação RAN TIM')->first();

    expect($projeto->etapas()->count())->toBe(5);
    expect($projeto->etapas()->pluck('etapa')->all())->toBe(NokiaProjeto::ETAPAS);
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

    $projeto = NokiaProjeto::where('nome', 'Implantação RAN TIM')->first();

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
    $projeto = NokiaProjeto::factory()->create(['codigo' => 'NOK-0001']);
    NokiaProjetoEtapa::create(['projeto_nokia_id' => $projeto->id, 'etapa' => 'MOS', 'status' => 'Pendente']);
    NokiaProjetoEtapa::create(['projeto_nokia_id' => $projeto->id, 'etapa' => 'Instalação', 'status' => 'Pendente']);

    Livewire::test(Show::class, ['projeto' => $projeto])
        ->assertSee('MOS')
        ->assertSee('Instalação')
        ->assertSee('Etapas')
        ->assertSee('Relatórios');
});

test('etapa can be advanced', function () {
    $projeto = NokiaProjeto::factory()->create(['codigo' => 'NOK-0001']);
    $etapa = NokiaProjetoEtapa::create(['projeto_nokia_id' => $projeto->id, 'etapa' => 'MOS', 'status' => 'Pendente']);

    Livewire::test(Show::class, ['projeto' => $projeto])
        ->call('avancarEtapa', $etapa->id)
        ->assertHasNoErrors();

    expect($etapa->refresh()->status)->toBe('Em andamento');
});

test('etapa can be advanced to concluida and records date', function () {
    $projeto = NokiaProjeto::factory()->create(['codigo' => 'NOK-0001']);
    $etapa = NokiaProjetoEtapa::create(['projeto_nokia_id' => $projeto->id, 'etapa' => 'MOS', 'status' => 'Em andamento']);

    Livewire::test(Show::class, ['projeto' => $projeto])
        ->call('avancarEtapa', $etapa->id)
        ->assertHasNoErrors();

    expect($etapa->refresh()->status)->toBe('Concluída');
    expect($etapa->data_conclusao)->not->toBeNull();
});

test('etapa cannot advance beyond concluida', function () {
    $projeto = NokiaProjeto::factory()->create(['codigo' => 'NOK-0001']);
    $etapa = NokiaProjetoEtapa::create(['projeto_nokia_id' => $projeto->id, 'etapa' => 'MOS', 'status' => 'Concluída']);

    Livewire::test(Show::class, ['projeto' => $projeto])
        ->call('avancarEtapa', $etapa->id);

    expect($etapa->refresh()->status)->toBe('Concluída');
});

test('etapa can be reverted', function () {
    $projeto = NokiaProjeto::factory()->create(['codigo' => 'NOK-0001']);
    $etapa = NokiaProjetoEtapa::create(['projeto_nokia_id' => $projeto->id, 'etapa' => 'MOS', 'status' => 'Em andamento']);

    Livewire::test(Show::class, ['projeto' => $projeto])
        ->call('retrocederEtapa', $etapa->id)
        ->assertHasNoErrors();

    expect($etapa->refresh()->status)->toBe('Pendente');
});

test('etapa cannot revert before pendente', function () {
    $projeto = NokiaProjeto::factory()->create(['codigo' => 'NOK-0001']);
    $etapa = NokiaProjetoEtapa::create(['projeto_nokia_id' => $projeto->id, 'etapa' => 'MOS', 'status' => 'Pendente']);

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

    $projeto = NokiaProjeto::where('nome', 'Implantação RAN TIM')->first();

    expect($projeto->historicos()->count())->toBe(2);
    expect($projeto->historicos()->orderBy('id')->first()->tipo)->toBe('criacao');
});

test('vincular and desvincular register historico', function () {
    $projeto = NokiaProjeto::factory()->create(['codigo' => 'NOK-0001']);
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
    $projeto = NokiaProjeto::factory()->create(['codigo' => 'NOK-0001']);
    $etapa = NokiaProjetoEtapa::create(['projeto_nokia_id' => $projeto->id, 'etapa' => 'MOS', 'status' => 'Pendente']);

    Livewire::test(Show::class, ['projeto' => $projeto])
        ->call('avancarEtapa', $etapa->id)
        ->assertHasNoErrors();

    $historico = $projeto->historicos()->where('tipo', 'etapa_alterada')->first();

    expect($historico)->not->toBeNull();
    expect($historico->descricao)->toBe('MOS: Pendente → Em andamento');
});

test('relatorio creation registers historico', function () {
    $projeto = NokiaProjeto::factory()->create(['codigo' => 'NOK-0001']);
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
    $projeto = NokiaProjeto::factory()->create(['codigo' => 'NOK-0001']);
    $projeto->registrarHistorico('criacao', 'Projeto criado');

    Livewire::test(Show::class, ['projeto' => $projeto])
        ->assertSee('Histórico')
        ->assertSee('Projeto criado');
});
