<?php

namespace App\Livewire\User;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

class Edit extends Component
{
    use Toast, WithFileUploads;

    #[Locked]
    public User $user;
    #[Locked]
    public Profile $profile;
    #[Validate('required', as: 'username', message: 'Please provide a username')]
    #[Validate('min:2', as: 'username', message: 'Your username must be at least 2 characters')]
    #[Validate('max:30', as: 'username', message: 'Your username must be no more than 30 characters')]
    public $name;
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
    #[Validate('required', as: 'date of birth', message: 'Please provide a date of birth')]
    #[Validate('date_format:Y-m-d', as: 'date of birth', message: 'Please provide a valid date format (YYYY-MM-DD)')]
    public $dob;
    #[Validate(rule: 'required', attribute: 'links', as: 'links', message: 'Please provide at least one valid link')]
    #[Validate(rule: 'url:http,https', attribute: 'links.*', as: 'link', message: 'Please provide some valid links')]
    public array $links = [];
    #[Validate('required', as: 'status', message: 'Please provide a status')]
    #[Validate('max:100', as: 'status', message: 'Maximum length of status is 100 characters')]
    public $status;
    #[Locked]
    public $changedAvatar = false;
    public function render()
    {
        return view('livewire.user.edit');
    }
    public function mount(User $user)
    {
        $this->user = $user;
        $this->profile = Profile::firstOrCreate(
            ['user_id' => $user->id],
            [
                'date_of_birth' => now()
            ]
        );
        $this->name = $this->user->name;
        $this->avatar = asset('storage/avatars/'.$this->user->avatar);
        $this->dob = $this->profile->date_of_birth;
        $this->links = explode(" ", $this->profile->links) ?? array();
        $this->status = $this->profile->status;
    }
    public function updateProfile()
    {
        $data = [
            'name' => $this->name,
            'dob' => $this->dob,
            'links' => $this->links,
            'status' => $this->status,
        ];
        $rules = [
            'name' => ['required','min:2','max:30'],
            'dob' => ['required','date_format:Y-m-d'],
            'links' => ['required','min:1'],
            'links.*' => ['url:http,https'],
            'status' => ['required','max:100'],
        ];
        $messages = [
            'name.required' => 'Please provide a name',
            'name.min' => 'Your name must be at least 2 characters',
            'name.max' => 'Your name must be no more than 30 characters',
            'dob.required' => 'Please provide a date of birth',
            'dob.date_format' => 'Please provide a valid date format (Y-m-d)',
            'links.required' => 'Please provide at least one valid link',
            'links.*.url' => 'Please provide some valid links',
            'status.required' => 'Please provide a status',
            'status.max' => 'Maximum length of status is 100 characters',
        ];
        $attributes = [
            'name' => 'name',
            'dob' => 'date of Birth',
            'links' => 'links',
            'links.*' => 'link',
            'status' => 'status',
        ];
        if ($this->changedAvatar) {
            $data[] = ['avatar' => $this->avatar];
            $rules[] = ['avatar' => ['required','image','max:10240']];
            $messages[] = [
                'avatar.required' => 'Please provide an avatar',
                'avatar.image' => 'Please provide an image file type',
                'avatar.max' => 'Max allowed size is 10 MB',
            ];
            $attributes[] = ['avatar' => 'avatar'];
        }
        $validated = Validator::make(
            data: $data,
            rules: $rules,
            messages: $messages,
            attributes: $attributes
        )->validate();
        if ($this->changedAvatar) {
            $avatar_name = $this->avatar->hashName();
            $old_avatar = $this->user->avatar;
        } else {
            $avatar_name = 'default-avatar-white.svg';
        }
        $result = false;
        DB::transaction(function () use ($avatar_name, &$result) {
            if ($this->changedAvatar) {
                $this->user->update([
                    'name' => $this->name,
                    'avatar' => $avatar_name,
                ]);
            } else {
                $this->user->update([
                    'name' => $this->name,
                ]);
            }
            $this->profile->update([
                'date_of_birth' => $this->dob,
                'links' => implode(" ", $this->links),
                'status' => $this->status,
            ]);
            $result = true;
        });
        if ($result) {
            if ($this->changedAvatar) {
                Storage::delete($old_avatar);
                $this->avatar->storeAs('avatars', $avatar_name, 'public');
                $this->avatar = asset('storage/avatars/'.$this->user->avatar);
            }
            $this->name = $this->user->name;
            $this->dob = $this->profile->date_of_birth;
            $this->links = explode(" ", $this->profile->links) ?? array();
            $this->status = $this->profile->status;
            $this->success('Profile updated successfully.', position: 'toast-bottom');
        } else {
            $this->error('Failed to update profile.', position: 'toast-bottom');
        }
    }
    public function cancelUpdateProfile()
    {
        $this->name = $this->user->name;
        $this->avatar = asset('storage/avatars/'.$this->user->avatar);
        $this->dob = $this->profile->date_of_birth;
        $this->links = explode(" ", $this->profile->links) ?? array();
        $this->status = $this->profile->status;
        $this->changedAvatar = false;
        $this->success('Form cleared.', position: 'toast-bottom');
    }
    public function updatePassword()
    {
        $validated = Validator::make(
            data: [
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
            ],
            rules: [
                'password' => ['required','min:8','max:100','confirmed'],
                'password_confirmation' => ['required','same:password'],
            ],
            messages: [
                'password.required' => 'Please provide a secure password',
                'password.min' => 'Your password must be at least 8 characters',
                'password.max' => 'Your password must be no more than 100 characters',
                'password.confirmed' => 'Password not confirmed',
                'password_confirmation.required' => 'Please confirm your password',
                'password_confirmation.same' => 'Password do not match',
            ],
            attributes: [
                'password' => 'password',
                'password_confirmation' => 'password confirmation',
            ]
        )->validate();
        $result = false;
        DB::transaction(function () use (&$result) {
            $this->user->update([
                'password' => Hash::make($this->password),
            ]);
            $result = true;
        }, attempts: 100);
        if ($result) {
            $this->success('Password updated successfully.', position: 'toast-bottom');
            $this->reset('password', 'password_confirmation');
        } else {
            $this->error('Failed to update password.', position: 'toast-bottom');
        }
    }
    public function cancelUpdatePassword()
    {
        $this->reset('password', 'password_confirmation');
        $this->success('Form cleared.', position: 'toast-bottom');
    }
    public function updated()
    {
        $this->resetValidation();
    }
    public function updatedAvatar()
    {
        $this->changedAvatar = true;
    }
}
