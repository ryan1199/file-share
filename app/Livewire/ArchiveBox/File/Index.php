<?php

namespace App\Livewire\ArchiveBox\File;

use App\Models\ArchiveBox;
use App\Models\File;
use Illuminate\Support\Facades\Auth;
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
    public $showEditFile = false;

    public function render()
    {
        return view('livewire.archive-box.file.index', [
            'files' => $this->files(),
        ]);
    }
    public function mount(ArchiveBox $archiveBox)
    {
        $this->archiveBox = $archiveBox;
        $this->archiveBox->load('users');
    }
    public function files()
    {
        return $this->archiveBox->files()->when($this->search, function ($query) {
            $query->where('name', 'like', '%'.$this->search.'%')->orWhere('slug', 'like', '%'.$this->search.'%')->orWhere('description', 'like', '%'.$this->search.'%')->orWhere('extension', 'like', '%'.$this->search.'%');
        })->orderBy(...array_values($this->sortBy))->simplePaginate(10);
    }
    #[Computed(cache: true)]
    public function headers(): array
    {
        $columns = Schema::getColumnListing('files');
        $headers = [];
        foreach ($columns as $column) {
            if (in_array($column, ['name', 'description', 'slug', 'extension', 'size'])) {
                if ($column == 'slug') {
                    $headers[] = ['key' => $column, 'label' => ucfirst(str_replace('_','', $column)), 'class' => 'table-cell text-base-content'];
                    $headers[] = ['key' => 'actions', 'label' => 'Actions', 'class' => 'table-cell text-base-content', 'sortable' => false];
                } else {
                    $headers[] = ['key' => $column, 'label' => ucfirst(str_replace('_','', $column)), 'class' => 'table-cell text-base-content'];
                }
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
    public function editFile(File $file)
    {
        $this->authorize('update', [File::class, $file, $this->archiveBox]);
        $this->showEditFile = true;
        $this->dispatch('file.edit', $file)->to(Edit::class);
    }
    public function deleteFile(File $file)
    {
        $this->authorize('delete', [File::class, $file, $this->archiveBox]);
        $this->dispatch('file.delete', $file)->to(Edit::class);
    }
}
