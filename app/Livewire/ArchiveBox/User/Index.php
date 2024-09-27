<?php

namespace App\Livewire\ArchiveBox\User;

use App\Models\ArchiveBox;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class Index extends Component
{
    use Toast, WithPagination, WithoutUrlPagination;

    public string $search = '';
    public array $sortBy = ['column' => 'name', 'direction' => 'asc'];
    #[Locked]
    public ArchiveBox $archiveBox;
    #[Locked]
    public $showUpdateUser = false;
    #[Locked]
    public $showNewUser = false;

    public function render()
    {
        return view('livewire.archive-box.user.index', [
            'users' => $this->users(),
        ]);
    }
    public function users()
    {
        return $this->archiveBox->users()->withAggregate('archiveBoxes', 'permission')->when($this->search, function ($query) {
            $query->where('name', 'like', '%'.$this->search.'%')->orWhere('slug', 'like', '%'.$this->search.'%')->orWhere('email', 'like', '%'.$this->search.'%');
        })->orderBy(...array_values($this->sortBy))->simplePaginate(10);
    }
    #[Computed(cache: true)]
    public function headers(): array
    {
        $columns = Schema::getColumnListing('users');
        $headers = [];
        foreach ($columns as $column) {
            if (in_array($column, ['name', 'email', 'slug'])) {
                $headers[] = ['key' => $column, 'label' => ucfirst(str_replace('_','', $column)), 'class' => 'table-cell text-base-content'];
            }
        }
        $headers[] = ['key' => 'archive_boxes_permission', 'label' => 'Permission', 'class' => 'table-cell text-base-content'];
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
