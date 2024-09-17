<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Mary\Traits\Toast;

class Login extends Component
{
    use Toast;

    #[Validate('required', as: 'email', message: 'Please provide a valid and active email address')]
    #[Validate('email:rfc,dns,spoof,filter,filter_unicode,strict', as: 'email', message: 'Please provide a valid email address')]
    public $email;
    #[Validate('required', as: 'password', message: 'Please provide a secure password')]
    #[Validate('min:8', as: 'password', message: 'Your password must be at least 8 characters')]
    public $password;
    #[Validate('boolean', as: 'remember me', message: 'Check this so we remember you')]
    public $rememberMe = false;

    public function render()
    {
        return view('livewire.auth.login');
    }
    public function login()
    {
        $this->validate();
        $user = Auth::attempt([
            'email' => $this->email,
            'password' => $this->password
        ], $this->rememberMe);
        if ($user) {
            $this->reset();
            $this->success('Login successful, Hello ' . Auth::user()->name, position: 'toast-bottom', redirectTo: route('welcome'));
        } else {
            $this->error('Invalid credentials. Please try again.', position: 'toast-bottom');
        }
    }
    public function cancel()
    {
        $this->reset();
        $this->success('Form cleared.', position: 'toast-bottom');
    }
}
