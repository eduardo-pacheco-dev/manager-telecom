<?php

namespace App\Livewire\Usuarios;

use App\Models\User;
use App\Services\ExcelExporter;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Title('UsuÃ¡rios')]
class Index extends Component
{
    use WithPagination;

    private const SORTABLE = ['name', 'email', 'role', 'ativo', 'created_at'];

    public string $search = '';

    public string $filtroRole = '';

    public string $filtroStatus = '';

    public string $sortField = 'name';

    public string $sortDirection = 'asc';

    public int $perPage = 10;

    public ?int $usuarioParaExcluir = null;

    /** @var array<int, int> */
    public array $selecionados = [];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroRole(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroStatus(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $campo): void
    {
        if (! in_array($campo, self::SORTABLE, true)) {
            return;
        }

        if ($this->sortField === $campo) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $campo;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function toggleAtivo(User $usuario): void
    {
        if ($usuario->id === auth()->id()) {
            $this->dispatch('flux-toast', text: __('VocÃª nÃ£o pode alterar o prÃ³prio status.'), variant: 'danger');

            return;
        }

        $usuario->update(['ativo' => ! $usuario->ativo]);

        $this->dispatch('usuario-updated');
    }

    public function alternarSelecao(int $id): void
    {
        if (in_array($id, $this->selecionados, true)) {
            $this->selecionados = array_values(array_diff($this->selecionados, [$id]));
        } else {
            $this->selecionados[] = $id;
        }
    }

    public function selecionarTodosDaPagina(): void
    {
        $idsPagina = $this->usuarios()->pluck('id')->all();

        $todosSelecionados = array_diff($idsPagina, $this->selecionados) === [];

        $this->selecionados = $todosSelecionados
            ? array_values(array_diff($this->selecionados, $idsPagina))
            : array_values(array_unique(array_merge($this->selecionados, $idsPagina)));
    }

    public function limparSelecao(): void
    {
        $this->selecionados = [];
    }

    public function ativarSelecionados(): void
    {
        if ($this->selecionados === []) {
            return;
        }

        $ids = array_values(array_diff($this->selecionados, [auth()->id()]));

        if ($ids === []) {
            return;
        }

        User::whereIn('id', $ids)->update(['ativo' => true]);

        $this->limparSelecao();
        $this->dispatch('usuario-updated');
    }

    public function desativarSelecionados(): void
    {
        if ($this->selecionados === []) {
            return;
        }

        $ids = array_values(array_diff($this->selecionados, [auth()->id()]));

        if ($ids === []) {
            return;
        }

        User::whereIn('id', $ids)->update(['ativo' => false]);

        $this->limparSelecao();
        $this->dispatch('usuario-updated');
    }

    public function excluirSelecionados(): void
    {
        if ($this->selecionados === []) {
            return;
        }

        $ids = array_values(array_diff($this->selecionados, [auth()->id()]));

        if ($ids === []) {
            return;
        }

        User::whereIn('id', $ids)->delete();

        $this->limparSelecao();
        $this->dispatch('usuario-deleted');
    }

    public function exportarSelecionados(ExcelExporter $exporter): StreamedResponse
    {
        if ($this->selecionados === []) {
            abort(422, __('Nenhum usuÃ¡rio selecionado.'));
        }

        $usuarios = User::whereIn('id', $this->selecionados)
            ->orderBy('name')
            ->get();

        return $exporter->download(
            'usuarios-selecionados.xlsx',
            $this->cabecalhoExportacao(),
            $this->linhasExportacao($usuarios),
        );
    }

    public function exportarTodos(ExcelExporter $exporter): StreamedResponse
    {
        $query = $this->queryUsuarios();

        return $exporter->download(
            'usuarios.xlsx',
            $this->cabecalhoExportacao(),
            $this->linhasExportacao($query->orderBy('name')->get()),
        );
    }

    /**
     * @return array<int, string>
     */
    private function cabecalhoExportacao(): array
    {
        return ['Nome', 'E-mail', 'Perfil', 'Status', 'Verificado em', 'Criado em'];
    }

    /**
     * @param  Collection<int, User>  $usuarios
     * @return array<int, array<int, mixed>>
     */
    private function linhasExportacao(Collection $usuarios): array
    {
        return $usuarios->map(function (User $usuario): array {
            return [
                $usuario->name,
                $usuario->email,
                $usuario->role === 'admin' ? 'Admin' : 'UsuÃ¡rio',
                $usuario->ativo ? 'Ativo' : 'Inativo',
                $usuario->email_verified_at?->format('d/m/Y'),
                $usuario->created_at?->format('d/m/Y'),
            ];
        })->all();
    }

    public function destroy(User $usuario): void
    {
        if ($usuario->id === auth()->id()) {
            $this->usuarioParaExcluir = null;
            $this->dispatch('flux-toast', text: __('VocÃª nÃ£o pode excluir o prÃ³prio usuÃ¡rio.'), variant: 'danger');

            return;
        }

        $usuario->delete();

        $this->usuarioParaExcluir = null;
        $this->limparSelecao();

        $this->dispatch('usuario-deleted');
    }

    /**
     * @return array<string, int>
     */
    #[Computed]
    public function stats(): array
    {
        return [
            'total' => User::query()->count(),
            'ativos' => User::query()->where('ativo', true)->count(),
            'admins' => User::query()->where('role', 'admin')->count(),
            'usuarios' => User::query()->where('role', 'user')->count(),
        ];
    }

    #[Computed]
    public function usuarioAlvo(): ?User
    {
        return $this->usuarioParaExcluir
            ? User::find($this->usuarioParaExcluir)
            : null;
    }

    /**
     * @return LengthAwarePaginator<int, User>
     */
    public function usuarios(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->queryUsuarios()
            ->orderBy($this->sortField, $this->sortDirection === 'desc' ? 'desc' : 'asc')
            ->paginate($this->perPage);
    }

    /**
     * @return Builder<User>
     */
    private function queryUsuarios(): Builder
    {
        return User::query()
            ->when($this->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($this->filtroRole !== '', function ($query) {
                $query->where('role', $this->filtroRole);
            })
            ->when($this->filtroStatus !== '', function ($query) {
                $query->where('ativo', $this->filtroStatus === 'ativo');
            });
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'filtroRole', 'filtroStatus']);

        $this->sortField = 'name';
        $this->sortDirection = 'asc';

        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.usuarios.index', [
            'usuarios' => $this->usuarios(),
        ]);
    }
}
