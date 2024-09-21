<?php

namespace App\Livewire\Auth;

use App\Mail\RequestResetPasswordSended;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Mary\Traits\Toast;
use Illuminate\Support\Str;

class ResetPassword extends Component
{
    use Toast;

    #[Validate('required', as: 'email', message: 'Please provide a valid and active email address')]
    #[Validate('email:rfc,dns,spoof,filter,filter_unicode,strict', as: 'email', message: 'Please provide a valid email address')]
    #[Validate('exists:users,email', as: 'email', message: 'We can not find your email address, maybe another email address')]
    public $email;

    public function render()
    {
        return view('livewire.auth.reset-password');
    }
    public function resetPassword()
    {
        $this->validate();
        $user = User::where('email', $this->email)->first();
        if ($user->email_verified_at == null) {
            $this->error('Email address has not been verified. Please verify your email first.', position: 'toast-bottom', redirectTo: route('auth.email-verification'));
        } else {
            $password = Str::random(100);
            $result = false;
            DB::transaction(function () use ( $user, $password, &$result) {
                $user->update([
                    'password' => Hash::make($password),
                ]);
                $result = true;
            }, attempts: 100);
            if ($result) {
                Mail::to($user)->send(new RequestResetPasswordSended($user, $password));
                $this->success('Reset password successful. We send you new password. Check your inbox', position: 'toast-bottom');
            } else {
                $this->error('An error occurred while resetting password. Please try again later.', position: 'toast-bottom', redirectTo: route('auth.reset-password'));
            }
            $this->reset();
        }
    }
    public function cancel()
    {
        $this->reset();
        $this->success('Form cleared.', position: 'toast-bottom');
    }
}
