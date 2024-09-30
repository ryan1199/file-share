<?php

namespace App\Livewire\ArchiveBox\User;

use App\Models\ArchiveBox;
use App\Models\User;
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
        DB::transaction(function () use ($user, $permission, &$result) {
            $this->archiveBox->users()->where('user_id', $user->id)->update([
                'permission' => $permission
            ]);
            $result = true;
        }, attempts: 100);
        if ($result) {
            $this->success('User permission updated successfully', position: 'toast-bottom');
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
            $result = true;
        }, attempts: 100);
        if ($result) {
            $this->success('User removed from archive box successfully', position: 'toast-bottom');
        } else {
            $this->error('Failed to remove user from archive box', position: 'toast-bottom');
        }
    }
}
