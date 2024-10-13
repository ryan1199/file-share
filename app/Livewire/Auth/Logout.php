<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Mary\Traits\Toast;

class Logout extends Component
{
    use Toast;

    public function render()
    {
        return view('livewire.auth.logout');
    }
    public function logout()
    {
        Auth::logout();
 
        session()->invalidate();
    
        session()->regenerateToken();

        $this->success('You are now logged out', position: 'toast-bottom', redirectTo: route('home'));
    }
}
