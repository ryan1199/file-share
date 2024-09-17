<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Mary\Traits\Toast;

class Register extends Component
{
    use Toast;

    #[Validate('required', as: 'username', message: 'Please provide a username')]
    #[Validate('min:2', as: 'username', message: 'Your username must be at least 2 characters')]
    #[Validate('max:30', as: 'username', message: 'Your username must be no more than 30 characters')]
    public $name;
    #[Validate('required', as: 'email', message: 'Please provide a valid and active email address')]
    #[Validate('email:rfc,dns,spoof,filter,filter_unicode,strict', as: 'email', message: 'Please provide a valid email address')]
    #[Validate('unique:users,email', as: 'email', message: 'Email address is already registered')]
    public $email;
    #[Validate('required', as: 'password', message: 'Please provide a secure password')]
    #[Validate('min:8', as: 'password', message: 'Your password must be at least 8 characters')]
    #[Validate('confirmed', as: 'password', message: 'Password not confirmed')]
    public $password;
    #[Validate('same:password', as: 'password', message: 'Password do not match')]
    public $password_confirmation;

    public function render()
    {
        return view('livewire.auth.register');
    }
    public function register()
    {
        $this->validate();
        $slug = User::generateSlug();
        $result = false;
        DB::transaction(function () use (&$result, $slug) {
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'slug' => $slug,
            ]);
            $result = true;
        }, 100);
        if ($result) {
            $this->reset();
            $this->success('Registration successful. You can now log in.', position: 'toast-bottom', redirectTo: route('auth.login'));
        } else {
            $this->error('An error occurred while registering. Please try again later.', position: 'toast-bottom');
        }
    }
    public function cancel()
    {
        $this->reset();
        $this->success('Form cleared.', position: 'toast-bottom');
    }
}
