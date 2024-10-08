<?php

namespace App\Livewire\Auth;

use App\Mail\RequestEmailVerificationSended;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Mary\Traits\Toast;
use Illuminate\Support\Str;

class EmailVerification extends Component
{
    use Toast;

    #[Locked]
    public $token;
    #[Validate('required', as: 'email', message: 'Please provide a valid and active email address')]
    #[Validate('email:rfc,dns,spoof,filter,filter_unicode,strict', as: 'email', message: 'Please provide a valid email address')]
    #[Validate('exists:users,email', as: 'email', message: 'We can not find your email address, maybe another email address')]
    public $email;
    #[Validate('required', as: 'password', message: 'Please provide a secure password')]
    #[Validate('min:8', as: 'password', message: 'Your password must be at least 8 characters')]
    public $password;

    public function render()
    {
        return view('livewire.auth.email-verification');
    }
    public function mount($token = null)
    {
        $this->token = $token;
    }
    public function sendEmailVerification()
    {
        $this->validate();
        $user = User::where('email', $this->email)->first();
        $validCredentials = Hash::check($this->password, $user->password);
        if ($user == null) {
            $this->error('Account not found.', position: 'toast-bottom');
        } elseif ($user != null && !$validCredentials) {
            $this->error('Invalid credentials. Please try again later.', position: 'toast-bottom', timeout: 10000);
        } elseif ($user != null && $validCredentials && $user->email_verified_at != null) {
            $this->error('Email has already been verified.', position: 'toast-bottom', redirectTo: route('archive-box.index'));
        } else {
            $token = Str::random(100);
            $result = false;
            DB::transaction(function () use ($token, $user, &$result) {
                $user->update([
                    'token' => $token,
                ]);
                $result = true;
            }, attempts: 10);
            if ($result) {
                Mail::to($user)->send(new RequestEmailVerificationSended($user));
                $this->success('Verification email sent successfully. Check your inbox.', position: 'toast-bottom');
            } else {
                $this->error('Failed to send verification email. Please try again later.', position: 'toast-bottom');
            }
        }
        $this->reset();
    }
    public function verifyEmail()
    {
        if ($this->token == null) {
            $this->error('Token not found. Please request one.', position: 'toast-bottom', redirectTo: route('auth.email-verification'));
        } else {
            $this->validate();
            $user = User::where('email', $this->email)->where('token', $this->token)->first();
            $validCredentials = Hash::check($this->password, $user->password);
            if ($user == null) {
                $this->token = null;
                $this->error('Invalid verification token. Please request the new one.', position: 'toast-bottom', redirectTo: route('auth.email-verification'), timeout: 10000);
            } elseif ($user != null && !$validCredentials) {
                $this->error('Invalid credentials. Please try again later.', position: 'toast-bottom', timeout: 10000);
            } else {
                if ($user->email_verified_at != null) {
                    $this->error('Email has already been verified.', position: 'toast-bottom', redirectTo: route('archive-box.index'));
                } else {
                    $result = false;
                    DB::transaction(function () use ($user, &$result) {
                        $user->update([
                            'email_verified_at' => now(),
                            'token' => null,
                        ]);
                        $result = true;
                    }, attempts: 10);
                    if ($result) {
                        $this->success('Email verification successful. You can now log in.', position: 'toast-bottom', redirectTo: route('auth.login'));
                    } else {
                        $this->error('Failed to verify email. Please try again later.', position: 'toast-bottom', redirectTo: route('auth.email-verification', $this->token));
                    }
                }
            }
            $this->reset();
        }
    }
    public function cancel()
    {
        if ($this->token == null) {
            $this->reset();
        } else {
            $this->resetExcept('token');
        }
        $this->success('Form cleared.', position: 'toast-bottom');
    }
}
