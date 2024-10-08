<?php

namespace App\Livewire\ArchiveBox;

use App\Livewire\User\Index as UserIndex;
use App\Models\ArchiveBox;
use App\Models\ArchiveBoxUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
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
        return ArchiveBox::query()
        ->orderBy('archive_boxes.'.$this->sortBy['column'], $this->sortBy['direction'])
        ->join('archive_box_user', 'archive_boxes.id', '=', 'archive_box_user.archive_box_id')
        ->join('users', 'archive_box_user.user_id', '=', 'users.id')
        ->where('archive_box_user.permission', '=', 3)
        ->select('archive_boxes.*', 'users.name as users_name')
        ->orderBy('archive_box_user.id', 'desc')
        ->groupBy('archive_boxes.id')
        ->when($this->search, function ($query) {
            $query->where(function (Builder $query) {
                $query->where('archive_boxes.name', 'like', '%'.$this->search.'%')->orWhere('archive_boxes.slug', 'like', '%'.$this->search.'%')->orWhere('archive_boxes.description', 'like', '%'.$this->search.'%');
            });
        })->simplePaginate(10);
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
    public function getListeners()
    {
        return [
            "echo:archive-box.index,ArchiveBox\Created" => 'archiveBoxes',
            "echo:archive-box.index,ArchiveBox\Created" => 'notifyNewArchiveBox',
            "echo:archive-box.index,ArchiveBox\Deleted" => 'archiveBoxes',
            "echo:archive-box.index,ArchiveBox\Deleted" => 'notifyDeletedArchiveBox',
            "echo:archive-box.index,ArchiveBox\Updated" => 'archiveBoxes',
            "echo:archive-box.index,ArchiveBox\Updated" => 'notifyUpdatedArchiveBox',
            "echo:archive-box.index,ArchiveBox\User\PermissionChanged" => 'archiveBoxes',
            "echo:archive-box.index,User\Deleted" => 'archiveBoxes',
        ];
    }
}
