<?php

namespace App\Livewire\ArchiveBox;

use App\Livewire\User\Index as UserIndex;
use App\Models\ArchiveBox;
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

    public function render()
    {
        return view('livewire.archive-box.index', [
            'archiveBoxes' => $this->archiveBoxes(),
        ]);
    }
    public function archiveBoxes()
    {
        return ArchiveBox::withAggregate('users', 'name')->when($this->search, function ($query) {
            $query->where('name', 'like', '%'.$this->search.'%')->orWhere('slug', 'like', '%'.$this->search.'%')->orWhere('description', 'like', '%'.$this->search.'%');
        })->orderBy(...array_values($this->sortBy))->simplePaginate(10);
    }
    #[Computed(cache: true)]
    public function headers(): array
    {
        $columns = Schema::getColumnListing('archive_boxes');
        $headers = [];
        foreach ($columns as $column) {
            if (in_array($column, ['name', 'slug', 'description', 'private'])) {
                if ($column == 'private') {
                    $headers[] = ['key' => $column, 'label' => 'Visibility', 'class' => 'w-20 text-base-content'];
                } else {
                    $headers[] = ['key' => $column, 'label' => ucfirst(str_replace('_','', $column)), 'class' => 'w-16 text-base-content'];
                }
            }
        }
        $headers[] = ['key' => 'users_name', 'label' => 'Owner', 'class' => 'w-20 text-base-content'];
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
    public function seeUserByName($name)
    {
        $this->success('Search '.$name, position: 'toast-bottom', redirectTo: route('user.index', $name));
    }
}
