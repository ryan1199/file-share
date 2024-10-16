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
    public array $sortBy = ['column' => 'name', 'direction' => 'asc'];
    public int $perPage = 10;

    public function render()
    {
        return view('livewire.user.index', [
            'users' => $this->users(),
        ])->title('Users');
    }
    public function users()
    {
        return User::with('profile')->when($this->search, function ($query) {
            $query->where('name', 'like', '%'.$this->search.'%')->orWhere('slug', 'like', '%'.$this->search.'%')->orWhere('email', 'like', '%'.$this->search.'%');
        })->orderBy(...array_values($this->sortBy))->select(['id', 'name', 'slug', 'email', 'avatar'])->simplePaginate($this->perPage);
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
    public function updatedPerPage()
    {
        $this->resetPage();
    }
    public function mount($search = '')
    {
        $this->search = $search;
    }
    public function notifyNewUser($event)
    {
        $this->info('New user: '.$event['user']['name'], position: 'toast-bottom', timeout: 10000);
    }
    public function notifyDeletedUser($event)
    {
        $this->info('Deleted user: '.$event['userName'], position: 'toast-bottom', timeout: 10000);
    }
    public function notifyUpdatedUser($event)
    {
        $this->info('Updated user: '.$event['user']['name'], position: 'toast-bottom', timeout: 10000);
    }
    public function getListeners()
    {
        return [
            "echo:user.index,User\Created" => 'users',
            "echo:user.index,User\Created" => 'notifyNewUser',
            "echo:user.index,User\Deleted" => 'users',
            "echo:user.index,User\Deleted" => 'notifyDeletedUser',
            "echo:user.index,User\Updated" => 'users',
            "echo:user.index,User\Updated" => 'notifyUpdatedUser',
        ];
    }
}
