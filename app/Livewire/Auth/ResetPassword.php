<?php

namespace App\Livewire\Auth;

use App\Mail\NewPasswordCreated;
use App\Mail\RequestResetPasswordSended;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Mary\Traits\Toast;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;

class ResetPassword extends Component
{
    use Toast;

    #[Validate('required', as: 'email', message: 'Please provide a valid and active email address')]
    #[Validate('email:rfc,dns,spoof,filter,filter_unicode,strict', as: 'email', message: 'Please provide a valid email address')]
    #[Validate('exists:users,email', as: 'email', message: 'We can not find your email address, maybe another email address')]
    public $email;
    #[Locked]
    public $token;

    public function render()
    {
        return view('livewire.auth.reset-password')->title('Reset Password');
    }
    public function mount($token = null)
    {
        $this->token = $token;
    }
    public function requestResetPassword()
    {
        $this->validate();
        $user = User::where('email', $this->email)->first();
        if ($user->email_verified_at == null) {
            $this->error('Email address has not been verified. Please verify your email first.', position: 'toast-bottom', redirectTo: route('auth.email-verification'));
        } else {
            $token = Str::random(100);
            $result = false;
            DB::transaction(function () use ( $user, $token, &$result) {
                $user->update([
                    'token' => $token,
                ]);
                $result = true;
            }, attempts: 100);
            if ($result) {
                Mail::to($user)->send(new RequestResetPasswordSended($user));
                $this->success('Request reset password successful. We send you a confirmation. Check your inbox', position: 'toast-bottom');
            } else {
                $this->error('An error occurred while requesting to reset password. Please try again later.', position: 'toast-bottom', redirectTo: route('auth.reset-password'));
            }
            $this->reset();
        }
    }
    public function cancel()
    {
        $this->reset();
        $this->success('Form cleared.', position: 'toast-bottom');
    }
    public function resetPassword()
    {
        if ($this->token != null) {
            $user = User::where('token', $this->token)->first();
            if ($user != null) {
                $password = Str::random(100);
                $result = false;
                DB::transaction(function () use ( $user, $password, &$result) {
                    $user->update([
                        'password' => Hash::make($password),
                        'token' => null,
                    ]);
                    $result = true;
                }, attempts: 100);
                if ($result) {
                    Mail::to($user)->send(new NewPasswordCreated($user, $password));
                    $this->success('Reset password successful. We send you a new password. Check your inbox', position: 'toast-bottom', timeout: 10000, redirectTo: route('auth.login'));
                } else {
                    $this->error('An error occurred while creating a new password. Please try again later.', position: 'toast-bottom', timeout: 10000, redirectTo: route('auth.reset-password', $this->token));
                }
                $this->reset();
            } else {
                $this->error('Token is invalid or expired. Please try again.', position: 'toast-bottom', redirectTo: route('auth.reset-password'));
            }
        } else {
            $this->error('Token not found. Please request one.', position: 'toast-bottom', redirectTo: route('auth.reset-password'));
        }
    }
}
