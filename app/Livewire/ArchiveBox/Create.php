<?php

namespace App\Livewire\ArchiveBox;

use App\Events\ArchiveBox\Created;
use App\Models\ArchiveBox;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

class Create extends Component
{
    use Toast, WithFileUploads;

    #[Validate('required', as: 'archive box\'s name', message: 'Please provide an archive box\'s name')]
    #[Validate('min:2', as: 'archive box\'s name', message: 'Your archive box\'s name must be at least 2 characters')]
    #[Validate('max:30', as: 'archive box\'s name', message: 'Your archive box\'s name must be no more than 30 characters')]
    public $name;
    #[Validate('required', as: 'archive box\'s description', message: 'Please provide an archive box\'s description')]
    #[Validate('min:3', as: 'archive box\'s description', message: 'Your archive box\'s description must be at least 2 characters')]
    #[Validate('max:1000', as: 'archive box\'s description', message: 'Your archive box\'s description must be no more than 1000 characters')]
    public $description;
    #[Validate('required', as: 'archive box\'s cover', message: 'Please provide an archive box\'s cover')]
    #[Validate('image', as: 'archive box\'s cover', message: 'Please provide an image file type')]
    #[Validate('max:10240', as: 'archive box\'s cover', message: 'Max allowed size is 10 MB')]
    public $cover;
    #[Validate('required', as: 'archive box\'s visibility', message: 'Please provide an archive box\'s visibility')]
    #[Validate('boolean', as: 'archive box\'s visibility', message: 'Check this if you want to make private')]
    public $private = false;

    public function render()
    {
        return view('livewire.archive-box.create');
    }
    #[Computed(cache: true)]
    public function user()
    {
        return User::find(Auth::id());
    }
    public function store()
    {
        $this->validate();
        $slug = ArchiveBox::generateSlug();
        $result = false;
        $cover_name = $this->cover->hashName();
        $archiveBox = DB::transaction(function () use ($slug, $cover_name, &$result) {
            $archiveBox = ArchiveBox::create([
                'name' => $this->name,
                'slug' => $slug,
                'description' => $this->description,
                'cover' => $cover_name,
                'private' => $this->private,
            ]);
            $archiveBox->users()->attach($this->user()->id, ['permission' => 3]);
            $result = true;
            return $archiveBox;
        }, attempts: 100);
        if ($result) {
            $this->cover->storeAs('covers', $cover_name, 'public');
            $this->reset();
            $this->success('Archive box created successfully.', position: 'toast-bottom', redirectTo: route('archive-box.show', $archiveBox->slug));
            Created::dispatch($archiveBox, $this->user());
        } else {
            $this->error('Failed to create archive box.', position: 'toast-bottom');
        }
    }
    public function cancel()
    {
        $this->reset();
        $this->success('Form cleared.', position: 'toast-bottom');
    }
}
