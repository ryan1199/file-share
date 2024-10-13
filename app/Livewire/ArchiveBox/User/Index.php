<?php

namespace App\Livewire\ArchiveBox\User;

use App\Events\ArchiveBox\Log\Created;
use App\Events\ArchiveBox\User\Removed;
use App\Events\User\Log\Created as LogCreated;
use App\Models\ArchiveBox;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
    public int $perPage = 10;
    #[Locked]
    public ArchiveBox $archiveBox;
    public $showUpdateUser = false;
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
        })->orderBy(...array_values($this->sortBy))->simplePaginate($this->perPage);
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
    public function updatedPerPage()
    {
        $this->resetPage();
    }
    public function quitFromArchiveBox()
    {
        $this->authorize('quitFromArchiveBox', [ArchiveBox::class, $this->archiveBox]);
        $result = false;
        $user = User::find(Auth::id());
        DB::transaction(function () use (&$result, $user) {
            $this->archiveBox->users()->detach($user->id);
            $this->archiveBox->logs()->create([
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_slug' => $user->slug,
                'message' => $user->slug.'/'.$user->name.' quitted from archive box',
            ]);
            $user->logs()->create([
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_slug' => $user->slug,
                'message' => 'You quitted from archive box '.$this->archiveBox->slug.'/'.$this->archiveBox->name,
            ]);
            $result = true;
        }, attempts: 100);
        if ($result) {
            $this->success('You quitted from archive box successfully', position: 'toast-bottom', redirectTo: route('archive-box.index'));
            Removed::dispatch($this->archiveBox, $user);
            Created::dispatch($this->archiveBox);
            LogCreated::dispatch($user);
        } else {
            $this->error('Failed to quit from archive box', position: 'toast-bottom');
        }
    }
    public function notifyUserAddedToTheArchiveBox($event)
    {
        $this->info('User added: '.$event['user']['name'], position: 'toast-bottom', timeout: 10000);
    }
    public function notifyUserRemovedFromTheArchiveBox($event)
    {
        $this->info('User removed: '.$event['user']['name'], position: 'toast-bottom', timeout: 10000);
    }
    public function notifyPermissionOfUserChanged($event)
    {
        $this->info('Permission of user changed: '.$event['user']['name'], position: 'toast-bottom', timeout: 10000);
    }
    public function notifyUpdatedUser($event)
    {
        $this->info('Updated user: '.$event['user']['name'], position: 'toast-bottom', timeout: 10000);
    }
    public function notifyDeletedUser($event)
    {
        $this->info('Deleted user: '.$event['userName'], position: 'toast-bottom', timeout: 10000);
    }
    public function getListeners()
    {
        return [
            "echo:archive-box.show.{$this->archiveBox->slug}.user.index,ArchiveBox\User\Added" => 'users',
            "echo:archive-box.show.{$this->archiveBox->slug}.user.index,ArchiveBox\User\Added" => 'notifyUserAddedToTheArchiveBox',
            "echo:archive-box.show.{$this->archiveBox->slug}.user.index,ArchiveBox\User\PermissionChanged" => 'users',
            "echo:archive-box.show.{$this->archiveBox->slug}.user.index,ArchiveBox\User\PermissionChanged" => 'notifyPermissionOfUserChanged',
            "echo:archive-box.show.{$this->archiveBox->slug}.user.index,ArchiveBox\User\Removed" => 'users',
            "echo:archive-box.show.{$this->archiveBox->slug}.user.index,ArchiveBox\User\Removed" => 'notifyUserRemovedFromTheArchiveBox',
            "echo:archive-box.show.{$this->archiveBox->slug}.user.index,User\Updated" => 'users',
            "echo:archive-box.show.{$this->archiveBox->slug}.user.index,User\Updated" => 'notifyUpdatedUser',
            "echo:archive-box.show.{$this->archiveBox->slug}.user.index,User\Deleted" => 'users',
            "echo:archive-box.show.{$this->archiveBox->slug}.user.index,User\Deleted" => 'notifyDeletedUser',
        ];
    }
}
