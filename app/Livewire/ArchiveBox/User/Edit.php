<?php

namespace App\Livewire\ArchiveBox\User;

use App\Events\ArchiveBox\Log\Created;
use App\Events\ArchiveBox\User\PermissionChanged;
use App\Events\ArchiveBox\User\Removed;
use App\Models\ArchiveBox;
use App\Models\ArchiveBoxUser;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class Edit extends Component
{
    use Toast, WithPagination, WithoutUrlPagination;

    #[Locked]
    public ArchiveBox $archiveBox;
    public string $search = '';
    #[Locked]
    public array $availableSortBy = [
        ['id' => 'name','name' => 'Name',],
        ['id' => 'created_at','name' => 'Created At',],
        ['id' => 'slug','name' => 'Slug',],
    ];
    public $sortBy = 'name';
    public bool $asc = true;
    public int $perPage = 10;

    public function render()
    {
        return view('livewire.archive-box.user.edit', [
            'users' => $this->users(),
        ]);
    }
    public function mount(ArchiveBox $archiveBox)
    {
        $this->archiveBox = $archiveBox;
    }
    public function users()
    {
        return $this->archiveBox->users()->withAggregate('archiveBoxes', 'permission')->when($this->search, function ($query) {
            $query->where('name', 'like', '%'.$this->search.'%')->orWhere('slug', 'like', '%'.$this->search.'%')->orWhere('email', 'like', '%'.$this->search.'%');
        })->orderBy($this->sortBy, $this->asc ? 'asc' : 'desc')->simplePaginate($this->perPage, pageName: 'update-users-page');
    }
    public function updatedSearch()
    {
        $this->resetPage(pageName: 'update-users-page');
    }
    public function updatedSortBy()
    {
        $this->resetPage(pageName: 'update-users-page');
    }
    public function updatedAsc()
    {
        $this->resetPage(pageName: 'update-users-page');
    }
    public function updatedPerPage()
    {
        $this->resetPage(pageName: 'update-users-page');
    }
    public function updateUserPermission(User $user, $permission)
    {
        $this->authorize('update', [ArchiveBox::class, $this->archiveBox]);
        $result = false;
        $user_permission = ArchiveBoxUser::where('user_id', $user->id)->where('archive_box_id', $this->archiveBox->id)->select('permission')->first();
        DB::transaction(function () use ($user, $permission, &$result, $user_permission) {
            $this->archiveBox->users()->where('user_id', $user->id)->update([
                'permission' => $permission
            ]);
            $promoted = (int) $permission > (int) $user_permission ? ' promoted' : ' demoted';
            $this->archiveBox->logs()->create([
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name,
                'user_slug' => Auth::user()->slug,
                'message' => $user->slug.'/'.$user->name.$promoted.' from permission level '.$user_permission.' to permission level '.$permission,
            ]);
            $result = true;
        }, attempts: 100);
        if ($result) {
            $this->success('User permission updated successfully', position: 'toast-bottom');
            PermissionChanged::dispatch($this->archiveBox, $user);
            Created::dispatch($this->archiveBox);
        } else {
            $this->error('Failed to update user permission', position: 'toast-bottom');
        }
    }
    public function removeUser(User $user)
    {
        $this->authorize('update', [ArchiveBox::class, $this->archiveBox]);
        $result = false;
        DB::transaction(function () use ($user, &$result) {
            $this->archiveBox->users()->detach($user->id);
            $this->archiveBox->logs()->create([
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name,
                'user_slug' => Auth::user()->slug,
                'message' => $user->slug.'/'.$user->name.' removed from archive box',
            ]);
            $result = true;
        }, attempts: 100);
        if ($result) {
            $this->success('User removed from archive box successfully', position: 'toast-bottom');
            Removed::dispatch($this->archiveBox, $user);
            Created::dispatch($this->archiveBox);
        } else {
            $this->error('Failed to remove user from archive box', position: 'toast-bottom');
        }
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
            "echo:archive-box.show.{$this->archiveBox->slug}.user.edit,User\Updated" => 'users',
            "echo:archive-box.show.{$this->archiveBox->slug}.user.edit,User\Updated" => 'notifyUpdatedUser',
            "echo:archive-box.show.{$this->archiveBox->slug}.user.edit,User\Deleted" => 'users',
            "echo:archive-box.show.{$this->archiveBox->slug}.user.edit,User\Deleted" => 'notifyDeletedUser',
        ];
    }
}
