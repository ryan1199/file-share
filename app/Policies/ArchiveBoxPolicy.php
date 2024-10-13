<?php

namespace App\Policies;

use App\Models\ArchiveBox;
use App\Models\ArchiveBoxUser;
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

    public function updateUserPermission(User $user, ArchiveBox $archiveBox, User $model): Response
    {
        $archiveBox->load('users');
        $permission_of_target = ArchiveBoxUser::where('user_id', $model->id)->where('archive_box_id', $archiveBox->id)->first()->permission;
        if ($permission_of_target != 3) {
            return $archiveBox->users->where('pivot.permission', 3)->contains($user->id) ? Response::allow() : Response::deny('You do not have required permission');
        } else {
            return Response::deny('Cannot update permission of a user with permission level 3');
        }
    }

    public function removeUser(User $user, ArchiveBox $archiveBox, User $model): Response
    {
        $archiveBox->load('users');
        $permission_of_target = ArchiveBoxUser::where('user_id', $model->id)->where('archive_box_id', $archiveBox->id)->first()->permission;
        if ($permission_of_target != 3) {
            return $archiveBox->users->where('pivot.permission', 3)->contains($user->id) ? Response::allow() : Response::deny('You do not have required permission');
        } else {
            return Response::deny('Cannot update permission of a user with permission level 3');
        }
    }

    public function quitFromArchiveBox(User $user, ArchiveBox $archiveBox): Response
    {
        $userPartOfArchiveBox = ArchiveBoxUser::where('user_id', $user->id)->where('archive_box_id', $archiveBox->id)->first();
        if ($userPartOfArchiveBox != null) {
            return Response::allow();
        } else {
            return Response::deny('You are not part of '. $archiveBox->name);
        }
    }
}
