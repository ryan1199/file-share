<?php

namespace App\Livewire\User;

use App\Models\Profile;
use App\Models\User;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Mary\Traits\Toast;

class Show extends Component
{
    use Toast;

    #[Locked]
    public User $user;
    public Profile $profile;
    #[Locked]
    public $showSetting = false;
    public $showCreateArchiveBox = false;

    public function render()
    {
        $name = $this->user->name;
        $email = $this->user->email;
        $slug = $this->user->slug;
        $avatar = $this->user->avatar;
        $dob = $this->user->profile->date_of_birth;
        $links = explode(" ", $this->user->profile->links);
        $status = $this->user->profile->status;
        return view('livewire.user.show', [
            'name' => $name,
            'email' => $email,
            'slug' => $slug,
            'avatar' => $avatar,
            'dob' => $dob,
            'links' => $links,
            'status' => $status,
        ]);
    }
    public function mount(User $user)
    {
        $this->user = $user;
        $this->user->load('profile');
    }
}
