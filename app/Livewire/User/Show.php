<?php

namespace App\Livewire\User;

use App\Models\User;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Mary\Traits\Toast;

class Show extends Component
{
    use Toast;

    #[Locked]
    public User $user;
    public $showSetting = false;
    public $showCreateArchiveBox = false;
    public $showLogs = false;

    public function render()
    {
        $name = $this->user->name;
        $email = $this->user->email;
        $slug = $this->user->slug;
        $avatar = $this->user->avatar;
        $dob = $this->user->profile->date_of_birth;
        $links = ($this->user->profile->links != null) ? explode(" ", $this->user->profile->links) : null;
        $status = $this->user->profile->status;
        return view('livewire.user.show', [
            'name' => $name,
            'email' => $email,
            'slug' => $slug,
            'avatar' => $avatar,
            'dob' => $dob,
            'links' => $links,
            'status' => $status,
        ])->title($this->user->name);
    }
    public function mount(User $user)
    {
        $this->user = $user;
        $this->user->load('profile');
    }
    public function loadUpdatedUser($event)
    {
        $this->user = User::find($event['user']['id']);
        $this->user->load('profile');
    }
    public function notifyUpdatedUser($event)
    {
        $this->info($event['user']['name'].' updates some datas', position: 'toast-bottom', timeout: 10000);
    }
    public function getListeners()
    {
        return [
            "echo:user.show.{$this->user->slug},User\Updated" => 'loadUpdatedUser',
            "echo:user.show.{$this->user->slug},User\Updated" => 'notifyUpdatedUser',
        ];
    }
}
