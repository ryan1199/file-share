<?php

namespace App\Livewire\ArchiveBox\File;

use App\Models\ArchiveBox;
use App\Models\File;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;
use Illuminate\Support\Str;

class Create extends Component
{
    use Toast, WithFileUploads;

    #[Locked]
    public ArchiveBox $archiveBox;
    #[Validate('required', as: 'file\'s name', message: 'Please provide a file\'s name')]
    #[Validate('min:2', as: 'file\'s name', message: 'Your file\'s name must be at least 2 characters')]
    #[Validate('max:30', as: 'file\'s name', message: 'Your file\'s name must be no more than 30 characters')]
    public $name;
    #[Validate('required', as: 'file\'s description', message: 'Please provide a file\'s description')]
    #[Validate('min:3', as: 'file\'s description', message: 'Your file\'s description must be at least 2 characters')]
    #[Validate('max:1000', as: 'file\'s description', message: 'Your file\'s description must be no more than 1000 characters')]
    public $description;
    #[Validate('required', as: 'file', message: 'Please provide a file')]
    #[Validate('file', as: 'file', message: 'The provided must be a file')]
    #[Validate('max:30720', as: 'file', message: 'Max allowed size is 30 MB')]
    public $file;

    public function render()
    {
        return view('livewire.archive-box.file.create');
    }
    public function mount(ArchiveBox $archiveBox)
    {
        $this->archiveBox = $archiveBox;
    }
    public function store()
    {
        $this->authorize('create', [File::class, $this->archiveBox]);
        $this->validate();
        $slug = File::generateSlug();
        $file_size = $this->file->getSize();
        $file_extension = $this->file->getClientOriginalExtension();
        $file_name = Str::random(100).'.'.$file_extension;
        $result = false;
        DB::transaction(function () use ($slug, $file_name, $file_size, $file_extension, &$result) {
            $this->archiveBox->files()->create([
                'name' => $this->name,
                'description' => $this->description,
                'slug' => $slug,
                'path' => $file_name,
                'extension' => $file_extension,
                'size' => $file_size,
            ]);
            $result = true;
        });
        if ($result) {
            $this->file->storeAs($this->archiveBox->slug, $file_name, 'local');
            $this->resetExcept('archiveBox');
            $this->success('File uploaded successfully', 'success');
        } else {
            $this->error('Failed to upload file', 'error');
        }
    }
    public function cancel()
    {
        $this->resetExcept('archiveBox');
        $this->success('Form cleared.', position: 'toast-bottom');
    }
    public function updatedFile()
    {
        $this->name = $this->file->getClientOriginalName();
    }
    public function updated()
    {
        $this->resetValidation();
    }
}
