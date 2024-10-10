<?php

namespace App\Livewire\ArchiveBox\Log;

use App\Models\ArchiveBox;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class Index extends Component
{
    use Toast, WithPagination, WithoutUrlPagination;

    public $sort = false;
    public $perPage = 10;
    #[Locked]
    public ArchiveBox $archiveBox;

    public function render()
    {
        return view('livewire.archive-box.log.index', [
            'logs' => $this->logs(),
        ]);
    }
    public function mount(ArchiveBox $archiveBox)
    {
        $this->archiveBox = $archiveBox;
    }
    public function logs()
    {
        $sort = (bool) $this->sort ? 'asc' : 'desc';
        return $this->archiveBox->logs()->with('user')->orderBy('created_at', $sort)->simplePaginate($this->perPage);
    }
    public function updatedSort()
    {
        $this->sort = (bool) $this->sort ? true : false;
        $this->resetPage();
    }
    public function updatedPerPage()
    {
        $this->perPage = ($this->perPage > 0) && ($this->perPage < 101) ? $this->perPage : 10;
        $this->resetPage();
    }
    public function notifyNewLog($event)
    {
        $this->info('New log', position: 'toast-bottom', timeout: 10000);
    }
    public function getListeners()
    {
        return [
            "echo:archive-box.show.{$this->archiveBox->slug}.log.index,ArchiveBox\Log\Created" => 'logs',
            "echo:archive-box.show.{$this->archiveBox->slug}.log.index,ArchiveBox\Log\Created" => 'notifyNewLog',
        ];
    }
}
