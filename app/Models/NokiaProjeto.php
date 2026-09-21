<?php

namespace App\Models;

use Database\Factories\NokiaProjetoFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $codigo
 * @property string $nome
 * @property string|null $descricao
 * @property string $status
 * @property string|null $oc
 * @property string|null $os_fam_entrega
 * @property string|null $os_fam_instalacao
 * @property string|null $os_fam_panoramica
 * @property string|null $os_fam_desinstalacao
 * @property Carbon|null $data_inicio
 * @property Carbon|null $data_fim
 * @property bool $ativo
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Collection<int, Estacao> $estacoes
 * @property Collection<int, OrdemServico> $ordensServico
 * @property Collection<int, NokiaRelatorio> $relatorios
 * @property Collection<int, NokiaProjetoEtapa> $etapas
 * @property Collection<int, NokiaProjetoHistorico> $historicos
 */
class NokiaProjeto extends Model
{
    public const STATUS = ['Planejamento', 'Em andamento', 'Pausado', 'Concluído', 'Cancelado'];

    public const ETAPAS = ['MOS', 'Instalação', 'Integração', 'Documentação', 'RFA'];

    public const ETAPAS_BASELINE = ['MOS', 'Instalação', 'Integração', 'RFA'];

    /** @use HasFactory<NokiaProjetoFactory> */
    use HasFactory;

    protected $table = 'nokia_projetos';

    protected $fillable = [
        'codigo', 'nome', 'descricao', 'status', 'oc',
        'os_fam_entrega', 'os_fam_instalacao', 'os_fam_panoramica', 'os_fam_desinstalacao',
        'data_inicio', 'data_fim', 'ativo',
    ];

    protected function casts(): array
    {
        return [
            'data_inicio' => 'date',
            'data_fim' => 'date',
            'ativo' => 'boolean',
        ];
    }

    /**
     * @return HasMany<Estacao, $this>
     */
    public function estacoes(): HasMany
    {
        return $this->hasMany(Estacao::class, 'projeto_nokia_id')->orderBy('site_id');
    }

    /**
     * @return HasMany<OrdemServico, $this>
     */
    public function ordensServico(): HasMany
    {
        return $this->hasMany(OrdemServico::class, 'projeto_nokia_id');
    }

    /**
     * @return HasMany<NokiaRelatorio, $this>
     */
    public function relatorios(): HasMany
    {
        return $this->hasMany(NokiaRelatorio::class, 'projeto_nokia_id');
    }

    /**
     * @return HasMany<NokiaProjetoEtapa, $this>
     */
    public function etapas(): HasMany
    {
        return $this->hasMany(NokiaProjetoEtapa::class, 'projeto_nokia_id')->orderBy('id');
    }

    /**
     * @return HasMany<NokiaProjetoHistorico, $this>
     */
    public function historicos(): HasMany
    {
        return $this->hasMany(NokiaProjetoHistorico::class, 'projeto_nokia_id')->orderByDesc('created_at');
    }

    public function registrarHistorico(string $tipo, string $descricao): void
    {
        $this->historicos()->create([
            'tipo' => $tipo,
            'descricao' => $descricao,
            'user_id' => auth()->id(),
        ]);
    }

    public function ensureEtapas(): void
    {
        $existentes = $this->etapas()->pluck('etapa')->all();

        foreach (self::ETAPAS as $etapa) {
            if (! in_array($etapa, $existentes, true)) {
                $this->etapas()->create([
                    'etapa' => $etapa,
                    'status' => 'Pendente',
                ]);
            }
        }
    }

    /**
     * @param  array<string, array{baseline?: string|null, planejada?: string|null, real?: string|null}>  $cronograma
     */
    public function atualizarCronogramaEtapas(array $cronograma): void
    {
        $etapas = $this->etapas()->get()->keyBy('etapa');

        foreach (self::ETAPAS_BASELINE as $etapa) {
            if (! isset($etapas[$etapa])) {
                continue;
            }

            $etapas[$etapa]->update([
                'data_baseline' => $cronograma[$etapa]['baseline'] ?? null,
                'data_planejada' => $cronograma[$etapa]['planejada'] ?? null,
                'data_real' => $cronograma[$etapa]['real'] ?? null,
            ]);
        }
    }
}
