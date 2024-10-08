<?php

namespace App\Livewire\ArchiveBox\File;

use App\Events\File\Deleted;
use App\Events\File\Updated;
use App\Models\ArchiveBox;
use App\Models\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

class Edit extends Component
{
    use Toast, WithFileUploads;

    #[Locked]
    public ArchiveBox $archiveBox;
    #[Locked]
    public File $file;
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
    public $uploadFile;
    #[Locked]
    public $changedUploadFile = false;

    public function render()
    {
        return view('livewire.archive-box.file.edit');
    }
    public function mount(ArchiveBox $archiveBox)
    {
        $this->archiveBox = $archiveBox;
    }
    #[On('file.edit')]
    public function editFile(File $file)
    {
        $this->file = $file;
        $this->name = $this->file->name;
        $this->description = $this->file->description;
    }
    public function updatedUploadFile()
    {
        $this->changedUploadFile = true;
    }
    public function updated()
    {
        $this->resetValidation();
    }
    public function cancel()
    {
        $this->name = $this->file->name;
        $this->description = $this->file->description;
        $this->reset('uploadFile');
        $this->changedUploadFile = false;
        $this->success('Form cleared.', position: 'toast-bottom');
    }
    public function update()
    {
        $this->authorize('update', [File::class, $this->file, $this->archiveBox]);
        $data = [
            'name' => $this->name,
            'description' => $this->description,
        ];
        $rules = [
            'name' => ['required','min:2','max:30'],
            'description' => ['required','min:3','max:1000'],
        ];
        $messages = [
            'name.required' => 'Please provide a file\'s name',
            'name.min' => 'Your file\'s name must be at least 2 characters',
            'name.max' => 'Your file\'s name must be no more than 30 characters',
            'description.required' => 'Please provide a file\'s description',
            'description.min' => 'Your file\'s description must be at least 2 characters',
            'description.max' => 'Your file\'s description must be no more than 1000 characters',
        ];
        $attributes = [
            'name' => 'file\'s name',
            'description' => 'file\'s description',
        ];
        if ($this->changedUploadFile) {
            $data[] = ['uploadFile' => $this->uploadFile];
            $rules[] = ['uploadFile' => ['required','file','max:30720']];
            $messages[] = [
                'uploadFile.required' => 'Please provide a new file',
                'uploadFile.file' => 'The provided must be a file',
                'uploadFile.max' => 'Max allowed size is 30 MB',
            ];
            $attributes[] = ['uploadFile' => 'file'];
        }
        $validated = Validator::make(
            data: $data,
            rules: $rules,
            messages: $messages,
            attributes: $attributes
        )->validate();
        if ($this->changedUploadFile) {
            $uploadFileName = $this->uploadFile->hashName();
            $uploadFileSize = $this->uploadFile->getSize();
            $uploadFileExtension = $this->uploadFile->extension();
            $oldFile = $this->file->path;
        } else {
            $uploadFileName = $this->file->path;
            $uploadFileSize = $this->file->size;
            $uploadFileExtension = $this->file->extension;
        }
        $result = false;
        DB::transaction(function () use ($uploadFileName, $uploadFileExtension, $uploadFileSize, &$result) {
            if ($this->changedUploadFile) {
                $this->file->update([
                    'name' => $this->name,
                    'description' => $this->description,
                    'path' => $uploadFileName,
                    'extension' => $uploadFileExtension,
                    'size' => $uploadFileSize,
                ]);
            } else {
                $this->file->update([
                    'name' => $this->name,
                    'description' => $this->description,
                ]);
            }
            $result = true;
        }, attempts: 100);
        if ($result) {
            if ($this->changedUploadFile) {
                Storage::delete($this->archiveBox->slug.'/'.$oldFile);
                $this->uploadFile->storeAs($this->archiveBox->slug, $uploadFileName, 'local');
            }
            $this->reset('changedUploadFile');
            $this->success('File updated successfully', position: 'toast-bottom');
            Updated::dispatch($this->archiveBox, $this->file);
        } else {
            $this->error('Failed to update file', position: 'toast-bottom');
        }
    }
    #[On('file.delete')]
    public function deleteFile(File $file)
    {
        $this->authorize('delete', [File::class, $file, $this->archiveBox]);
        $result = false;
        $filePath = $file->path;
        $fileName = $file->name;
        DB::transaction(function () use ($file, &$result) {
            $file->likes()->detach();
            $file->delete();
            $result = true;
        }, attempts: 100);
        if ($result) {
            Storage::delete($this->archiveBox->slug.'/'.$filePath);
            $this->success('File deleted successfully', position: 'toast-bottom');
            Deleted::dispatch($this->archiveBox, $fileName);
        } else {
            $this->error('Failed to delete file', position: 'toast-bottom');
        }
    }
}
