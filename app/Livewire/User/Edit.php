<?php

namespace App\Livewire\User;

use App\Events\ArchiveBox\Deleted as ArchiveBoxDeleted;
use App\Events\ArchiveBox\User\PermissionChanged;
use App\Events\User\Deleted;
use App\Events\User\Updated;
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
    public $name;
    public $password;
    public $password_confirmation;
    public $avatar;
    public $dob;
    public array $links = [];
    public $status;
    #[Locked]
    public $changedAvatar = false;
    public $selectedTab = 'profile';
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
        $this->links = ($this->profile->links != null) ? explode(" ", $this->profile->links) : ['https://example.com'];
        $this->status = $this->profile->status;
    }
    public function updateProfile()
    {
        $this->authorize('update', [Profile::class, $this->profile]);
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
                if ($old_avatar != 'default-avatar-white.svg') {
                    Storage::disk('public')->delete('avatars/'.$old_avatar);
                }
                $this->avatar->storeAs('avatars', $avatar_name, 'public');
                $this->avatar = asset('storage/avatars/'.$this->user->avatar);
            }
            $this->name = $this->user->name;
            $this->dob = $this->profile->date_of_birth;
            $this->links = explode(" ", $this->profile->links) ?? array();
            $this->status = $this->profile->status;
            $this->reset('changedAvatar');
            $this->success('Profile updated successfully.', position: 'toast-bottom');
            Updated::dispatch($this->user);
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
        $this->authorize('update', [User::class, $this->user]);
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
    public function deleteAccount()
    {
        $this->authorize('delete', [User::class, $this->user]);
        $user_name = $this->user->name;
        $user_avatar = $this->user->avatar;
        $user_archive_boxes = $this->user->archiveBoxes()->get();
        $archive_boxes = [];
        $deleted_archive_boxes = [];
        $changed_permission_of_user_in_archive_box = [];
        $result = false;
        DB::transaction(function () use (&$result, $user_archive_boxes, &$archive_boxes, &$deleted_archive_boxes, &$changed_permission_of_user_in_archive_box) {
            foreach ($user_archive_boxes as $archive_box) {
                $archive_box->load('users');
                $archive_box->load('files');
                if (count($archive_box->users) > 1) {
                    $userIdsWithPermissionLevel3 = [];
                    $userIdsWithPermissionLevel2 = [];
                    $userIdsWithPermissionLevel1 = [];
                    $archive_boxes[] = $archive_box->id;
                    foreach ($archive_box->users as $user) {
                        if ($user->id != $this->user->id) {
                            if ($user->pivot->permission == 3) {
                                $userIdsWithPermissionLevel3[] = $user->id;
                            } elseif ($user->pivot->permission == 2) {
                                $userIdsWithPermissionLevel2[] = $user->id;
                            } else {
                                $userIdsWithPermissionLevel1[] = $user->id;
                            }
                        }
                    }
                    if (count($userIdsWithPermissionLevel3) == 0) {
                        if (count($userIdsWithPermissionLevel2) > 0) {
                            $selectedUserId = $userIdsWithPermissionLevel2[rand(0, count($userIdsWithPermissionLevel2)-1)];
                            $archive_box->users()->updateExistingPivot($selectedUserId, ['permission' => 3]);
                            $changed_permission_of_user_in_archive_box[] = [
                                'archive_box' => $archive_box,
                                'user' => User::find($selectedUserId),
                            ];
                        } else {
                            $selectedUserId = $userIdsWithPermissionLevel1[rand(0, count($userIdsWithPermissionLevel1)-1)];
                            $archive_box->users()->updateExistingPivot($selectedUserId, ['permission' => 3]);
                            $changed_permission_of_user_in_archive_box[] = [
                                'archive_box' => $archive_box,
                                'user' => User::find($selectedUserId),
                            ];
                        }
                    }
                } else {
                    $deleted_archive_boxes[] = [
                        'archive_box_name' => $archive_box->name,
                        'archive_box_slug' => $archive_box->slug,
                        'archive_box_cover' => $archive_box->cover,
                    ];
                    foreach ($archive_box->files as $file) {
                        $file->likes()->detach();
                    }
                    $archive_box->files()->delete();
                    $archive_box->users()->detach();
                    $archive_box->delete();
                }
            }
            $this->user->likes()->detach();
            $this->user->archiveBoxes()->detach();
            $this->user->profile()->delete();
            $this->user->delete();
            $result = true;
        }, attempts: 100);
        if ($result) {
            if ($user_avatar != 'default-avatar-white.svg') {
                Storage::disk('public')->delete('avatars/'.$user_avatar);
            }
            foreach ($changed_permission_of_user_in_archive_box as $permission_change) {
                PermissionChanged::dispatch($permission_change['archive_box'], $permission_change['user'], true);
            }
            foreach ($deleted_archive_boxes as $archive_box) {
                Storage::disk('public')->delete('covers/'.$archive_box['archive_box_cover']);
                Storage::disk('local')->deleteDirectory($archive_box['archive_box_slug']);
                ArchiveBoxDeleted::dispatch($archive_box['archive_box_name']);
            }
            if (count($archive_boxes) > 0) {
                foreach ($archive_boxes as $archive_box) {
                    Deleted::dispatch($user_name, $archive_box);
                }
            } else {
                Deleted::dispatch($user_name);
            }
            $this->success('Account deleted successfully.', position: 'toast-bottom', timeout: 10000, redirectTo: route('auth.logout'));
        } else {
            $this->error('Failed to delete account.', position: 'toast-bottom', timeout: 10000);
        }
    }
}
