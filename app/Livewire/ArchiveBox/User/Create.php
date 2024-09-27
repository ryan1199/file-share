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

class Create extends Component
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
        return view('livewire.archive-box.user.create', [
            'users' => $this->users(),
        ]);
    }
    public function mount(ArchiveBox $archiveBox)
    {
        $this->archiveBox = $archiveBox;
    }
    public function users()
    {
        return User::whereNotIn('id', $this->archiveBox->idsOfJoinedUsers())->when($this->search, function ($query) {
            $query->where('name', 'like', '%'.$this->search.'%')->orWhere('slug', 'like', '%'.$this->search.'%')->orWhere('email', 'like', '%'.$this->search.'%');
        })->orderBy($this->sortBy, $this->asc ? 'asc' : 'desc')->simplePaginate($this->perPage, pageName: 'new-users-page');
    }
    public function updatedSearch()
    {
        $this->resetPage(pageName: 'new-users-page');
    }
    public function updatedSortBy()
    {
        $this->resetPage(pageName: 'new-users-page');
    }
    public function updatedAsc()
    {
        $this->resetPage(pageName: 'new-users-page');
    }
    public function updatedPerPage()
    {
        $this->resetPage(pageName: 'new-users-page');
    }
    public function newUser($user_id, $permission)
    {
        $result = false;
        DB::transaction(function () use ($user_id, $permission, &$result) {
            $this->archiveBox->users()->attach($user_id, ['permission' => $permission]);
            $result = true;
        }, attempts: 100);
        if ($result) {
            $this->success('User added to archive box successfully', position: 'toast-bottom');
        } else {
            $this->error('Failed to add user to archive box', position: 'toast-bottom');
        }
    }
}
