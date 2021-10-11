<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, User $model)
    {
        if (($user->role === "Admin" || $user->role === "Manager")
            || ($user->role === "Leader" && $user->id === $model->id)
            || ($user->role === "Staff" && $user->id === $model->id)) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, User $model)
    {
        if (($user->role === "Admin" || $user->role === "Manager")
            || ($user->role === "Staff" && $user->id === $model->id)) {
            return true;
        }
        return false;
    }

}
