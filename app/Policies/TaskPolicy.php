<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update or delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function deleteOrUpdate(User $user, Task $task)
    {
        if (($user->role == "Admin" || $user->role == "Manager")
            || ($user->role == "Leader" && $task->created_by == $user->id)) {
            return true;
        }

        return false;
    }

}
