<?php

namespace App\Livewire\User;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class Index extends Component
{
    use Toast, WithPagination, WithoutUrlPagination;

    public string $search = '';
    public bool $drawer = false;
    public array $sortBy = ['column' => 'name', 'direction' => 'asc'];

    public function render()
    {
        return view('livewire.user.index', [
            'users' => $this->users(),
        ]);
    }
    public function users()
    {
        return User::with('profile')->when($this->search, function ($query) {
            $query->where('name', 'like', '%'.$this->search.'%')->orWhere('slug', 'like', '%'.$this->search.'%')->orWhere('email', 'like', '%'.$this->search.'%');
        })->orderBy(...array_values($this->sortBy))->select(['id', 'name', 'slug', 'email', 'avatar'])->simplePaginate(10);
    }
    public function clear(): void
    {
        $this->reset();
        $this->resetPage();
        $this->success('Filters cleared.', position: 'toast-bottom');
    }
    #[Computed(cache: true)]
    public function headers(): array
    {
        $columns = Schema::getColumnListing('users');
        $headers = [];
        foreach ($columns as $column) {
            if (in_array($column, ['name', 'email', 'slug'])) {
                $headers[] = ['key' => $column, 'label' => ucfirst(str_replace('_','', $column)), 'class' => 'w-20 text-base-content'];
            }
        }
        return $headers;
    }
    public function updatedSearch()
    {
        $this->resetPage();
    }
    public function updatedSortBy()
    {
        $this->resetPage();
    }
}
