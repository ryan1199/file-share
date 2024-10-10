<?php

namespace App\Livewire\User\Log;

use App\Models\User;
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
    public User $user;

    public function render()
    {
        return view('livewire.user.log.index', [
            'logs' => $this->logs(),
        ]);
    }
    public function mount(User $user)
    {
        $this->user = $user;
    }
    public function logs()
    {
        $sort = (bool) $this->sort ? 'asc' : 'desc';
        return $this->user->logs()->with('user')->orderBy('created_at', $sort)->simplePaginate($this->perPage);
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
            "echo:user.show.{$this->user->slug}.log.index,User\Log\Created" => 'logs',
            "echo:user.show.{$this->user->slug}.log.index,User\Log\Created" => 'notifyNewLog',
        ];
    }
}
