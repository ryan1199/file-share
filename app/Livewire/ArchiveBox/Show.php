<?php

namespace App\Livewire\ArchiveBox;

use App\Models\ArchiveBox;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Mary\Traits\Toast;

class Show extends Component
{
    use Toast;
    
    #[Locked]
    public ArchiveBox $archiveBox;
    public $showSetting = false;
    public $showUploadFile = false;
    public $showLogs = false;
    
    public function render()
    {
        $name = $this->archiveBox->name;
        $description = $this->archiveBox->description;
        $slug = $this->archiveBox->slug;
        $cover = $this->archiveBox->cover;
        $private = $this->archiveBox->private;
        return view('livewire.archive-box.show', [
            'name' => $name,
            'description' => $description,
            'slug' => $slug,
            'cover' => $cover,
            'private' => $private,
        ]);
    }
    public function mount(ArchiveBox $archiveBox)
    {
        $this->archiveBox = $archiveBox;
        $this->archiveBox->load('users');
    }public function loadUpdatedArchiveBox($event)
    {
        $this->archiveBox = ArchiveBox::find($event['archiveBox']['id']);
        $this->archiveBox->load('users');
    }
    public function notifyUpdatedArchiveBox($event)
    {
        $this->info('Updated archive box: '.$event['archiveBox']['name'], position: 'toast-bottom', timeout: 10000);
    }
    public function getListeners()
    {
        return [
            "echo:archive-box.show.{$this->archiveBox->slug},ArchiveBox\Updated" => 'loadUpdatedArchiveBox',
            "echo:archive-box.show.{$this->archiveBox->slug},ArchiveBox\Updated" => 'notifyUpdatedArchiveBox',
        ];
    }
}
