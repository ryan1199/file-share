<?php

namespace App\Policies;

use App\Models\ArchiveBox;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ArchiveBoxPolicy
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
    public function view(User $user, ArchiveBox $archiveBox): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ArchiveBox $archiveBox): Response
    {
        $archiveBox->load('users');
        return $archiveBox->users->where('pivot.permission', 3)->contains($user->id) ? Response::allow() : Response::deny('You do not have required permission');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ArchiveBox $archiveBox): Response
    {
        $archiveBox->load('users');
        return $archiveBox->users->where('pivot.permission', 3)->contains($user->id) ? Response::allow() : Response::deny('You do not have required permission');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ArchiveBox $archiveBox): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ArchiveBox $archiveBox): bool
    {
        //
    }
}
