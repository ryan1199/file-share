<?php

namespace App\Livewire\ArchiveBox;

use App\Models\ArchiveBox;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class Edit extends Component
{
    use Toast, WithFileUploads, WithPagination, WithoutUrlPagination;

    #[Locked]
    public ArchiveBox $archiveBox;
    public $name;
    public $description;
    public $cover;
    public $private = false;
    #[Locked]
    public $changedCover = false;
    public string $searchKeeper = '';
    public string $searchUser = '';
    #[Locked]
    public array $availableSortBy = [
        ['id' => 'name','name' => 'Name',],
        ['id' => 'created_at','name' => 'Created At',],
        ['id' => 'slug','name' => 'Slug',],
    ];
    public $sortByKeeper = 'name';
    public $sortByUser = 'name';
    public bool $ascKeeper = true;
    public bool $ascUser = true;
    
    public function render()
    {
        return view('livewire.archive-box.edit', [
            'keepers' => $this->keepers(),
            'users' => $this->users(),
        ]);
    }
    public function mount(ArchiveBox $archiveBox)
    {
        $this->archiveBox = $archiveBox;
        $this->name = $this->archiveBox->name;
        $this->description = $this->archiveBox->description;
        $this->cover = asset('storage/covers/'.$this->archiveBox->cover);
        $this->private = $this->archiveBox->private;
        $this->keepersId();
    }
    public function updateArchiveBox()
    {
        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'private' => $this->private,
        ];
        $rules = [
            'name' => ['required','min:2','max:30'],
            'description' => ['required','min:3','max:1000'],
            'private' => ['required','boolean'],
        ];
        $messages = [
            'name.required' => 'Please provide an archive box\'s name',
            'name.min' => 'Your archive box\'s name must be at least 2 characters',
            'name.max' => 'Your archive box\'s name must be no more than 30 characters',
            'description.required' => 'Please provide an archive box\'s description',
            'description.min' => 'Your archive box\'s description must be at least 2 characters',
            'description.max' => 'Your archive box\'s description must be no more than 1000 characters',
            'private.required' => 'Please provide an archive box\'s visibility',
            'private.boolean' => 'Check this if you want to make private',
        ];
        $attributes = [
            'name' => 'archive box\'s name',
            'description' => 'archive box\'s description',
            'private' => 'archive box\'s visibility',
        ];
        if ($this->changedCover) {
            $data[] = ['cover' => $this->cover];
            $rules[] = ['cover' => ['required','image','max:10240']];
            $messages[] = [
                'cover.required' => 'Please provide an archive box\'s cover',
                'cover.image' => 'Please provide an image file type',
                'cover.max' => 'Max allowed size is 10 MB',
            ];
            $attributes[] = ['cover' => 'archive box\'s cover'];
        }
        $validated = Validator::make(
            data: $data,
            rules: $rules,
            messages: $messages,
            attributes: $attributes,
        )->validate();
        if ($this->changedCover) {
            $cover_name = $this->cover->hashName();
            $old_cover = $this->archiveBox->cover;
        } else {
            $cover_name = 'https://picsum.photos/500/200';
        }
        $result = false;
        DB::transaction(function () use ($cover_name, &$result) {
            if ($this->changedCover) {
                $this->archiveBox->update([
                    'name' => $this->name,
                    'description' => $this->description,
                    'cover' => $cover_name,
                    'private' => $this->private,
                ]);
            } else {
                $this->archiveBox->update([
                    'name' => $this->name,
                    'description' => $this->description,
                    'private' => $this->private,
                ]);
            }
            $result = true;
        });
        if ($result) {
            if ($this->changedCover) {
                Storage::disk('public')->delete('covers/'.$old_cover);
                $this->cover->storeAs('covers', $cover_name, 'public');
                $this->cover = asset('storage/covers/'.$this->archiveBox->cover);
            }
            $this->name = $this->archiveBox->name;
            $this->description = $this->archiveBox->description;
            $this->private = $this->archiveBox->private;
            $this->success('Archive box updated successfully', position: 'toast-bottom');
        } else {
            $this->error('Failed to update archive box', position: 'toast-bottom');
        }
    }
    public function cancelUpdateArchiveBox()
    {
        $this->name = $this->archiveBox->name;
        $this->description = $this->archiveBox->description;
        $this->private = $this->archiveBox->private;
        $this->cover = asset('storage/covers/'.$this->archiveBox->cover);
        $this->success('Form cleared', position: 'toast-bottom');
    }
    public function keepers()
    {
        return $this->archiveBox->users()->withAggregate('archiveBoxes', 'permission')->when($this->searchKeeper, function ($query) {
            $query->where('name', 'like', '%'.$this->searchKeeper.'%')->orWhere('slug', 'like', '%'.$this->searchKeeper.'%')->orWhere('email', 'like', '%'.$this->searchKeeper.'%');
        })->orderBy($this->sortByKeeper, $this->ascKeeper ? 'asc' : 'desc')->simplePaginate(10, pageName: 'keepers-page');
    }
    public function users()
    {
        return User::whereNotIn('id', $this->keepersId())->when($this->searchUser, function ($query) {
            $query->where('name', 'like', '%'.$this->searchUser.'%')->orWhere('slug', 'like', '%'.$this->searchUser.'%')->orWhere('email', 'like', '%'.$this->searchUser.'%');
        })->orderBy($this->sortByUser, $this->ascUser ? 'asc' : 'desc')->simplePaginate(10, pageName: 'users-page');
    }
    public function updatedSearchKeeper()
    {
        $this->resetPage(pageName: 'keepers-page');
    }
    public function updatedSearchUser()
    {
        $this->resetPage(pageName: 'users-page');
    }
    public function updatedSortByKeeper()
    {
        $this->resetPage(pageName: 'keepers-page');
    }
    public function updatedSortByUser()
    {
        $this->resetPage(pageName: 'users-page');
    }
    public function updateUserPermission($user_id, $permission)
    {
        $result = false;
        DB::transaction(function () use ($user_id, $permission, &$result) {
            $this->archiveBox->users()->where('user_id', $user_id)->update([
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
    public function removeUser($user_id)
    {
        $this->keepersId();
        $result = false;
        DB::transaction(function () use ($user_id, &$result) {
            $this->archiveBox->users()->detach($user_id);
            $result = true;
        }, attempts: 100);
        if ($result) {
            $this->success('User removed from archive box successfully', position: 'toast-bottom');
        } else {
            $this->error('Failed to remove user from archive box', position: 'toast-bottom');
        }
        unset($this->keepersId);
    }
    #[Locked]
    public function keepersId()
    {
        return $this->keepers()->pluck('id');
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
