<?php

namespace App\Livewire\Auth;

use App\Events\User\Created;
use App\Mail\RequestEmailVerificationSended;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;
use Illuminate\Support\Str;

class Register extends Component
{
    use Toast, WithFileUploads;

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
    #[Validate('max:100', as: 'password', message: 'Your password must be no more than 100 characters')]
    #[Validate('confirmed', as: 'password', message: 'Password not confirmed')]
    public $password;
    #[Validate('same:password', as: 'password', message: 'Password do not match')]
    public $password_confirmation;
    #[Validate('required', as: 'avatar', message: 'Please provide an avatar')]
    #[Validate('image', as: 'avatar', message: 'Please provide an image file type')]
    #[Validate('max:10240', as: 'avatar', message: 'Max allowed size is 10 MB')]
    public $avatar;

    public function render()
    {
        return view('livewire.auth.register');
    }
    public function register()
    {
        $this->validate();
        $slug = User::generateSlug();
        $result = false;
        $avatar_name = $this->avatar->hashName();
        $token = Str::random(100);
        $user = DB::transaction(function () use (&$result, $slug, $avatar_name, $token) {
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'slug' => $slug,
                'avatar' => $avatar_name,
                'token' => $token,
            ]);
            $user->profile()->create([
                'date_of_birth' => now()
            ]);
            $result = true;
            return $user;
        }, attempts: 100);
        if ($result) {
            $this->avatar->storeAs('avatars', $avatar_name, 'public');
            Mail::to($user)->send(new RequestEmailVerificationSended($user));
            $this->reset();
            $this->success('Registration successful. You need to verify your email address. Check your inbox', position: 'toast-bottom', redirectTo: route('archive-box.index'));
            Created::dispatch($user);
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
