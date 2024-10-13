<?php

namespace App\Livewire\User\ArchiveBox;

use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Mary\Traits\Toast;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class Index extends Component
{
    use Toast, WithPagination, WithoutUrlPagination;

    public string $search = '';
    public array $sortBy = ['column' => 'name', 'direction' => 'asc'];
    public int $perPage = 5;
    #[Locked]
    public User $user;

    public function render()
    {
        return view('livewire.user.archive-box.index', [
            'archiveBoxes' => $this->archiveBoxes(),
        ]);
    }
    public function archiveBoxes()
    {
        return $this->user->archiveBoxes()->when($this->search, function ($query) {
            $query->where('name', 'like', '%'.$this->search.'%')->orWhere('slug', 'like', '%'.$this->search.'%')->orWhere('description', 'like', '%'.$this->search.'%');
        })->orderBy(...array_values($this->sortBy))->simplePaginate($this->perPage);
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
    public function notifyNewArchiveBox($event)
    {
        $this->info('New archive box: '.$event['archiveBox']['name'], position: 'toast-bottom', timeout: 10000);
    }
    public function notifyDeletedArchiveBox($event)
    {
        $this->info('Deleted archive box: '.$event['archiveBoxName'], position: 'toast-bottom', timeout: 10000);
    }
    public function notifyUpdatedArchiveBox($event)
    {
        $this->info('Updated archive box: '.$event['archiveBox']['name'], position: 'toast-bottom', timeout: 10000);
    }
    public function notifyUserAddedToTheArchiveBox($event)
    {
        $this->info($event['user']['name'].' added to the archive box: '.$event['archiveBox']['name'], position: 'toast-bottom', timeout: 10000);
    }
    public function notifyUserRemovedFromTheArchiveBox($event)
    {
        $this->info($event['user']['name'].' removed from the archive box: '.$event['archiveBox']['name'], position: 'toast-bottom', timeout: 10000);
    }
    public function mount(User $user)
    {
        $this->user = $user;
    }
    public function getListeners()
    {
        return [
            "echo:user.show.{$this->user->slug}.archive-box.index,ArchiveBox\Created" => 'archiveBoxes',
            "echo:user.show.{$this->user->slug}.archive-box.index,ArchiveBox\Created" => 'notifyNewArchiveBox',
            "echo:user.show.{$this->user->slug}.archive-box.index,ArchiveBox\Deleted" => 'archiveBoxes',
            "echo:user.show.{$this->user->slug}.archive-box.index,ArchiveBox\Deleted" => 'notifyDeletedArchiveBox',
            "echo:user.show.{$this->user->slug}.archive-box.index,ArchiveBox\Updated" => 'archiveBoxes',
            "echo:user.show.{$this->user->slug}.archive-box.index,ArchiveBox\Updated" => 'notifyUpdatedArchiveBox',
            "echo:user.show.{$this->user->slug}.archive-box.index,ArchiveBox\User\Added" => 'archiveBoxes',
            "echo:user.show.{$this->user->slug}.archive-box.index,ArchiveBox\User\Added" => 'notifyUserAddedToTheArchiveBox',
            "echo:user.show.{$this->user->slug}.archive-box.index,ArchiveBox\User\Removed" => 'archiveBoxes',
            "echo:user.show.{$this->user->slug}.archive-box.index,ArchiveBox\User\Removed" => 'notifyUserRemovedFromTheArchiveBox',
        ];
    }
}
