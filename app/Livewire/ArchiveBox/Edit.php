<?php

namespace App\Livewire\ArchiveBox;

use App\Events\ArchiveBox\Deleted;
use App\Events\ArchiveBox\Log\Created;
use App\Events\ArchiveBox\Updated;
use App\Models\ArchiveBox;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

class Edit extends Component
{
    use Toast, WithFileUploads;

    #[Locked]
    public ArchiveBox $archiveBox;
    public $name;
    public $description;
    public $cover;
    public $private = false;
    #[Locked]
    public $changedCover = false;
    
    public function render()
    {
        return view('livewire.archive-box.edit');
    }
    public function mount(ArchiveBox $archiveBox)
    {
        $this->archiveBox = $archiveBox;
        $this->name = $this->archiveBox->name;
        $this->description = $this->archiveBox->description;
        $this->cover = asset('storage/covers/'.$this->archiveBox->cover);
        $this->private = (bool) $this->archiveBox->private;
    }
    public function updateArchiveBox()
    {
        $this->authorize('update', [ArchiveBox::class, $this->archiveBox]);
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
            $old_name = $this->archiveBox->name;
            $old_description = $this->archiveBox->description;
            $old_private = (bool) $this->archiveBox->private;
            $old_cover = $this->archiveBox->cover;
            if ($this->changedCover) {
                $this->archiveBox->update([
                    'name' => $this->name,
                    'description' => $this->description,
                    'cover' => $cover_name,
                    'private' => $this->private,
                ]);
                if ($old_cover != $cover_name) {
                    $this->archiveBox->logs()->create([
                        'user_id' => Auth::id(),
                        'user_name' => Auth::user()->name,
                        'user_slug' => Auth::user()->slug,
                        'message' => 'Cover changed',
                    ]);
                }
            } else {
                $this->archiveBox->update([
                    'name' => $this->name,
                    'description' => $this->description,
                    'private' => $this->private,
                ]);
            }
            if ($old_name != $this->name) {
                $this->archiveBox->logs()->create([
                    'user_id' => Auth::id(),
                    'user_name' => Auth::user()->name,
                    'user_slug' => Auth::user()->slug,
                    'message' => 'Name of archive box changed from '.$old_name.' to '.$this->name,
                ]);
            }
            if ($old_description != $this->description) {
                $this->archiveBox->logs()->create([
                    'user_id' => Auth::id(),
                    'user_name' => Auth::user()->name,
                    'user_slug' => Auth::user()->slug,
                    'message' => 'Description of archive box changed from '.$old_description.' to '.$this->description,
                ]);
            }
            if ($old_private != $this->private) {
                $private = (bool) $this->private ? 'private' : 'public';
                $old_private = (bool) $old_private ? 'private' : 'public';
                $this->archiveBox->logs()->create([
                    'user_id' => Auth::id(),
                    'user_name' => Auth::user()->name,
                    'user_slug' => Auth::user()->slug,
                    'message' => 'Visibility of archive box changed from '.$old_private.' to '.$private,
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
            Updated::dispatch($this->archiveBox, Auth::user());
            Created::dispatch($this->archiveBox);
        } else {
            $this->error('Failed to update archive box', position: 'toast-bottom');
        }
    }
    public function cancelUpdateArchiveBox()
    {
        $this->name = $this->archiveBox->name;
        $this->description = $this->archiveBox->description;
        $this->private = (bool) $this->archiveBox->private;
        $this->cover = asset('storage/covers/'.$this->archiveBox->cover);
        $this->success('Form cleared', position: 'toast-bottom');
    }
    public function deleteArchiveBox()
    {
        $this->authorize('delete', [ArchiveBox::class, $this->archiveBox]);
        $archiveBoxName = $this->archiveBox->name;
        $archiveBoxSlug = $this->archiveBox->slug;
        $archiveBoxCover = $this->archiveBox->cover;
        $archiveBoxUsers = $this->archiveBox->users()->get();
        $result = false;
        DB::transaction(function () use (&$result) {
            foreach ($this->archiveBox->files()->get() as $files) {
                $files->likes()->detach();
            }
            $this->archiveBox->files()->delete();
            $this->archiveBox->users()->detach();
            $this->archiveBox->logs()->delete();
            $this->archiveBox->delete();
            $result = true;
        }, attempts: 100);
        if ($result) {
            Storage::disk('public')->delete('covers/'.$archiveBoxCover);
            Storage::disk('local')->deleteDirectory($archiveBoxSlug);
            $this->success('Archive box deleted successfully', position: 'toast-bottom', timeout: 10000, redirectTo: route('archive-box.index'));
            foreach ($archiveBoxUsers as $user) {
                Deleted::dispatch($archiveBoxName, $user->id);
            }
        } else {
            $this->error('Failed to delete archive box', position: 'toast-bottom', timeout: 10000);
        }
    }
}
