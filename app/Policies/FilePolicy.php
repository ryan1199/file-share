<?php

namespace App\Policies;

use App\Models\ArchiveBox;
use App\Models\File;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FilePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, File $file): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, ArchiveBox $archiveBox): Response
    {
        $archiveBox->load('users');
        return $archiveBox->users->whereIn('pivot.permission', [2,3])->contains($user->id) ? Response::allow() : Response::deny('You do not have required permission');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, File $file, ArchiveBox $archiveBox): Response
    {
        $archiveBox->load('users');
        return $file->archive_box_id == $archiveBox->id && $archiveBox->users->whereIn('pivot.permission', [2,3])->contains($user->id) ? Response::allow() : Response::deny('You do not have required permission');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, File $file, ArchiveBox $archiveBox): Response
    {
        $archiveBox->load('users');
        return $file->archive_box_id == $archiveBox->id && $archiveBox->users->where('pivot.permission', 3)->contains($user->id) ? Response::allow() : Response::deny('You do not have required permission');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, File $file): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, File $file): bool
    {
        //
    }
}
